<?php
// Test script untuk debug purchase checkout
// Akses via: http://localhost/nsa/test_purchase_debug.php

// Simulasi data yang dikirim dari form
$testData = [
    'tanggal_masuk' => '2025-01-16',
    'supplier' => 'NSA',
    'catatan' => 'Baru',
    'items' => json_encode([
        [
            'product_id' => 1,
            'kode_barang' => '192168888',
            'nama_barang' => 'Mikrotik',
            'satuan' => 'Unit',
            'stok_saat_ini' => 10,
            'jumlah' => 100
        ],
        [
            'product_id' => 2,
            'kode_barang' => '080989999',
            'nama_barang' => 'SD Card',
            'satuan' => 'Unit',
            'stok_saat_ini' => 100,
            'jumlah' => 1
        ]
    ])
];

echo "<h2>Test Data yang akan dikirim ke /purchase/store:</h2>";
echo "<pre>";
print_r($testData);
echo "</pre>";

echo "<h3>Items (decoded):</h3>";
echo "<pre>";
print_r(json_decode($testData['items'], true));
echo "</pre>";

echo "<hr>";
echo "<h3>Test dengan CURL:</h3>";
echo "<p>Jalankan command ini di terminal:</p>";
echo "<pre>";
$curlCommand = "curl -X POST http://localhost/nsa/purchase/store \\\n";
$curlCommand .= "  -H \"Content-Type: application/x-www-form-urlencoded\" \\\n";
$curlCommand .= "  -d \"tanggal_masuk=" . $testData['tanggal_masuk'] . "\" \\\n";
$curlCommand .= "  -d \"supplier=" . urlencode($testData['supplier']) . "\" \\\n";
$curlCommand .= "  -d \"catatan=" . urlencode($testData['catatan']) . "\" \\\n";
$curlCommand .= "  -d \"items=" . urlencode($testData['items']) . "\"";
echo htmlspecialchars($curlCommand);
echo "</pre>";

echo "<hr>";
echo "<h3>Atau test dengan JavaScript (buka console browser):</h3>";
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function testCheckout() {
        var data = {
            tanggal_masuk: '2025-01-16',
            supplier: 'NSA',
            catatan: 'Baru',
            items: JSON.stringify([{
                    product_id: 1,
                    kode_barang: '192168888',
                    nama_barang: 'Mikrotik',
                    satuan: 'Unit',
                    stok_saat_ini: 10,
                    jumlah: 100
                },
                {
                    product_id: 2,
                    kode_barang: '080989999',
                    nama_barang: 'SD Card',
                    satuan: 'Unit',
                    stok_saat_ini: 100,
                    jumlah: 1
                }
            ])
        };

        console.log('Sending data:', data);

        $.ajax({
            url: 'http://localhost/nsa/purchase/store',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                console.log('Success response:', response);
                alert('Success: ' + JSON.stringify(response));
            },
            error: function(xhr, status, error) {
                console.log('Error response:', xhr.responseText);
                alert('Error: ' + xhr.responseText);
            }
        });
    }

    console.log('Test function loaded. Run: testCheckout()');
</script>
<button onclick="testCheckout()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">
    Test Checkout via AJAX
</button>