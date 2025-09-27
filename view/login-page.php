<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Template Name: Authora Login Page
 *
 * This is a custom template for the Authora login page
 */

get_header(); ?>

<div class="authora-login-page">
    <div class="authora-login-container">
        <?php include( AUTHORA_LOGIN_VIEW . 'loginFormContent.php' ); ?>
    </div>
</div>

<?php get_footer(); ?>
