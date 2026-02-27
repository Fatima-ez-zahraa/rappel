    </div><!-- end page content -->
</main><!-- end dashboard-main -->

<?php include __DIR__ . '/cookie_banner.php'; ?>

<!-- Dashboard JS -->
<script src="/rappel/public/assets/js/app.js?v=4.1"></script>
<script>
// Initialize Lucide icons
if (typeof lucide !== 'undefined') lucide.createIcons();
</script>
<?php if (isset($extraScript)) echo $extraScript; ?>
</body>
</html>
