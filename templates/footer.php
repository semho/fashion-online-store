<footer class="page-footer">
  <div class="container">
    <a class="page-footer__logo" href="#">
      <img src="/img/logo--footer.svg" alt="Fashion">
    </a>
    <nav class="page-footer__menu">
      <?php showMenu($mainMenu, 'sort', 'SORT_ACS', 'main-menu--footer'); ?>
    </nav>
    <address class="page-footer__copyright">
      © Все права защищены
    </address>
  </div>
</footer>
</body>
</html>
<?php ob_end_flush(); ?>
