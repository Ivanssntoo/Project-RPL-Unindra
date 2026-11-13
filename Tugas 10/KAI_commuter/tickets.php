<?php
$rawIds = isset($_GET['ids']) ? (string)$_GET['ids'] : '';
$idParts = preg_split('/[^0-9]+/', $rawIds, -1, PREG_SPLIT_NO_EMPTY);
$ids = array_values(array_unique(array_filter(array_map('intval', $idParts), static function ($id) { return $id > 0; })));
$res = [];
if (!empty($ids)) {
    $idList = implode(',', $ids);
    $qry = $conn->query("SELECT r.*, s.code AS sched_code, s.train_id, s.route_from, s.route_to, CONCAT(t.code, ' - ', t.name) AS train_name FROM `reservation_list` r INNER JOIN `schedule_list` s ON s.id = r.schedule_id LEFT JOIN `train_list` t ON t.id = s.train_id WHERE r.id IN ({$idList}) ORDER BY r.id ASC");
    if ($qry) {
        $res = $qry->fetch_all(MYSQLI_ASSOC);
    }
}
$h = static function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); };
$trainGroup = [1 => 'First Class', 2 => 'Ekonomi'];
?>
<div class="kai-ticket-page">
    <?php if (empty($res)): ?>
        <span class="kai-success-badge">TIKET</span>
        <h1 class="kai-page-title mt-3">Tiket tidak ditemukan</h1>
        <p class="kai-page-subtitle">Data tiket tidak ditemukan. Silakan periksa kembali tautan tiket atau pilih jadwal untuk membuat reservasi baru.</p>
        <a href="./?page=schedules" class="kai-btn kai-btn-primary">Lihat jadwal</a>
    <?php else: ?>
        <span class="kai-success-badge"><i class="fas fa-check-circle mr-1" aria-hidden="true"></i> RESERVASI BERHASIL</span>
        <h1 class="kai-page-title mt-3">Tiket perjalanan siap dicetak</h1>
        <p class="kai-page-subtitle">Periksa kembali detail perjalanan dan data penumpang sebelum mencetak tiket.</p>

        <div class="kai-ticket-list" id="outprint">
            <?php foreach ($res as $row): ?>
                <?php
                    $passengerName = trim(strtoupper($row['lastname'] . ', ' . $row['firstname'] . ' ' . ($row['middlename'] ?? '')));
                    $journeyDate = date('d M Y', strtotime($row['schedule']));
                    $journeyTime = date('H:i', strtotime($row['schedule']));
                    $classLabel = $trainGroup[(int)$row['seat_type']] ?? 'Kelas tidak diketahui';
                ?>
                <article class="kai-ticket-card">
                    <header class="kai-ticket-header">
                        <div class="kai-ticket-brand">
                            <span class="kai-brand-mark" aria-hidden="true">
                                <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><rect x="6" y="4" width="20" height="24" rx="5" fill="#fff"/><rect x="9" y="8" width="14" height="8" rx="2" fill="#146ba8"/><rect x="8" y="19" width="16" height="3" rx="1" fill="#f26b3a"/><circle cx="11" cy="25" r="1.5" fill="#102b46"/><circle cx="21" cy="25" r="1.5" fill="#102b46"/></svg>
                            </span>
                            <span><strong>KAI Commuter</strong><small>TIKET PERJALANAN</small></span>
                        </div>
                        <span class="kai-ticket-type">RESERVATION TICKET</span>
                    </header>
                    <div class="kai-ticket-body">
                        <div class="kai-ticket-grid">
                            <div class="kai-ticket-info code">
                                <span>KODE JADWAL</span>
                                <strong><?= $h($row['sched_code']) ?></strong>
                            </div>
                            <div class="kai-ticket-info route">
                                <span>RUTE PERJALANAN</span>
                                <strong><?= $h(ucwords($row['route_from'] ?? '')) ?> → <?= $h(ucwords($row['route_to'] ?? '')) ?></strong>
                            </div>
                            <div class="kai-ticket-info">
                                <span>TANGGAL &amp; WAKTU</span>
                                <strong><?= $h($journeyDate) ?> · <?= $h($journeyTime) ?></strong>
                            </div>
                            <div class="kai-ticket-info">
                                <span>KERETA</span>
                                <strong><?= $h($row['train_name'] ?: 'KAI Commuter') ?></strong>
                            </div>
                            <div class="kai-ticket-info">
                                <span>NAMA PENUMPANG</span>
                                <strong><?= $h($passengerName) ?></strong>
                            </div>
                            <div class="kai-ticket-info">
                                <span>KELAS</span>
                                <strong><?= $h($classLabel) ?></strong>
                            </div>
                        </div>
                        <div class="kai-ticket-bottom">
                            <span class="kai-ticket-class"><?= $h(strtoupper($classLabel)) ?></span>
                            <span class="kai-note">Tarif: <?= $h(rtrim(rtrim(number_format((float)$row['fare_amount'], 2, '.', ','), '0'), '.')) ?></span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="kai-ticket-actions">
            <button type="button" class="kai-btn kai-btn-primary" id="print"><i class="fas fa-print" aria-hidden="true"></i> Cetak tiket</button>
            <a href="./?page=schedules" class="kai-btn kai-btn-outline">Kembali ke jadwal</a>
        </div>
    <?php endif; ?>
</div>

<?php if (!empty($res)): ?>
<script>
$(function(){
    $('#print').on('click', function(){
        var ticketMarkup = document.getElementById('outprint').outerHTML;
        var styleLinks = Array.from(document.querySelectorAll('link[rel="stylesheet"]'))
            .map(function(link){ return '<link rel="stylesheet" href="' + link.href + '">'; }).join('');
        var printStyle = '<style>body{margin:24px;background:#fff;font-family:Arial,sans-serif;color:#172b3d}.kai-ticket-list{display:block}.kai-ticket-card{margin:0 0 18px;border:1px solid #dce5ed;break-inside:avoid;page-break-inside:avoid}.kai-ticket-header{-webkit-print-color-adjust:exact;print-color-adjust:exact}.kai-ticket-body{padding:22px}.kai-ticket-actions{display:none!important}@page{margin:14mm}</style>';
        var win = window.open('', '_blank', 'width=1000,height=720');
        if (!win) { alert('Izinkan pop-up untuk mencetak tiket.'); return; }
        win.document.open();
        win.document.write('<!doctype html><html lang="id"><head><meta charset="utf-8"><title>Tiket KAI Commuter</title>' + styleLinks + printStyle + '</head><body>' + ticketMarkup + '</body></html>');
        win.document.close();
        win.onload = function(){ win.focus(); win.print(); };
    });
});
</script>
<?php endif; ?>
