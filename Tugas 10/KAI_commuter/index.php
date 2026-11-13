<?php require_once('./config.php'); ?>
<!DOCTYPE html>
<html lang="id" style="height:auto;">
<?php require_once('inc/header.php'); ?>
<body class="layout-top-nav kai-app" style="height:auto;">
<div class="wrapper">
    <?php $page = isset($_GET['page']) ? basename((string)$_GET['page']) : 'home'; ?>
    <?php require_once('inc/topBarNav.php'); ?>

    <?php if ($_settings->chk_flashdata('success')): ?>
        <script>
            alert_toast(<?= json_encode($_settings->flashdata('success')) ?>, 'success');
        </script>
    <?php endif; ?>

    <main class="kai-site-main" id="main-content">
        <section class="kai-page-wrapper">
            <?php
                $allowedPages = ['home', 'schedules', 'reserve', 'tickets', 'about', 'about_us', 'contact_us'];
                if (!in_array($page, $allowedPages, true) || !is_file(__DIR__ . '/' . $page . '.php')) {
                    include '404.html';
                } else {
                    include __DIR__ . '/' . $page . '.php';
                }
            ?>
        </section>
    </main>

    <div class="modal fade rounded-0" id="confirm_modal" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Konfirmasi</h5></div>
                <div class="modal-body"><div id="delete_content"></div></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="confirm" onclick="">Lanjutkan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade rounded-0" id="uni_modal" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title"></h5></div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="submit" onclick="$('#uni_modal form').submit()">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="uni_modal_right" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-full-height modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span class="fa fa-arrow-right"></span></button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="viewer_modal" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Tutup"><span class="fa fa-times"></span></button>
                <img src="" alt="">
            </div>
        </div>
    </div>

    <?php require_once('inc/footer.php'); ?>
</div>
</body>
</html>
