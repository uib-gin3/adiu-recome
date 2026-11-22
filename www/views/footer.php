  </main>

  <footer class="site-footer">
    <p><?php echo t('footer.line'); ?></p>
  </footer>

<?php foreach ((isset($scripts) ? $scripts : []) as $script) { ?>
  <script src="<?php echo e($script); ?>"></script>
<?php } ?>
</body>
</html>
