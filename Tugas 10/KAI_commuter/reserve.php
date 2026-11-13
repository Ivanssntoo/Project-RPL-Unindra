<?php
$scheduleId = isset($_GET['sid']) ? (int)$_GET['sid'] : 0;
$scheduleRow = null;
$trainRow = null;
if ($scheduleId > 0) {
    $qry = $conn->query("SELECT s.*, t.code AS train_code, t.name AS train_name, t.first_class_capacity, t.economy_capacity FROM `schedule_list` s LEFT JOIN `train_list` t ON t.id = s.train_id WHERE s.delete_flag = 0 AND s.id = {$scheduleId} LIMIT 1");
    if ($qry && $qry->num_rows > 0) {
        $scheduleRow = $qry->fetch_assoc();
    }
}
$h = static function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); };
$money = static function ($value) { return rtrim(rtrim(number_format((float)$value, 2, '.', ','), '0'), '.'); };
$today = date('Y-m-d');
if ($scheduleRow) {
    $isFixedDate = !empty($scheduleRow['date_schedule']);
    $bookingDate = $isFixedDate ? $scheduleRow['date_schedule'] : $today;
    $bookingTime = date('H:i', strtotime($scheduleRow['time_schedule']));
    $scheduleDateLabel = $isFixedDate ? date('d F Y', strtotime($scheduleRow['date_schedule'])) : 'Tanggal dipilih saat reservasi';
    $trainLabel = trim(($scheduleRow['train_code'] ?? '') . ' - ' . ($scheduleRow['train_name'] ?? ''));
}
?>
<div class="kai-inner-page">
    <?php if (!$scheduleRow): ?>
        <p class="kai-page-kicker">RESERVASI</p>
        <h1 class="kai-page-title">Jadwal tidak ditemukan</h1>
        <p class="kai-page-subtitle">Jadwal yang kamu pilih tidak tersedia atau sudah dihapus. Silakan kembali ke daftar jadwal.</p>
        <a href="./?page=schedules" class="kai-btn kai-btn-primary">Kembali ke jadwal</a>
    <?php else: ?>
        <p class="kai-page-kicker">DETAIL PEMESANAN</p>
        <h1 class="kai-page-title">Lengkapi reservasi</h1>
        <p class="kai-page-subtitle">Pilih kelas perjalanan dan masukkan nama seluruh penumpang sesuai identitas.</p>

        <div class="kai-progress" aria-label="Tahapan reservasi">
            <div class="kai-progress-step"><span>1</span> Pilih jadwal</div>
            <div class="kai-progress-line"></div>
            <div class="kai-progress-step"><span>2</span> Data penumpang</div>
            <div class="kai-progress-line"></div>
            <div class="kai-progress-step pending"><span>3</span> Tiket</div>
        </div>

        <section class="kai-trip-banner">
            <div>
                <p class="kai-trip-eyebrow">JADWAL DIPILIH</p>
                <h2 class="kai-trip-route"><?= $h(ucwords($scheduleRow['route_from'])) ?> <span aria-hidden="true">→</span> <?= $h(ucwords($scheduleRow['route_to'])) ?></h2>
                <p class="kai-trip-detail">Kode jadwal: <?= $h($scheduleRow['code']) ?> &nbsp;•&nbsp; <?= $h($bookingTime) ?> &nbsp;•&nbsp; <?= $h($isFixedDate ? $scheduleDateLabel : 'Setiap hari') ?></p>
            </div>
            <span class="kai-trip-pill">KAI Commuter</span>
        </section>

        <div class="kai-reservation-layout">
            <section class="kai-panel kai-form-panel">
                <h2 class="kai-panel-title">Data penumpang</h2>
                <p class="kai-panel-help">Isi nama depan, tengah (jika ada), dan belakang untuk setiap penumpang.</p>

                <form action="" id="reserve-form" autocomplete="on">
                    <input type="hidden" name="schedule_id" value="<?= (int)$scheduleRow['id'] ?>">
                    <input type="hidden" name="time" value="<?= $h($scheduleRow['time_schedule']) ?>">
                    <input type="hidden" name="fare_amount" id="fare-amount" value="<?= $h($scheduleRow['economy_fare']) ?>">

                    <?php if ($isFixedDate): ?>
                        <input type="hidden" name="date" value="<?= $h($bookingDate) ?>">
                    <?php else: ?>
                        <div class="kai-field mb-3" style="max-width:320px">
                            <label for="booking-date">Tanggal perjalanan</label>
                            <input class="kai-input" type="date" name="date" id="booking-date" value="<?= $h($bookingDate) ?>" min="<?= $h($today) ?>" required>
                        </div>
                    <?php endif; ?>

                    <p class="kai-class-label">Kelas perjalanan</p>
                    <div class="kai-class-options">
                        <label class="kai-class-option" data-seat-option="1">
                            <input type="radio" name="seat_type" value="1" required>
                            <strong>First Class</strong>
                            <small>Tarif <?= $h($money($scheduleRow['first_class_fare'])) ?></small>
                            <span class="selected-tag">DIPILIH</span>
                        </label>
                        <label class="kai-class-option selected" data-seat-option="2">
                            <input type="radio" name="seat_type" value="2" checked required>
                            <strong>Ekonomi</strong>
                            <small>Tarif <?= $h($money($scheduleRow['economy_fare'])) ?></small>
                            <span class="selected-tag">DIPILIH</span>
                        </label>
                    </div>

                    <p class="kai-passenger-section-title">PENUMPANG</p>
                    <div id="reserve-field">
                        <div class="kai-passenger-item reserve-item">
                            <div class="row">
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label>Nama depan</label>
                                        <input type="text" class="form-control" name="firstname[]" autocomplete="given-name" required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label>Nama tengah <span class="font-weight-normal">(opsional)</span></label>
                                        <input type="text" class="form-control" name="middlename[]" autocomplete="additional-name">
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label>Nama belakang</label>
                                        <input type="text" class="form-control" name="lastname[]" autocomplete="family-name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <button class="btn btn-link btn-sm btn-remove" type="button"><i class="fas fa-times mr-1" aria-hidden="true"></i>Hapus penumpang</button>
                            </div>
                        </div>
                    </div>

                    <div class="kai-reservation-actions">
                        <button class="kai-btn kai-btn-outline" type="button" id="add_item"><i class="fas fa-plus" aria-hidden="true"></i> Tambah penumpang</button>
                        <button class="kai-btn kai-btn-primary" type="submit">Kirim reservasi <i class="fas fa-arrow-right" aria-hidden="true"></i></button>
                    </div>
                </form>
            </section>

            <aside class="kai-panel kai-summary-panel">
                <h2 class="kai-panel-title">Ringkasan reservasi</h2>
                <div class="kai-summary-row">
                    <span>RUTE</span>
                    <strong><?= $h(ucwords($scheduleRow['route_from'])) ?> → <?= $h(ucwords($scheduleRow['route_to'])) ?></strong>
                </div>
                <div class="kai-summary-row">
                    <span>WAKTU</span>
                    <strong><?= $h($scheduleDateLabel) ?> · <?= $h($bookingTime) ?></strong>
                </div>
                <div class="kai-summary-row">
                    <span>KERETA</span>
                    <strong><?= $h($trainLabel ?: 'KAI Commuter') ?></strong>
                </div>
                <div class="kai-summary-row">
                    <span>KELAS</span>
                    <strong id="summary-class">Ekonomi</strong>
                </div>
                <div class="kai-summary-total">
                    <span>TARIF PER PENUMPANG</span>
                    <strong id="summary-fare"><?= $h($money($scheduleRow['economy_fare'])) ?></strong>
                </div>
                <p class="kai-note mb-0">Tarif mengikuti data jadwal yang dikelola administrator. Jumlah akhir menyesuaikan jumlah penumpang.</p>
            </aside>
        </div>
    <?php endif; ?>
