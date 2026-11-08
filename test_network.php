<?php
echo "=== DIAGNOSIS KONEKSI KE RAJAONGKIR ===\n\n";

// 1. Cek DNS resolution
echo "1. DNS Resolution:\n";
$host = 'api.rajaongkir.com';
$ips = gethostbynamel($host);
if ($ips) {
    echo "   $host resolves to: " . implode(', ', $ips) . "\n";
} else {
    echo "   GAGAL resolve DNS untuk $host\n";
}

// 2. Cek koneksi dasar (TCP) ke port 443
echo "\n2. TCP Connection Test ke port 443:\n";
$fp = @fsockopen($host, 443, $errno, $errstr, 10);
if ($fp) {
    echo "   ✅ Koneksi TCP berhasil!\n";
    fclose($fp);
} else {
    echo "   ❌ Koneksi TCP gagal: $errstr ($errno)\n";
}

// 3. Cek koneksi ke port 80 (HTTP)
echo "\n3. TCP Connection Test ke port 80:\n";
$fp2 = @fsockopen($host, 80, $errno2, $errstr2, 10);
if ($fp2) {
    echo "   ✅ Koneksi TCP port 80 berhasil!\n";
    fclose($fp2);
} else {
    echo "   ❌ Koneksi TCP port 80 gagal: $errstr2 ($errno2)\n";
}

// 4. Cek file_get_contents (alternatif)
echo "\n4. Test file_get_contents:\n";
try {
    $ctx = stream_context_create(['http' => ['timeout' => 10]]);
    $content = @file_get_contents("https://$host/starter/province", false, $ctx);
    if ($content) {
        echo "   ✅ Berhasil! Panjang: " . strlen($content) . " bytes\n";
        echo "   Preview: " . substr($content, 0, 200) . "\n";
    } else {
        $error = error_get_last();
        echo "   ❌ Gagal: " . ($error['message'] ?? 'Unknown error') . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Exception: " . $e->getMessage() . "\n";
}

// 5. Test proxy environment
echo "\n5. Cek Proxy Environment:\n";
$proxy = getenv('HTTP_PROXY') ?: getenv('http_proxy') ?: getenv('HTTPS_PROXY') ?: getenv('https_proxy');
if ($proxy) {
    echo "   Proxy terdeteksi: $proxy\n";
} else {
    echo "   Tidak ada proxy environment\n";
}

// 6. Test ping
echo "\n6. Ping Test:\n";
$ping = @exec("ping -n 2 $host 2>&1", $output, $return);
if ($return === 0) {
    echo "   ✅ Ping berhasil\n";
} else {
    echo "   ❌ Ping gagal (mungkin ICMP diblokir)\n";
}
