<?php
$currentPage = isset($page) ? (string)$page : 'home';
$loggedIn = isset($_settings) && (int)$_settings->userdata('id') > 0;
$isAdmin = $loggedIn && (int)$_settings->userdata('login_type') === 1;
$loginUrl = $isAdmin ? base_url . 'admin/' : base_url . 'admin/login.php';
?>
<nav class="kai-navbar" aria-label="Navigasi utama">
    <div class="kai-container kai-nav-inner">
        <a class="kai-brand" href="<?= htmlspecialchars(base_url, ENT_QUOTES, 'UTF-8') ?>" aria-label="KAI Commuter Beranda">
            <span class="kai-brand-mark" aria-hidden="true">
                <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><rect x="6" y="4" width="20" height="24" rx="5" fill="#fff"/><rect x="9" y="8" width="14" height="8" rx="2" fill="#146ba8"/><rect x="8" y="19" width="16" height="3" rx="1" fill="#f26b3a"/><circle cx="11" cy="25" r="1.5" fill="#102b46"/><circle cx="21" cy="25" r="1.5" fill="#102b46"/></svg>
            </span>
            <span class="kai-brand-copy"><strong>KAI</strong><small>COMMUTER</small></span>
        </a>

        <button class="kai-nav-toggle" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="kai-nav-links"><i class="fas fa-bars" aria-hidden="true"></i></button>

        <div class="kai-nav-links" id="kai-nav-links">
            <a href="<?= htmlspecialchars(base_url, ENT_QUOTES, 'UTF-8') ?>" class="<?= $currentPage === 'home' ? 'active' : '' ?>">Beranda</a>
            <a href="<?= htmlspecialchars(base_url, ENT_QUOTES, 'UTF-8') ?>?page=schedules" class="<?= $currentPage === 'schedules' ? 'active' : '' ?>">Jadwal</a>
            <a href="<?= htmlspecialchars(base_url, ENT_QUOTES, 'UTF-8') ?>?page=about" class="<?= in_array($currentPage, ['about','about_us'], true) ? 'active' : '' ?>">Tentang</a>
            <a href="<?= htmlspecialchars(base_url, ENT_QUOTES, 'UTF-8') ?>?page=contact_us" class="<?= $currentPage === 'contact_us' ? 'active' : '' ?>">Kontak</a>
        </div>

        <div class="kai-nav-actions">
            <a class="kai-btn kai-btn-outline" href="<?= htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') ?>"><?= $isAdmin ? 'Dashboard' : 'Masuk' ?></a>
        </div>
    </div>
</nav>
<script>
(function(){
    var toggle = document.querySelector('.kai-nav-toggle');
    var links = document.getElementById('kai-nav-links');
    if (toggle && links) {
        toggle.addEventListener('click', function(){
            var expanded = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            links.classList.toggle('open', !expanded);
            var icon = toggle.querySelector('i');
            if (icon) icon.className = expanded ? 'fas fa-bars' : 'fas fa-times';
        });
    }
})();
</script>
