<?php
// Filter jadwal berdasarkan tanggal pilihan dan, bila diisi, rentang waktu sekitar 2 jam.
$today = date('Y-m-d');
$dateInput = isset($_GET['date']) ? trim((string)$_GET['date']) : $today;
$dateObject = DateTime::createFromFormat('Y-m-d', $dateInput);
$date = ($dateObject && $dateObject->format('Y-m-d') === $dateInput) ? $dateInput : $today;
$timeInput = isset($_GET['time']) ? trim((string)$_GET['time']) : '';
$time = (preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $timeInput)) ? $timeInput : '';
$dateEsc = $conn->real_escape_string($date);
$swhere = " AND ((`type` = 1) OR (`type` = 2 AND `date_schedule` = '{$dateEsc}'))";
if ($time !== '') {
    $center = strtotime($date . ' ' . $time);
    $from = date('H:i:s', $center - (2 * 60 * 60));
    $to = date('H:i:s', $center + (2 * 60 * 60));
    $fromEsc = $conn->real_escape_string($from);
    $toEsc = $conn->real_escape_string($to);
    if ($from > $to) {
        $timeWhere = "(TIME(`time_schedule`) >= '{$fromEsc}' OR TIME(`time_schedule`) <= '{$toEsc}')";
    } else {
        $timeWhere = "TIME(`time_schedule`) BETWEEN '{$fromEsc}' AND '{$toEsc}'";
    }
    $swhere .= " AND {$timeWhere}";
}

$reserve = [];
$reservations = $conn->query("SELECT schedule_id, seat_type, COUNT(*) AS total FROM `reservation_list` WHERE DATE(`schedule`) = '{$dateEsc}' GROUP BY schedule_id, seat_type");
if ($reservations) {
    while ($reservation = $reservations->fetch_assoc()) {
        $reserve[(int)$reservation['schedule_id']][(int)$reservation['seat_type']] = (int)$reservation['total'];
    }
}

$trainMap = [];
$trainQry = $conn->query("SELECT t.id, t.code, t.name, t.first_class_capacity, t.economy_capacity, CONCAT(t.code, ' - ', t.name) AS train FROM `train_list` t WHERE t.id IN (SELECT train_id FROM `schedule_list` WHERE delete_flag = 0 {$swhere})");
if ($trainQry) {
    while ($train = $trainQry->fetch_assoc()) {
        $trainMap[(int)$train['id']] = $train;
    }
}

$schedules = [];
$qry = $conn->query("SELECT * FROM `schedule_list` WHERE delete_flag = 0 {$swhere} ORDER BY TIME(`time_schedule`) ASC");
if ($qry) {
    while ($row = $qry->fetch_assoc()) {
        $train = $trainMap[(int)$row['train_id']] ?? null;
        $firstCapacity = $train ? (int)$train['first_class_capacity'] : 0;
        $economyCapacity = $train ? (int)$train['economy_capacity'] : 0;
        $row['train_name'] = $train ? $train['train'] : 'Kereta tidak tersedia';
        $row['first_slot'] = max(0, $firstCapacity - ($reserve[(int)$row['id']][1] ?? 0));
        $row['economy_slot'] = max(0, $economyCapacity - ($reserve[(int)$row['id']][2] ?? 0));
        $row['journey_date'] = ($row['type'] == 1) ? $date : $row['date_schedule'];
        $schedules[] = $row;
    }
}
$h = static function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); };
$money = static function ($value) { return rtrim(rtrim(number_format((float)$value, 2, '.', ','), '0'), '.'); };
?>
<div class="kai-inner-page">
    <p class="kai-page-kicker">PENCARIAN PERJALANAN</p>
    <h1 class="kai-page-title">Jadwal perjalanan</h1>
    <p class="kai-page-subtitle">Pilih jadwal yang sesuai. Rute, waktu, kereta, kelas, tarif, dan kapasitas ditampilkan pada hasil pencarian.</p>

    <form class="kai-panel kai-filter-panel" id="filter-schedule" method="get" action="">
        <input type="hidden" name="page" value="schedules">
        <div class="kai-field">
            <label for="schedule-date">Tanggal perjalanan</label>
            <input class="kai-input" type="date" name="date" id="schedule-date" value="<?= $h($date) ?>" min="<?= $h($today) ?>" required>
        </div>
        <div class="kai-field">
            <label for="schedule-time">Waktu keberangkatan</label>
            <select class="kai-select" name="time" id="schedule-time">
                <option value="" <?= $time === '' ? 'selected' : '' ?>>Semua waktu</option>
                <?php foreach (['06:00'=>'Pagi · sekitar 06.00','09:00'=>'Pagi · sekitar 09.00','12:00'=>'Siang · sekitar 12.00','15:00'=>'Sore · sekitar 15.00','18:00'=>'Malam · sekitar 18.00'] as $value => $label): ?>
                    <option value="<?= $h($value) ?>" <?= $time === $value ? 'selected' : '' ?>><?= $h($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="kai-btn kai-btn-primary" type="submit">Tampilkan jadwal <i class="fas fa-arrow-right" aria-hidden="true"></i></button>
    </form>

    <div class="kai-results-head">
        <div>
            <h2>JADWAL YANG TERSEDIA</h2>
            <p><?= count($schedules) ?> jadwal ditemukan untuk <?= $h(date('d F Y', strtotime($date))) ?><?= $time !== '' ? ' · sekitar pukul ' . $h($time) : '' ?></p>
        </div>
    </div>

    <?php if (empty($schedules)): ?>
        <div class="kai-empty-state">
            <i class="far fa-calendar-times fa-2x mb-2" aria-hidden="true"></i>
            <div><strong>Belum ada jadwal pada pilihan ini.</strong></div>
            <div>Silakan pilih tanggal atau waktu keberangkatan yang berbeda.</div>
        </div>
    <?php else: ?>
        <div class="kai-schedule-list">
            <?php foreach ($schedules as $row): ?>
                <?php
                    $timeLabel = date('H:i', strtotime($row['time_schedule']));
                    $typeLabel = ((int)$row['type'] === 1) ? 'Setiap hari' : date('d M Y', strtotime($row['date_schedule']));
                    $fromLabel = ucwords((string)$row['route_from']);
                    $toLabel = ucwords((string)$row['route_to']);
                ?>
                <article class="kai-schedule-card">
                    <div class="kai-schedule-meta">
                        <span class="kai-code-badge"><?= $h($row['code']) ?></span>
                        <strong class="kai-schedule-time"><?= $h($timeLabel) ?></strong>
                        <span class="kai-schedule-type"><?= $h($typeLabel) ?></span>
                    </div>
                    <div class="kai-schedule-route">
                        <h3 class="kai-route-main"><?= $h($fromLabel) ?> <span class="route-arrow">→</span> <?= $h($toLabel) ?></h3>
                        <p class="kai-route-train"><?= $h($row['train_name']) ?></p>
                        <div class="kai-route-fares">
                            <span class="kai-fare-chip">Ekonomi · <?= $h($money($row['economy_fare'])) ?></span>
                            <span class="kai-fare-chip orange">First Class · <?= $h($money($row['first_class_fare'])) ?></span>
                        </div>
                    </div>
                    <div class="kai-schedule-action">
                        <a href="./?page=reserve&amp;sid=<?= (int)$row['id'] ?>" class="kai-btn kai-btn-primary">Pilih jadwal</a>
                        <span class="kai-slot-note">Sisa kursi dihitung untuk tanggal pilihan</span>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
