<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$table_name = $wpdb->prefix . 'ramisa_online_chat_settings';
$wpdb->query( 'DROP TABLE IF EXISTS `' . esc_sql( $table_name ) . '`' );

delete_option( 'ramisa_online_chat_options' );
