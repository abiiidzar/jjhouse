<?php
// Fix BOM pada file JSON
$files = [
    'resources/data/rajaongkir_provinces.json',
    'resources/data/rajaongkir_cities.json',
];

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "File tidak ditemukan: $file\n";
        continue;
    }
    
    $content = file_get_contents($file);
    // Hapus BOM (UTF-8 BOM: EF BB BF)
    $bom = pack('H*', 'EFBBBF');
    if (substr($content, 0, 3) === $bom) {
        $content = substr($content, 3);
        file_put_contents($file, $content);
        echo "BOM dihapus dari: $file\n";
    } else {
        echo "Tidak ada BOM di: $file\n";
    }
    
    // Verifikasi JSON valid
    $data = json_decode($content, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "  JSON valid, count: " . count($data) . "\n";
    } else {
        echo "  JSON ERROR: " . json_last_error_msg() . "\n";
    }
}
