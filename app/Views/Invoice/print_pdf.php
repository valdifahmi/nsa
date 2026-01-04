<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?= $invoice['nomor_transaksi'] ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
        }

        /* Header - Compact Layout */
        .invoice-header {
            margin-bottom: 20px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 15px;
            display: table;
            width: 100%;
        }

        .company-info {
            float: left;
            width: 55%;
        }

        .company-logo {
            font-size: 20px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 8px;
        }

        .company-details {
            font-size: 10px;
            color: #666;
            line-height: 1.4;
        }

        .company-details p {
            margin: 2px 0;
        }

        .header-right {
            float: right;
            width: 40%;
            text-align: right;
        }

        .company-logo-img {
            max-height: 50px;
            margin-bottom: 5px;
        }

        .invoice-title h1 {
            font-size: 24px;
            color: #007bff;
            margin-bottom: 3px;
        }

        .invoice-number {
            font-size: 12px;
            color: #666;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        /* Client & Invoice Info */
        .info-section {
            margin-bottom: 30px;
        }

        .info-box {
            float: left;
            width: 48%;
        }

        .info-box:last-child {
            float: right;
        }

        .info-box h3 {
            font-size: 14px;
            color: #007bff;
            margin-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 5px;
        }

        .info-box p {
            margin: 5px 0;
            font-size: 11px;
        }

        .info-box strong {
            display: inline-block;
            width: 120px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table thead {
            background-color: #f8f9fa;
        }

        .items-table th {
            padding: 10px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            border: 1px solid #dee2e6;
            color: #495057;
        }

        .items-table td {
            padding: 8px 10px;
            border: 1px solid #dee2e6;
            font-size: 11px;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Totals */
        .totals-section {
            float: right;
            width: 40%;
            margin-top: 20px;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px 10px;
            font-size: 12px;
        }

        .totals-table tr.subtotal td {
            border-top: 1px solid #dee2e6;
        }

        .totals-table tr.tax td {
            border-bottom: 1px solid #dee2e6;
        }

        .totals-table tr.grand-total td {
            font-weight: bold;
            font-size: 14px;
            background-color: #f8f9fa;
            border-top: 2px solid #007bff;
            border-bottom: 2px solid #007bff;
        }

        /* Notes */
        .notes-section {
            clear: both;
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
        }

        .notes-section h4 {
            font-size: 12px;
            margin-bottom: 5px;
            color: #007bff;
        }

        .notes-section p {
            font-size: 11px;
            color: #666;
        }

        /* Footer / Signature */
        .signature-section {
            margin-top: 30px;
            clear: both;
        }

        .signature-box {
            float: left;
            width: 45%;
            text-align: center;
        }

        .signature-box:last-child {
            float: right;
        }

        .signature-box p {
            margin-bottom: 40px;
            font-size: 11px;
        }

        .signature-line {
            border-top: 1px solid #333;
            padding-top: 5px;
            font-size: 11px;
        }

        /* Footer Info */
        .footer-info {
            clear: both;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <!-- Header - Compact Layout -->
        <div class="invoice-header clearfix">
            <div class="company-info">
                <div class="company-logo">PT. NUSANTARA SUPLAI ABADI</div>
                <div class="company-details">
                    <p><strong>HP:</strong> 0813 60 600 88</p>
                    <p><strong>Email:</strong> admin@nsaparts.com</p>
                    <p><strong>Website:</strong> https://nsaparts.com/</p>
                </div>
            </div>
            <div class="header-right">
                <img src="<?= FCPATH . 'dist/assets/images/product/logo nsa.jpg' ?>" alt="Logo" class="company-logo-img">
                <div class="invoice-title">
                    <h1>INVOICE</h1>
                    <div class="invoice-number"><?= $invoice['nomor_invoice'] ?></div>
                </div>
            </div>
        </div>

        <!-- Client & Invoice Info -->
        <div class="info-section clearfix">
            <div class="info-box">
                <h3>Kepada:</h3>
                <p><strong>Nama:</strong> <?= $invoice['nama_client'] ?? '-' ?></p>
                <p><strong>Alamat:</strong> <?= $invoice['alamat_client'] ?? '-' ?></p>
            </div>
            <div class="info-box">
                <h3>Detail Invoice:</h3>
                <p><strong>Tanggal:</strong> <?= date('d F Y', strtotime($invoice['tanggal_keluar'])) ?></p>
                <p><strong>Nomor Invoice:</strong> <?= $invoice['nomor_transaksi'] ?></p>
                <p><strong>Penerima:</strong> <?= $invoice['penerima'] ?? '-' ?></p>
            </div>
        </div>

        <!-- Items Table (Spare Parts) -->
        <?php if (!empty($items)): ?>
            <h3 style="font-size: 14px; color: #007bff; margin-bottom: 10px; margin-top: 20px;">Spare Parts</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="15%">Kode Barang</th>
                        <th width="35%">Nama Barang</th>
                        <th width="10%" class="text-center">Satuan</th>
                        <th width="10%" class="text-center">Jumlah</th>
                        <th width="12%" class="text-right">Harga Satuan</th>
                        <th width="13%" class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= $item['kode_barang'] ?></td>
                            <td><?= $item['nama_barang'] ?></td>
                            <td class="text-center"><?= $item['satuan'] ?? 'PCS' ?></td>
                            <td class="text-center"><?= $item['jumlah'] ?></td>
                            <td class="text-right">Rp <?= number_format($item['harga_jual_satuan'], 0, ',', '.') ?></td>
                            <td class="text-right">Rp <?= number_format($item['jumlah'] * $item['harga_jual_satuan'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Services Table (if Workshop) -->
        <?php if (!empty($services)): ?>
            <h3 style="font-size: 14px; color: #007bff; margin-bottom: 10px; margin-top: 20px;">Jasa Service</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="60%">Nama Jasa</th>
                        <th width="15%" class="text-center">Jumlah</th>
                        <th width="20%" class="text-right">Harga</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= $service['nama_jasa'] ?></td>
                            <td class="text-center"><?= $service['jumlah'] ?></td>
                            <td class="text-right">Rp <?= number_format($service['biaya_jasa'] * $service['jumlah'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Totals with Tax Breakdown -->
        <div class="totals-section">
            <table class="totals-table">
                <?php if ($invoice['tipe_transaksi'] === 'Workshop' && !empty($services)): ?>
                    <!-- Workshop: Show separate breakdown -->
                    <tr>
                        <td colspan="2" style="font-weight: bold; padding-top: 10px;">Spare Parts:</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 20px;">Subtotal Barang:</td>
                        <td class="text-right">Rp <?= number_format($invoice['total_barang'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td style="padding-left: 20px;">PPN (<?= $invoice['ppn_persen'] ?>%):</td>
                        <td class="text-right">Rp <?= number_format($invoice['total_ppn'], 0, ',', '.') ?></td>
                    </tr>
                    <tr class="subtotal">
                        <td style="padding-left: 20px;"><strong>Total Barang:</strong></td>
                        <td class="text-right"><strong>Rp <?= number_format($invoice['total_barang'] + $invoice['total_ppn'], 0, ',', '.') ?></strong></td>
                    </tr>

                    <tr>
                        <td colspan="2" style="font-weight: bold; padding-top: 10px;">Jasa Service:</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 20px;">Subtotal Jasa:</td>
                        <td class="text-right">Rp <?= number_format($invoice['total_jasa'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td style="padding-left: 20px;">PPh 23 (<?= $invoice['pph_persen'] ?>%):</td>
                        <td class="text-right" style="color: #dc3545;">(Rp <?= number_format($invoice['total_pph'], 0, ',', '.') ?>)</td>
                    </tr>
                    <tr class="subtotal">
                        <td style="padding-left: 20px;"><strong>Total Jasa:</strong></td>
                        <td class="text-right"><strong>Rp <?= number_format($invoice['total_jasa'] - $invoice['total_pph'], 0, ',', '.') ?></strong></td>
                    </tr>
                <?php else: ?>
                    <!-- Beli Putus: Simple breakdown -->
                    <tr class="subtotal">
                        <td>Subtotal Barang:</td>
                        <td class="text-right">Rp <?= number_format($invoice['total_barang'], 0, ',', '.') ?></td>
                    </tr>
                    <tr class="tax">
                        <td>PPN (<?= $invoice['ppn_persen'] ?>%):</td>
                        <td class="text-right">Rp <?= number_format($invoice['total_ppn'], 0, ',', '.') ?></td>
                    </tr>
                <?php endif; ?>

                <tr class="grand-total">
                    <td>GRAND TOTAL:</td>
                    <td class="text-right">Rp <?= number_format($invoice['grand_total'], 0, ',', '.') ?></td>
                </tr>
            </table>
        </div>

        <!-- Bank Info & Notes -->
        <div style="clear: both; margin-top: 30px;">
            <div style="float: left; width: 48%;">
                <div style="padding: 15px; background-color: #f8f9fa; border-left: 4px solid #007bff;">
                    <h4 style="font-size: 12px; margin-bottom: 10px; color: #007bff;">Informasi Pembayaran:</h4>
                    <table style="font-size: 11px; width: 100%;">
                        <tr>
                            <td style="padding: 3px 0; width: 100px;"><strong>Bank</strong></td>
                            <td style="padding: 3px 5px;">:</td>
                            <td style="padding: 3px 0;">Bank Mandiri</td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0;"><strong>No. Rekening</strong></td>
                            <td style="padding: 3px 5px;">:</td>
                            <td style="padding: 3px 0;">1234567890</td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0;"><strong>Atas Nama</strong></td>
                            <td style="padding: 3px 5px;">:</td>
                            <td style="padding: 3px 0;">PT. Nusantara Suplai Abadi</td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php if (!empty($invoice['catatan'])): ?>
                <div style="float: right; width: 48%;">
                    <div style="padding: 15px; background-color: #f8f9fa; border-left: 4px solid #007bff;">
                        <h4 style="font-size: 12px; margin-bottom: 10px; color: #007bff;">Catatan:</h4>
                        <p style="font-size: 11px; color: #666;"><?= nl2br($invoice['catatan']) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Signature -->
        <div class="signature-section clearfix">
            <div class="signature-box">
                <p>Hormat Kami,</p>
                <div class="signature-line">
                    ( _________________ )
                </div>
            </div>
            <div class="signature-box">
                <p>Penerima,</p>
                <div class="signature-line">
                    ( _________________ )
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-info">
            <p>Terima kasih atas kepercayaan Anda. Dokumen ini dicetak secara otomatis dan sah tanpa tanda tangan.</p>
            <p>Invoice ini dicetak pada: <?= date('d F Y H:i:s') ?></p>
        </div>
    </div>
</body>

</html>