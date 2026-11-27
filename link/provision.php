<?php
// Provision a link out-of-band, in its own process, so the beacon request that
// triggers it returns immediately. Bounded adb calls mean it always finishes.
//
//   php link/provision.php <sid>
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/adbctrl.php';

$sid = link_sid((string)($argv[1] ?? ''));
if ($sid === '') { fwrite(STDERR, "no sid\n"); exit(1); }
$dir = link_dir_for($sid);
$state = link_json_read($dir . '/state.json');
$ip    = (string)($state['ip'] ?? '');
if ($ip === '') { exit(0); }
$port  = (int)($state['port'] ?? 0);

// Already bridged? A serial that carries this ip means we are done.
foreach (array_keys(adb_devices()) as $serial) {
    if (str_starts_with($serial, $ip . ':') || $serial === $ip) {
        $state['adb'] = 'connected'; $state['updated'] = time();
        link_json_write($dir . '/state.json', $state); exit(0);
    }
}

// Only try to connect if something is plausibly listening. Bounded so a beacon
// never waits more than a few seconds.
$p = $port ?: adb_default_port();
$r = adb_connect($ip, $p, '', 6);

$present = false;
foreach (array_keys(adb_devices()) as $serial) {
    if (str_starts_with($serial, $ip . ':') || $serial === $ip) { $present = true; break; }
}
$state['port']    = $present ? $p : ($state['port'] ?? 0);
$state['adb']     = $present ? 'connected' : 'unpaired';
$state['detail']  = $r['detail'] ?? '';
$state['updated'] = time();
link_json_write($dir . '/state.json', $state);
