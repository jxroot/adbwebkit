<?php
// SSRF guard for the "Open / Redirect / Tunnel" feature.
// A phone on carrier NAT has a 100.64/10 address; that is exactly a range a
// naive guard either allows wrongly or blocks wrongly. So: judge by RANGE,
// with an explicit allow-list switch, and resolve hostnames over DoH so a
// public name that points inward is still judged by its real address.

function ssrf_blocked_ranges(): array {
    return [
        '0.0.0.0/8','10.0.0.0/8','100.64.0.0/10','127.0.0.0/8','169.254.0.0/16',
        '172.16.0.0/12','192.0.0.0/24','192.168.0.0/16','198.18.0.0/15',
        '198.51.100.0/24','203.0.113.0/24','224.0.0.0/4','240.0.0.0/4',
    ];
}
function ssrf_blocked_v6(): array { return ['::1/128','fe80::/10','fc00::/7','::/128']; }

function ip_in_cidr(string $ip, string $cidr): bool {
    [$net, $bits] = array_pad(explode('/', $cidr, 2), 2, '32');
    $bits = (int)$bits;
    if (strpos($ip, ':') !== false) {
        $a = inet_pton($ip); $b = inet_pton($net);
        if ($a === false || $b === false) return false;
        $len = $bits; $bytes = intdiv($len, 8); $rem = $len % 8;
        if (substr($a, 0, $bytes) !== substr($b, 0, $bytes)) return false;
        if ($rem && (ord($a[$bytes]) >> (8 - $rem)) !== (ord($b[$bytes]) >> (8 - $rem))) return false;
        return true;
    }
    $a = ip2long($ip); $b = ip2long($net);
    if ($a === false || $b === false) return false;
    $mask = $bits === 0 ? 0 : ((-1 << (32 - $bits)) & 0xFFFFFFFF);
    return (($a & $mask) === ($b & $mask));
}
function ssrf_is_blocked(string $ip): bool {
    foreach (ssrf_blocked_ranges() as $c) if (ip_in_cidr($ip, $c)) return true;
    if (strpos($ip, ':') !== false) foreach (ssrf_blocked_v6() as $c) if (ip_in_cidr($ip, $c)) return true;
    return false;
}

function ssrf_doh(string $host): array {
    $url = 'https://cloudflare-dns.com/dns-query?name=' . urlencode($host) . '&type=A';
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 8,
        CURLOPT_HTTPHEADER => ['Accept: application/dns-json'],
    ]);
    $body = curl_exec($ch);

    if (!is_string($body)) return [];
    $d = json_decode($body, true);
    $ips = [];
    foreach (($d['Answer'] ?? []) as $a) {
        if (($a['type'] ?? 0) === 1 && filter_var($a['data'] ?? '', FILTER_VALIDATE_IP)) $ips[] = $a['data'];
    }
    return $ips;
}
function ssrf_resolve(string $host): array {
    if (filter_var($host, FILTER_VALIDATE_IP)) return [$host];
    $ips = ssrf_doh($host);
    if (!$ips) { $loc = @gethostbynamel($host); if (is_array($loc)) $ips = $loc; }
    return $ips;
}

// Returns ['ok'=>bool,'detail'=>str,'ip'=>str,'source'=>str,'allow'=>bool]
function ssrf_check(string $raw): array {
    $u = trim($raw);
    if ($u === '') return ['ok'=>false,'detail'=>'empty'];
    if (!preg_match('#^[a-z][a-z0-9+.\-]*://#i', $u)) $u = 'http://' . $u;
    $p = parse_url($u);
    $host = strtolower((string)($p['host'] ?? ''));
    if ($host === '') return ['ok'=>false,'detail'=>'no host in ' . $raw];
    $allowPrivate = (bool)getenv('ADBWEBKIT_ALLOW_PRIVATE');

    // 1) this console's own public address = our own tailscale/tunnel host
    $self = trim((string)getenv('ADBWEBKIT_URL'));
    $selfHost = $self ? strtolower((string)parse_url($self, PHP_URL_HOST)) : '';

    $ips = ssrf_resolve($host);
    if (!$ips) return ['ok'=>false,'detail'=>'unresolvable: ' . $host, 'source'=>'dns'];

    $public = []; $private = [];
    foreach ($ips as $ip) { if (ssrf_is_blocked($ip)) { $private[] = $ip; } else { $public[] = $ip; } }

    if ($host === $selfHost) return ['ok'=>true,'detail'=>'this console','ip'=>$ips[0],'source'=>'public','allow'=>$allowPrivate];
    if ($public) return ['ok'=>true,'detail'=>'public host','ip'=>$public[0],'source'=>'public','allow'=>$allowPrivate];
    // resolved only to a private/link range
    return [
        'ok' => $allowPrivate,
        'detail' => $allowPrivate ? 'private host (allow-list on)' : 'private host ' . $private[0] . ' -- enable Allow private',
        'ip' => $private[0], 'source'=>'private', 'allow'=>$allowPrivate,
    ];
}

// Fetch through the guard, with redirects followed but each hop re-checked.
function ssrf_fetch(string $raw, int $maxBytes = 65536, int $maxHops = 4): array {
    $url = trim($raw); $hops = 0; $chain = [];
    while ($hops++ <= $maxHops) {
        $c = ssrf_check($url);
        if (!$c['ok']) return ['ok'=>false,'detail'=>$c['detail'],'chain'=>$chain];
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 20, CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_NOBODY => true, CURLOPT_HEADER => true,
            CURLOPT_USERAGENT => 'adbwebkit/1 (link fetch)',
        ]);
        $resp = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $loc  = (string)curl_getinfo($ch, CURLINFO_REDIRECT_URL);
        $err  = curl_error($ch);
        curl_close($ch);
        $chain[] = ['url'=>$url,'code'=>$code];
        if ($code >= 300 && $code < 400 && $loc) { $url = $loc; continue; }
        return ['ok'=>$code>0,'detail'=>$err ?: ('HTTP ' . $code), 'code'=>$code,'final'=>$url,'chain'=>$chain];
    }
    return ['ok'=>false,'detail'=>'too many redirects','chain'=>$chain];
}
