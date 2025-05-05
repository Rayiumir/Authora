<?php

defined('ABSPATH') || exit;

function authora_activation(){

    global $wpdb;
    
    $table              = $wpdb->authora_login;
    $table_collation    = $wpdb->collate;

    $sql = "CREATE TABLE `$table` (
        `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` bigint(20) unsigned NOT NULL DEFAULT 0,
        `mobile` varchar(11) NOT NULL,
        `code` varchar(20) NOT NULL,
        `message_id` bigint(20) unsigned NOT NULL DEFAULT 0,
        `price` smallint(5) unsigned NOT NULL DEFAULT 0,
        `status` varchar(20) NOT NULL DEFAULT 'pending',
        `expired_at` datetime NOT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        PRIMARY KEY (`ID`),
        KEY `user_id` (`user_id`),
        KEY `status` (`status`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=$table_collation";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}