<?php
$digits = 5;
?>
<div id="modal" class="modal-window" lang="<?php echo get_locale(); ?>" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">
  <div class="authora-container">
    <?php include( AUTHORA_LOGIN_VIEW . 'loginFormContent.php' ); ?>
  </div>
</div>