</div>

<?php if ($scheduleRow): ?>
<script>
(function($){
    function removePassenger($button) {
        if ($('.reserve-item').length <= 1) {
            alert('Form reservasi membutuhkan minimal satu penumpang.');
            return;
        }
        $button.closest('.reserve-item').remove();
    }

    function updateSeatSummary() {
        var selected = $('input[name="seat_type"]:checked').val();
        var fare = '';
        var label = '';
        if (selected === '1') {
            fare = <?= json_encode((string)$scheduleRow['first_class_fare']) ?>;
            label = 'First Class';
        } else if (selected === '2') {
            fare = <?= json_encode((string)$scheduleRow['economy_fare']) ?>;
            label = 'Ekonomi';
        }
        $('#fare-amount').val(fare);
        $('#summary-class').text(label || 'Pilih kelas perjalanan');
        $('#summary-fare').text(fare ? fare : 'Pilih kelas terlebih dahulu');
        $('.kai-class-option').removeClass('selected');
        if (selected) $('[data-seat-option="' + selected + '"]').addClass('selected');
    }

    $(function(){
        $('input[name="seat_type"]').on('change', updateSeatSummary);
        updateSeatSummary();
        $('#reserve-field').on('click', '.btn-remove', function(){ removePassenger($(this)); });
        $('#add_item').on('click', function(){
            var $item = $('.reserve-item').first().clone();
            $item.find('input').val('');
            $('#reserve-field').append($item);
            $item.find('input').first().trigger('focus');
        });

        $('#reserve-form').on('submit', function(e){
            e.preventDefault();
            var $form = $(this);
            $('.pop-msg').remove();
            if (!$('input[name="seat_type"]:checked').length) {
                alert('Silakan pilih kelas perjalanan terlebih dahulu.');
                return;
            }
            start_loader();
            $.ajax({
                url: _base_url_ + 'classes/Master.php?f=save_reservation',
                data: new FormData(this),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                dataType: 'json',
                error: function(xhr){
                    console.error(xhr);
                    alert_toast('Terjadi kesalahan saat mengirim reservasi.', 'error');
                    end_loader();
                },
                success: function(resp){
                    if (resp.status === 'success') {
                        location.href = './?page=tickets&ids=' + encodeURIComponent(resp.ids);
                        return;
                    }
                    var $message = $('<div class="alert alert-danger pop-msg" role="alert"></div>');
                    $message.text(resp.msg || 'Reservasi belum dapat diproses.');
                    $form.prepend($message);
                    $('html, body').animate({scrollTop: $form.offset().top - 90}, 'fast');
                    end_loader();
                }
            });
        });
    });
})(jQuery);
</script>
<?php endif; ?>
