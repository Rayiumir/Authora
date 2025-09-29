<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$digits = 5;
?>
<div id="modal" class="modal-window" lang="<?php echo esc_attr( get_locale() ); ?>" dir="<?php echo esc_attr( is_rtl() ? 'rtl' : 'ltr' ); ?>">
  <div class="authora-container">
    <?php include( AUTHORA_LOGIN_VIEW . 'loginFormContent.php' ); ?>
  </div>
</div>