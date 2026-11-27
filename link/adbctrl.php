<?php
// ADB control layer. Wraps the adb binary and, crucially, guards concurrent
// `adb connect` calls with a file lock -- two tabs, a beacon and a pair wire
// can otherwise fight over the same adb server and report false failures.
//
// Wireless-debug pairing needs ADB_VENDOR_KEYS on the console: adb only
// auto-authorises a connect() when it owns a key the phone was told to trust
// during pair(). Without it every reconnect shows the on-device popup.

require_once __DIR__ . '/config.php';

function adb_bin(): string { return link_cfg()['adb']; }
function adb_default_port(): int { return 5555; }

function adb_run(array $args, int $timeout = 25): array {
    $argv = array_merge([adb_bin()], $args);
    return link_exec($argv, $timeout);   // [rc, out, ms]
}

function adb_ensure_keys(): string {
    $env = (string)getenv('ADB_VENDOR_KEYS');
    if ($env !== '' && is_dir($env)) return $env;
    $key = link_dir() . '/adbkey';
    $keys = link_dir() . '/adbkeys';
    if (!is_file($key)) { adb_run(['keygen', $key]); }
    if (!is_dir($keys)) @mkdir($keys, 0777, true);
    @copy($key, $keys . '/adbkey'); @copy($key . '.pub', $keys . '/adbkey.pub');
    putenv('ADB_VENDOR_KEYS=' . $keys);
    return $keys;
}

// serial is the adb-visible "ip:port"; callers own how they build it.
function adb_serial(string $ip, int $port): string {
    $ip = preg_replace('/[^0-9a-fA-F:.]/', '', $ip);
    return $ip === '' ? '' : ($ip . ':' . $port);
}

function adb_devices(): array {
    [$rc, $out] = adb_run(['devices', '-l'], 15);
    $map = [];
    foreach (explode("\n", $out) as $line) {
        if (!preg_match('/^(\S+)\s+(\S+)(.*)$/', trim($line), $m)) continue;
        if ($m[1] === 'List') continue;
        $map[$m[1]] = ['state'=>$m[2], 'info'=>trim($m[3] ?? '')];
    }
    return $map;
}
function adb_present_count(): int {
    $n = 0; foreach (adb_devices() as $s) if ($s['state'] !== 'offline') $n++;
    return $n;
}

function adb_device_present(string $serial, ?string $port = null, string $token = ''): bool {
    if ($serial === '') return false;
    $d = adb_devices();
    return isset($d[$serial]) && in_array($d[$serial]['state'], ['device','unauthorized'], true);
}

// Connect, under the merge guard. Bounded: a dead host must not block.
function adb_connect(string $ip, int $port, string $token = '', int $timeout = 12): array {
    $serial = adb_serial($ip, $port);
    if ($serial === '') return ['ok'=>false,'detail'=>'no address'];
    adb_ensure_keys();
    return adb_with_guard(function () use ($serial, $timeout) {
        [$rc, $out] = adb_run(['connect', $serial], $timeout);
        $ok = strpos($out, 'connected') !== false || strpos($out, 'already') !== false;
        return ['ok'=>$ok, 'detail'=>trim($out)];
    });
}

function adb_pair(string $ip, int $port, string $code): array {
    adb_ensure_keys();
    return adb_with_guard(function () use ($ip, $port, $code) {
        [$rc, $out] = adb_run(['pair', adb_serial($ip, $port), $code], 40);
        return ['ok'=>strpos($out, 'Successfully paired') !== false || $rc === 0, 'detail'=>trim($out)];
    });
}

// The connect port after pairing. `adb mdns services` prints it; fall back to
// asking the phone, then to a small scan on the one host we already reached.
function adb_find_wifi_port(string $ip): int {
    [$rc, $out] = adb_run(['mdns', 'services'], 8);
    foreach (explode("\n", $out) as $line) {
        if (strpos($line, $ip) === false) continue;
        if (!preg_match('/(\d{2,5})\s*$/', trim($line), $m)) continue;
        if (strpos($line, '_adb-tls-connect') !== false || strpos($line, '_adb-tls-pairing') !== false) {
            $p = (int)$m[1];
            if ($p > 1024) { $at = ['_adb-tls-connect'=>0,'_adb-tls-pairing'=>0];
                // prefer the connect service
            }
        }
    }
    // simplest reliable path: ask adb which services exist and take a connect one
    foreach (explode("\n", $out) as $line) {
        if (strpos($line, $ip) === false || strpos($line, '_adb-tls-connect') === false) continue;
        if (preg_match('/(\d{2,5})\s*$/', trim($line), $m)) return (int)$m[1];
    }
    return 0;
}

// Merge guard: one lock app-wide around adb mutations, plus a device table the
// arm step seeds with a preferred (fixed) port so reconnects are predictable.
function adb_with_guard(callable $fn) {
    $lock = link_file('adb.lock');
    @mkdir(link_dir(), 0777, true);
    $fh = @fopen($lock, 'c');
    if ($fh) { @flock($fh, LOCK_EX); }
    try { return $fn(); }
    finally { if ($fh) { @flock($fh, LOCK_UN); @fclose($fh); } }
}

function adb_kill_others(string $keepSerial): void {
    foreach (adb_devices() as $s => $info) {
        if ($s === $keepSerial) continue;
        if (strpos($s, ':') === false) continue;         // leave USB alone
        if ($info['state'] === 'offline') adb_run(['disconnect', $s], 8);
    }
}

// Launch the agent once after install. Best-effort: a launcher-less build would
// fail this and only matter at the next reboot, so the exit code is ignored.
function adb_launch(string $verb, string $pkg): bool {
    [$rc, $out] = adb_run(['-s', $verb, 'shell', 'monkey', '-p', $pkg, '-c',
        'android.intent.category.LAUNCHER', '1'], 20);
    return strpos($out, 'No activities') === false;
}
