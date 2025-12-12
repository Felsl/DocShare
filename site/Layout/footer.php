<?php
// site/view/layout/footer.php
// compute base same as header
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
?>
</div> <!-- /container -->

<footer class="text-center mt-5 mb-3 text-muted">
    <small>&copy; <?= date('Y') ?> DocShare — Hệ thống chia sẻ tài liệu</small>
</footer>

<!-- Bootstrap JS (CDN). Using CDN avoids missing local file problems -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- If you have app-specific JS -->
<script src="<?= $base ?>/assets/js/app.js"></script>

</body>

</html>