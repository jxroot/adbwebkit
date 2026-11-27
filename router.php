<?php
// Router for `php -S 0.0.0.0:8080 router.php` in the adbwebkit root.
// Sends the short /s/<sid>/<verb> routes to the Link gate, /ping to a probe,
// everything else to a real file or the operator UI.
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($uri === '/ping') { header('Content-Type: text/plain'); echo 'adbwebkit ' . gethostname(); return true; }
if (preg_match('#^/s/([0-9a-f]{8,64})/agent/(apk\.json|report)$#', $uri, $m)) {
    $_GET['s'] = $m[1]; $_GET['a'] = $m[2] === 'report' ? 'agentreport' : 'agentapk';
    require __DIR__ . '/link/gate.php';
    return true;
}
if (preg_match('#^/s/([0-9a-f]{8,64})/(enroll|state|apk|pair|attach)$#', $uri, $m)) {
    $_GET['s'] = $m[1]; $_GET['a'] = $m[2];
    require __DIR__ . '/link/gate.php';
    return true;
}
if (preg_match('#^/s/([^/]+)/?$#', $uri, $m)) {
    $_GET['s'] = $m[1]; $_GET['a'] = 'view';
    require __DIR__ . '/link/gate.php';
    return true;
}
if ($uri === '/') { require __DIR__ . '/index.php'; return true; }
if (is_file(__DIR__ . $uri)) return false;     // let the built-in server serve it
require __DIR__ . '/index.php';                 // fallback: the operator UI
return true;
