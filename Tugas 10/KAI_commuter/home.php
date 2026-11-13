<?php
$today = date('Y-m-d');
?>
<div class="kai-home-page">
    <section class="kai-home-hero">
        <div class="kai-container kai-home-hero-inner">
            <div class="kai-hero-copy">
                <span class="kai-eyebrow-pill">LAYANAN PEMESANAN KERETA</span>
                <h1 class="kai-hero-title">Perjalanan lebih mudah,<br>setiap hari.</h1>
                <p class="kai-hero-description">Temukan jadwal perjalanan, pilih kelas, lalu isi data penumpang dalam satu alur pemesanan yang sederhana.</p>
                <span class="kai-hero-note">Pemesanan online&nbsp; • &nbsp;Tiket siap dicetak</span>
            </div>
            <div class="kai-train-visual" aria-label="Ilustrasi kereta commuter">
                <svg viewBox="0 0 560 300" role="img" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                    <text x="394" y="28" fill="#d9e6f0" font-size="10" font-weight="700" letter-spacing="1.5">COMMUTER LINE</text>
                    <path d="M35 265H530" stroke="#7892a7" stroke-width="5" stroke-linecap="round"/>
                    <path d="M52 276H515" stroke="#294d69" stroke-width="3" stroke-linecap="round"/>
                    <?php for($i=0;$i<9;$i++): ?><path d="M<?= 55 + ($i*55) ?> 266v13" stroke="#7892a7" stroke-width="4" stroke-linecap="round"/><?php endfor; ?>
                    <rect x="70" y="82" width="425" height="165" rx="22" fill="#f7fafc"/>
                    <path d="M91 82h362a19 19 0 0 1 19 19v8H72v-8a19 19 0 0 1 19-19Z" fill="#dce6ef"/>
                    <rect x="70" y="111" width="46" height="112" rx="12" fill="#146ba8"/>
                    <rect x="81" y="125" width="24" height="24" rx="5" fill="#b8dff3"/>
                    <rect x="81" y="155" width="24" height="47" rx="4" fill="#0f5d93"/>
                    <?php for($i=0;$i<5;$i++): $x=130+($i*68); ?>
                        <rect x="<?= $x ?>" y="121" width="54" height="58" rx="7" fill="#b8d6e9"/>
                        <rect x="<?= $x+7 ?>" y="128" width="7" height="43" rx="3" fill="#fff" opacity=".85"/>
                    <?php endfor; ?>
                    <?php for($i=0;$i<4;$i++): $x=145+($i*82); ?>
                        <rect x="<?= $x ?>" y="190" width="36" height="35" rx="5" fill="#d8e6ef" stroke="#8aa8bf" stroke-width="1.5"/>
                        <path d="M<?= $x+18 ?> 191v33" stroke="#8aa8bf" stroke-width="1.5"/>
                    <?php endfor; ?>
                    <rect x="70" y="224" width="425" height="11" fill="#f26b3a"/>
                    <rect x="70" y="235" width="425" height="12" rx="0" fill="#102b46"/>
                    <circle cx="145" cy="247" r="8" fill="#102b46"/><circle cx="145" cy="247" r="3" fill="#7892a7"/>
                    <circle cx="420" cy="247" r="8" fill="#102b46"/><circle cx="420" cy="247" r="3" fill="#7892a7"/>
                    <path d="M58 286H510" stroke="#4b6a82" stroke-width="2" stroke-linecap="round" opacity=".6"/>
                </svg>
            </div>
        </div>
    </section>

    <div class="kai-container kai-home-content">
        <form class="kai-search-card" action="<?= htmlspecialchars(base_url, ENT_QUOTES, 'UTF-8') ?>" method="get">
            <input type="hidden" name="page" value="schedules">
            <h2 class="kai-search-heading">Cari jadwal perjalanan</h2>
            <p class="kai-search-help">Atur tanggal dan waktu keberangkatan untuk melihat jadwal yang tersedia.</p>
            <div class="kai-field">
                <label for="home-date">Tanggal perjalanan</label>
                <input class="kai-input" type="date" id="home-date" name="date" value="<?= htmlspecialchars($today, ENT_QUOTES, 'UTF-8') ?>" min="<?= htmlspecialchars($today, ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="kai-field">
                <label for="home-time">Waktu keberangkatan</label>
                <select class="kai-select" id="home-time" name="time">
                    <option value="">Semua waktu</option>
                    <option value="06:00">Pagi · sekitar 06.00</option>
                    <option value="09:00">Pagi · sekitar 09.00</option>
                    <option value="12:00">Siang · sekitar 12.00</option>
                    <option value="15:00">Sore · sekitar 15.00</option>
                    <option value="18:00">Malam · sekitar 18.00</option>
                </select>
            </div>
            <button class="kai-btn kai-btn-primary" type="submit">Cari jadwal <i class="fas fa-arrow-right" aria-hidden="true"></i></button>
        </form>

        <section class="kai-steps" aria-labelledby="kai-steps-title">
            <p class="kai-section-kicker">MUDAH DALAM 3 LANGKAH</p>
            <h2 class="kai-section-title" id="kai-steps-title">Pesan perjalanan tanpa langkah yang rumit</h2>
            <div class="kai-step-grid">
                <article class="kai-step-card">
                    <span class="kai-step-number">01</span>
                    <h3>Pilih jadwal</h3>
                    <p>Lihat rute, waktu, kereta, kelas, tarif dan kapasitas yang tersedia.</p>
                </article>
                <article class="kai-step-card">
                    <span class="kai-step-number">02</span>
                    <h3>Isi data penumpang</h3>
                    <p>Tentukan kelas perjalanan dan masukkan nama setiap penumpang.</p>
                </article>
                <article class="kai-step-card">
                    <span class="kai-step-number">03</span>
                    <h3>Cetak tiket</h3>
                    <p>Selesaikan reservasi dan cetak tiket perjalanan dari halaman tiket.</p>
                </article>
            </div>
        </section>
    </div>
</div>
