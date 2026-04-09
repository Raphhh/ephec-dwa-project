</main>

<footer class="site-footer">
    <div class="container">
        <p>&copy; 2026 RockStation - Tous droits réservés</p>
    </div>
</footer>

<?php if (isset($jsScriptPathList)) { ?>
    <?php foreach ($jsScriptPathList as $jsScriptPath) { ?>
        <script src="<?php echo $jsScriptPath; ?>"></script>
    <?php } ?>
<?php } ?>

</body>
</html>