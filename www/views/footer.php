  </main>

  <footer class="site-footer">
    <p><?php echo t('footer.line'); ?></p>
    <p><a href="<?php echo e(sourceUrl()); ?>"><?php echo t('footer.source'); ?></a></p>
  </footer>

<?php foreach ((isset($scripts) ? $scripts : []) as $script) { ?>
  <script src="<?php echo e($script); ?>"></script>
<?php } ?>
</body>
</html>
