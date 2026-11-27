<?php
// Link subsystem: configuration and small helpers.
// Shared state lives in linkdata/ so the PHP server owns nothing in memory.

function link_dir(): string   { return __DIR__ . '/../linkdata'; }
function link_file(string $n): string { return link_dir() . '/' . $n; }

function link_cfg(): array {
    static $c = null;
    if ($c !== null) return $c;
    $c = [
        'public_url'    => rtrim((string)getenv('ADBWEBKIT_URL'), '/'),
        'adb'           => trim((string)(getenv('ADBWEBKIT_ADB') ?: 'adb')),
        'serial'        => trim((string)getenv('ADBWEBKIT_SERIAL')),
        'ntfy_topic'    => trim((string)(getenv('ADBWEBKIT_NTFY') ?: 'cg-console-a7f3c9d21b4e')),
        'cloudflared'   => trim((string)(getenv('ADBWEBKIT_CLOUDFLARED') ?: 'cloudflared')),
        'local_port'    => (int)(getenv('ADBWEBKIT_PORT') ?: 8080),
    ];
    return $c;
}

// Atomic-ish JSON write: temp file then rename, so a reader never sees half.
function link_json_write(string $path, $data): void {
    $tmp = $path . '.tmp.' . getmypid();
    file_put_contents($tmp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    @rename($tmp, $path);
}
function link_json_read(string $path): array {
    if (!is_file($path)) return [];
    $d = json_decode((string)file_get_contents($path), true);
    return is_array($d) ? $d : [];
}

function link_sid(string $sid): string {
    return preg_match('/^[0-9a-f]{8,64}$/', $sid) ? $sid : '';
}
function link_dir_for(string $sid): string { return link_dir() . "/s/" . basename($sid); }
function link_new_sid(): string { return bin2hex(random_bytes(16)); }

// The one place both ends agree on a device's identity: enrolment id.
function link_enroll(string $sid): string {
    $f = link_dir_for($sid) . '/enroll.json';
    return (string)(link_json_read($f)['enroll'] ?? '');
}
function link_device_id(string $enroll): string {
    return substr(sha1('adbe:' . $enroll), 0, 8);
}

function link_client_ip(): string {
    $fwd = explode(',', (string)($_SERVER['HTTP_X_FORWARDED_FOR'] ?? ''));
    foreach ($fwd as $f) { $f = trim($f); if ($f !== '' && filter_var($f, FILTER_VALIDATE_IP)) return $f; }
    return (string)($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
}

// Runs a command with a REAL timeout (exec()'s callers can block forever on a
// hung `adb connect`, and on php -S one blocked request freezes the console).
// proc_open + a deadline, killing the child at the deadline.
function link_exec(array $argv, int $timeout = 25): array {
    $cmd = implode(' ', array_map('escapeshellarg', $argv));
    $desc = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
    $pipes = [];
    $p = @proc_open($cmd, $desc, $pipes);
    if (!is_resource($p)) return [127, 'proc_open failed', 0];
    foreach ($pipes as $h) stream_set_blocking($h, false);
    $buf = ''; $t0 = microtime(true); $timedOut = false;
    while (true) {
        $buf .= (string)stream_get_contents($pipes[1]);
        $buf .= (string)stream_get_contents($pipes[2]);
        $st = proc_get_status($p);
        if (!$st || !$st['running']) break;
        if (microtime(true) - $t0 >= $timeout) { proc_terminate($p, 9); $timedOut = true; break; }
        usleep(40000);
    }
    $buf .= (string)stream_get_contents($pipes[1]);
    $buf .= (string)stream_get_contents($pipes[2]);
    fclose($pipes[1]); fclose($pipes[2]);
    $rc = proc_close($p);
    if ($timedOut) { $rc = 124; $buf .= "\n(timed out after {$timeout}s)"; }
    return [$rc, trim($buf), (int)((microtime(true) - $t0) * 1000)];
}

// Records that a device touched this link: first-seen IP and a capped hit log.
function link_touch(string $sid, string $ip, string $did = '', string $enroll = ''): void {
    $dir = link_dir_for($sid);
    if (!is_dir($dir)) @mkdir($dir, 0777, true);
    $s = link_json_read($dir . '/state.json');
    $s['first_seen'] = $s['first_seen'] ?? ['ip' => $ip, 'at' => time()];
    $s['last_seen']  = ['ip' => $ip, 'at' => time()];
    if ($did !== '') $s['device'] = $did;
    if ($enroll !== '') $s['enroll'] = $enroll;
    $s['hits'] = array_slice(array_merge($s['hits'] ?? [], [['ip'=>$ip,'at'=>time()]]), -50);
    link_json_write($dir . '/state.json', $s);
}

function link_send(int $code, $body, string $type = 'application/json'): void {
    http_response_code($code);
    header('Content-Type: ' . $type);
    header('Cache-Control: no-store');
    header('Access-Control-Allow-Origin: *');   // the link page runs on our origin anyway
    echo is_string($body) ? $body : json_encode($body, JSON_UNESCAPED_SLASHES);
}
