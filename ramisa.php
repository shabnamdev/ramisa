<?php
/**
 * Plugin Name: Ramisa Online Chat
 * Plugin URI: https://shcd.ir
 * Description: Adds a polished floating contact widget with a native settings panel, working hours, contact buttons, quick replies, animations, and shortcode support.
 * Version: 1.0.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: SHABNAM
 * Author URI: https://shabnam.dev
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ramisa-online-chat
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RAMISA_ONLINE_CHAT_VERSION', '1.0.0' );
define( 'RAMISA_ONLINE_CHAT_FILE', __FILE__ );
define( 'RAMISA_ONLINE_CHAT_DIR', plugin_dir_path( __FILE__ ) );
define( 'RAMISA_ONLINE_CHAT_URL', plugin_dir_url( __FILE__ ) );
define( 'RAMISA_ONLINE_CHAT_OPTION', 'ramisa_online_chat_options' );
define( 'RAMISA_ONLINE_CHAT_TABLE_SUFFIX', 'ramisa_online_chat_settings' );
define( 'RAMISA_ONLINE_CHAT_SETTINGS_KEY', 'settings' );
define( 'RAMISA_ONLINE_CHAT_MENU_SLUG', 'ramisa-online-chat-panel' );
define( 'RAMISA_ONLINE_CHAT_DEVELOPER_URL', 'https://shabnam.dev' );

function ramisa_online_chat_table_name() {
	global $wpdb;
	return $wpdb->prefix . RAMISA_ONLINE_CHAT_TABLE_SUFFIX;
}

function ramisa_online_chat_table_exists() {
	global $wpdb;

	$table_name = ramisa_online_chat_table_name();
	$found      = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) ) );

	return $found === $table_name;
}

function ramisa_online_chat_create_table() {
	global $wpdb;

	$table_name      = ramisa_online_chat_table_name();
	$charset_collate = $wpdb->get_charset_collate();

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$sql = "CREATE TABLE {$table_name} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		setting_key varchar(191) NOT NULL,
		setting_value longtext NOT NULL,
		created_at datetime NOT NULL,
		updated_at datetime NOT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY setting_key (setting_key)
	) {$charset_collate};";

	dbDelta( $sql );
}

function ramisa_online_chat_read_stored_options() {
	global $wpdb;

	if ( ! ramisa_online_chat_table_exists() ) {
		return array();
	}

	$table_name = ramisa_online_chat_table_name();
	$value      = $wpdb->get_var( $wpdb->prepare( "SELECT setting_value FROM {$table_name} WHERE setting_key = %s LIMIT 1", RAMISA_ONLINE_CHAT_SETTINGS_KEY ) );

	if ( null === $value || '' === $value ) {
		return array();
	}

	$options = maybe_unserialize( $value );

	return is_array( $options ) ? $options : array();
}

function ramisa_online_chat_save_stored_options( $options ) {
	global $wpdb;

	if ( ! is_array( $options ) ) {
		return false;
	}

	if ( ! ramisa_online_chat_table_exists() ) {
		ramisa_online_chat_create_table();
	}

	$table_name = ramisa_online_chat_table_name();
	$now        = current_time( 'mysql' );
	$stored     = maybe_serialize( $options );
	$row_id     = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table_name} WHERE setting_key = %s LIMIT 1", RAMISA_ONLINE_CHAT_SETTINGS_KEY ) );

	if ( $row_id ) {
		return false !== $wpdb->update(
			$table_name,
			array(
				'setting_value' => $stored,
				'updated_at'    => $now,
			),
			array( 'id' => absint( $row_id ) ),
			array( '%s', '%s' ),
			array( '%d' )
		);
	}

	return false !== $wpdb->insert(
		$table_name,
		array(
			'setting_key'   => RAMISA_ONLINE_CHAT_SETTINGS_KEY,
			'setting_value' => $stored,
			'created_at'    => $now,
			'updated_at'    => $now,
		),
		array( '%s', '%s', '%s', '%s' )
	);
}

function ramisa_online_chat_migrate_legacy_option() {
	$legacy = get_option( RAMISA_ONLINE_CHAT_OPTION, false );

	if ( is_array( $legacy ) && ! empty( $legacy ) && array() === ramisa_online_chat_read_stored_options() ) {
		ramisa_online_chat_save_stored_options( $legacy );
	}

	delete_option( RAMISA_ONLINE_CHAT_OPTION );
}

function ramisa_online_chat_prepare_storage() {
	if ( ! ramisa_online_chat_table_exists() ) {
		ramisa_online_chat_create_table();
	}

	ramisa_online_chat_migrate_legacy_option();
}

function ramisa_online_chat_get_plugin_header() {
	return array(
		'name'    => 'Ramisa Online Chat',
		'version' => RAMISA_ONLINE_CHAT_VERSION,
		'author'  => 'SHABNAM.DEV',
	);
}

function ramisa_online_chat_normalize_whatsapp_number( $value ) {
	$value  = sanitize_text_field( wp_unslash( $value ) );
	$digits = preg_replace( '/\D+/', '', $value );

	if ( 0 === strpos( $digits, '00' ) ) {
		$digits = substr( $digits, 2 );
	}

	return ( strlen( $digits ) >= 8 && strlen( $digits ) <= 15 ) ? $digits : '';
}

function ramisa_online_chat_get_primary_chat_url( $options ) {
	if ( ! empty( $options['chat_url'] ) ) {
		return esc_url_raw( $options['chat_url'] );
	}

	$number = ! empty( $options['whatsapp_number'] ) ? ramisa_online_chat_normalize_whatsapp_number( $options['whatsapp_number'] ) : '';
	if ( '' !== $number ) {
		return 'https://wa.me/' . rawurlencode( $number );
	}

	return '';
}

function ramisa_online_chat_get_destination_status( $options ) {
	$destination = ramisa_online_chat_get_primary_chat_url( $options );

	if ( '' !== $destination ) {
		return array(
			'ready'   => true,
			'label'   => __( 'WhatsApp destination is ready', 'ramisa-online-chat' ),
			'message' => __( 'The widget has a valid chat destination and will open it after a visitor clicks the button.', 'ramisa-online-chat' ),
		);
	}

	return array(
		'ready'   => false,
		'label'   => __( 'WhatsApp destination is not configured', 'ramisa-online-chat' ),
		'message' => __( 'Add a valid WhatsApp number or a public chat URL before publishing the widget.', 'ramisa-online-chat' ),
	);
}

function ramisa_online_chat_allowed_themes() {
	return array(
		'green'  => __( 'Emerald', 'ramisa-online-chat' ),
		'blue'   => __( 'Blue', 'ramisa-online-chat' ),
		'violet' => __( 'Violet', 'ramisa-online-chat' ),
		'gold'   => __( 'Gold', 'ramisa-online-chat' ),
		'dark'   => __( 'Dark', 'ramisa-online-chat' ),
	);
}

function ramisa_online_chat_allowed_animations() {
	return array(
		'soft'   => __( 'Soft entrance', 'ramisa-online-chat' ),
		'pulse'  => __( 'Pulse', 'ramisa-online-chat' ),
		'bounce' => __( 'Bounce', 'ramisa-online-chat' ),
		'float'  => __( 'Floating', 'ramisa-online-chat' ),
		'none'   => __( 'No motion', 'ramisa-online-chat' ),
	);
}

function ramisa_online_chat_allowed_sizes() {
	return array(
		'small'  => __( 'Small', 'ramisa-online-chat' ),
		'normal' => __( 'Normal', 'ramisa-online-chat' ),
		'large'  => __( 'Large', 'ramisa-online-chat' ),
	);
}

function ramisa_online_chat_allowed_shapes() {
	return array(
		'rounded' => __( 'Rounded square', 'ramisa-online-chat' ),
		'circle'  => __( 'Circle', 'ramisa-online-chat' ),
		'pill'    => __( 'Wide pill', 'ramisa-online-chat' ),
	);
}

function ramisa_online_chat_allowed_cards() {
	return array(
		'neumorphic' => __( 'Neumorphic', 'ramisa-online-chat' ),
		'glass'      => __( 'Glass', 'ramisa-online-chat' ),
		'compact'    => __( 'Compact', 'ramisa-online-chat' ),
	);
}

function ramisa_online_chat_allowed_label_modes() {
	return array(
		'always' => __( 'Always visible', 'ramisa-online-chat' ),
		'hover'  => __( 'Only on hover', 'ramisa-online-chat' ),
		'hidden' => __( 'Hidden', 'ramisa-online-chat' ),
	);
}

function ramisa_online_chat_allowed_header_alignments() {
	return array(
		'right'  => __( 'Right aligned', 'ramisa-online-chat' ),
		'center' => __( 'Centered', 'ramisa-online-chat' ),
		'left'   => __( 'Left aligned', 'ramisa-online-chat' ),
	);
}

function ramisa_online_chat_allowed_agent_layouts() {
	return array(
		'list' => __( 'List', 'ramisa-online-chat' ),
		'grid' => __( 'Grid', 'ramisa-online-chat' ),
	);
}

function ramisa_online_chat_allowed_icons() {
	return array(
		'chat'    => '💬',
		'support' => '🎧',
		'send'    => '✉',
		'phone'   => '☎',
		'help'    => '?',
	);
}

function ramisa_online_chat_weekdays() {
	return array(
		'sunday'    => __( 'Sunday', 'ramisa-online-chat' ),
		'monday'    => __( 'Monday', 'ramisa-online-chat' ),
		'tuesday'   => __( 'Tuesday', 'ramisa-online-chat' ),
		'wednesday' => __( 'Wednesday', 'ramisa-online-chat' ),
		'thursday'  => __( 'Thursday', 'ramisa-online-chat' ),
		'friday'    => __( 'Friday', 'ramisa-online-chat' ),
		'saturday'  => __( 'Saturday', 'ramisa-online-chat' ),
	);
}

function ramisa_online_chat_default_timezone() {
	$timezone = function_exists( 'wp_timezone_string' ) ? wp_timezone_string() : '';
	return $timezone ? $timezone : 'UTC';
}

function ramisa_online_chat_default_workdays() {
	return array(
		'sunday'    => '1',
		'monday'    => '1',
		'tuesday'   => '1',
		'wednesday' => '1',
		'thursday'  => '1',
		'friday'    => '0',
		'saturday'  => '1',
	);
}

function ramisa_online_chat_default_weekly_hours() {
	$workdays = ramisa_online_chat_default_workdays();
	$hours    = array();

	foreach ( ramisa_online_chat_weekdays() as $day_key => $day_label ) {
		$hours[ $day_key ] = array(
			'enabled' => isset( $workdays[ $day_key ] ) ? $workdays[ $day_key ] : '0',
			'start'   => '09:00',
			'end'     => '18:00',
		);
	}

	return $hours;
}

function ramisa_online_chat_default_options() {
	return array(
		'enabled'             => '1',
		'chat_url'            => '',
		'whatsapp_number'     => '',
		'button_text'         => __( 'Start a conversation', 'ramisa-online-chat' ),
		'bubble_label'        => __( 'Need assistance?', 'ramisa-online-chat' ),
		'bubble_title'        => __( 'How can we help today?', 'ramisa-online-chat' ),
		'bubble_subtitle'     => __( 'Choose the right contact option and send your message.', 'ramisa-online-chat' ),
		'agent_name'          => __( 'Support team', 'ramisa-online-chat' ),
		'agent_title'         => __( 'Customer support', 'ramisa-online-chat' ),
		'agent_message'       => __( 'Welcome. Choose a support option and our team will be glad to help.', 'ramisa-online-chat' ),
		'agent_photo'         => RAMISA_ONLINE_CHAT_URL . 'assets/image/agent1.webp',
		'position'            => 'left',
		'theme'               => 'green',
		'animation'           => 'soft',
		'button_size'         => 'normal',
		'button_shape'        => 'rounded',
		'card_style'          => 'neumorphic',
		'label_mode'          => 'always',
		'header_alignment'    => 'right',
		'agent_list_layout'   => 'list',
		'agent_search_enabled' => '0',
		'show_agent_avatars'  => '1',
		'icon'                => 'chat',
		'autoshow'            => '0',
		'show_current_time'   => '1',
		'open_in_new_tab'     => '1',
		'desktop_visibility'  => '1',
		'mobile_visibility'   => '1',
		'badge_enabled'       => '1',
		'badge_text'          => '1',
		'availability_enabled' => '0',
		'timezone'            => ramisa_online_chat_default_timezone(),
		'work_start'          => '09:00',
		'work_end'            => '18:00',
		'workdays'            => ramisa_online_chat_default_workdays(),
		'weekly_hours'        => ramisa_online_chat_default_weekly_hours(),
		'online_text'         => __( 'Available now', 'ramisa-online-chat' ),
		'offline_text'        => __( 'Currently unavailable', 'ramisa-online-chat' ),
		'offline_message'     => __( 'Our team is unavailable at the moment. Please leave a message and we will reply during working hours.', 'ramisa-online-chat' ),
		'hide_when_offline'   => '0',
		'quick_replies_enabled' => '1',
		'quick_1_text'        => __( 'Sales support', 'ramisa-online-chat' ),
		'quick_1_url'         => '',
		'quick_2_text'        => __( 'Technical support', 'ramisa-online-chat' ),
		'quick_2_url'         => '',
		'quick_3_text'        => __( 'Order follow-up', 'ramisa-online-chat' ),
		'quick_3_url'         => '',
		'secondary_enabled'   => '0',
		'secondary_name'      => __( 'Sales support', 'ramisa-online-chat' ),
		'secondary_title'     => __( 'Sales department', 'ramisa-online-chat' ),
		'secondary_url'       => '',
		'secondary_photo'     => RAMISA_ONLINE_CHAT_URL . 'assets/image/agent2.webp',
		'third_enabled'       => '0',
		'third_name'          => __( 'Technical support', 'ramisa-online-chat' ),
		'third_title'         => __( 'Technical department', 'ramisa-online-chat' ),
		'third_url'           => '',
		'third_photo'         => RAMISA_ONLINE_CHAT_URL . 'assets/image/agent3.webp',
	);
}

function ramisa_online_chat_get_options() {
	$options = ramisa_online_chat_read_stored_options();

	if ( empty( $options ) ) {
		$legacy = get_option( RAMISA_ONLINE_CHAT_OPTION, false );
		if ( is_array( $legacy ) && ! empty( $legacy ) ) {
			$options = $legacy;
			ramisa_online_chat_save_stored_options( $legacy );
			delete_option( RAMISA_ONLINE_CHAT_OPTION );
		}
	}

	if ( ! is_array( $options ) ) {
		$options = array();
	}

	$defaults = ramisa_online_chat_default_options();
	$options  = wp_parse_args( $options, $defaults );

	$options['workdays']     = isset( $options['workdays'] ) && is_array( $options['workdays'] ) ? wp_parse_args( $options['workdays'], $defaults['workdays'] ) : $defaults['workdays'];
	$options['weekly_hours'] = isset( $options['weekly_hours'] ) && is_array( $options['weekly_hours'] ) ? $options['weekly_hours'] : array();

	foreach ( $defaults['weekly_hours'] as $day_key => $day_hours ) {
		$current = isset( $options['weekly_hours'][ $day_key ] ) && is_array( $options['weekly_hours'][ $day_key ] ) ? $options['weekly_hours'][ $day_key ] : array();
		$options['weekly_hours'][ $day_key ] = wp_parse_args( $current, $day_hours );
	}

	return $options;
}

function ramisa_online_chat_sanitize_choice( $value, $allowed, $fallback ) {
	$value = sanitize_key( $value );
	return array_key_exists( $value, $allowed ) ? $value : $fallback;
}

function ramisa_online_chat_sanitize_time( $value, $fallback ) {
	$value = sanitize_text_field( wp_unslash( $value ) );
	return preg_match( '/^(?:[01]\d|2[0-3]):[0-5]\d$/', $value ) ? $value : $fallback;
}

function ramisa_online_chat_sanitize_timezone( $value ) {
	$value       = sanitize_text_field( wp_unslash( $value ) );
	$identifiers = timezone_identifiers_list();
	return in_array( $value, $identifiers, true ) ? $value : ramisa_online_chat_default_timezone();
}

function ramisa_online_chat_sanitize_options( $input ) {
	$input    = is_array( $input ) ? $input : array();
	$defaults = ramisa_online_chat_default_options();
	$output   = array();

	$output['enabled']             = empty( $input['enabled'] ) ? '0' : '1';
	$output['chat_url']            = isset( $input['chat_url'] ) ? esc_url_raw( wp_unslash( $input['chat_url'] ) ) : '';
	$output['whatsapp_number']     = isset( $input['whatsapp_number'] ) ? ramisa_online_chat_normalize_whatsapp_number( $input['whatsapp_number'] ) : '';
	$output['button_text']         = isset( $input['button_text'] ) ? sanitize_text_field( wp_unslash( $input['button_text'] ) ) : $defaults['button_text'];
	$output['bubble_label']        = isset( $input['bubble_label'] ) ? sanitize_text_field( wp_unslash( $input['bubble_label'] ) ) : $defaults['bubble_label'];
	$output['bubble_title']        = isset( $input['bubble_title'] ) ? sanitize_text_field( wp_unslash( $input['bubble_title'] ) ) : $defaults['bubble_title'];
	$output['bubble_subtitle']     = isset( $input['bubble_subtitle'] ) ? sanitize_text_field( wp_unslash( $input['bubble_subtitle'] ) ) : $defaults['bubble_subtitle'];
	$output['agent_name']          = isset( $input['agent_name'] ) ? sanitize_text_field( wp_unslash( $input['agent_name'] ) ) : $defaults['agent_name'];
	$output['agent_title']         = isset( $input['agent_title'] ) ? sanitize_text_field( wp_unslash( $input['agent_title'] ) ) : $defaults['agent_title'];
	$output['agent_message']       = isset( $input['agent_message'] ) ? sanitize_textarea_field( wp_unslash( $input['agent_message'] ) ) : $defaults['agent_message'];
	$output['agent_photo']         = isset( $input['agent_photo'] ) ? esc_url_raw( wp_unslash( $input['agent_photo'] ) ) : $defaults['agent_photo'];
	$output['position']            = isset( $input['position'] ) && in_array( $input['position'], array( 'left', 'right' ), true ) ? sanitize_key( $input['position'] ) : 'left';
	$output['theme']               = isset( $input['theme'] ) ? ramisa_online_chat_sanitize_choice( $input['theme'], ramisa_online_chat_allowed_themes(), 'green' ) : 'green';
	$output['animation']           = isset( $input['animation'] ) ? ramisa_online_chat_sanitize_choice( $input['animation'], ramisa_online_chat_allowed_animations(), 'soft' ) : 'soft';
	$output['button_size']         = isset( $input['button_size'] ) ? ramisa_online_chat_sanitize_choice( $input['button_size'], ramisa_online_chat_allowed_sizes(), 'normal' ) : 'normal';
	$output['button_shape']        = isset( $input['button_shape'] ) ? ramisa_online_chat_sanitize_choice( $input['button_shape'], ramisa_online_chat_allowed_shapes(), 'rounded' ) : 'rounded';
	$output['card_style']          = isset( $input['card_style'] ) ? ramisa_online_chat_sanitize_choice( $input['card_style'], ramisa_online_chat_allowed_cards(), 'neumorphic' ) : 'neumorphic';
	$output['label_mode']          = isset( $input['label_mode'] ) ? ramisa_online_chat_sanitize_choice( $input['label_mode'], ramisa_online_chat_allowed_label_modes(), 'always' ) : 'always';
	$output['header_alignment']    = isset( $input['header_alignment'] ) ? ramisa_online_chat_sanitize_choice( $input['header_alignment'], ramisa_online_chat_allowed_header_alignments(), 'right' ) : 'right';
	$output['agent_list_layout']   = isset( $input['agent_list_layout'] ) ? ramisa_online_chat_sanitize_choice( $input['agent_list_layout'], ramisa_online_chat_allowed_agent_layouts(), 'list' ) : 'list';
	$output['agent_search_enabled'] = empty( $input['agent_search_enabled'] ) ? '0' : '1';
	$output['show_agent_avatars']  = empty( $input['show_agent_avatars'] ) ? '0' : '1';
	$output['icon']                = isset( $input['icon'] ) ? ramisa_online_chat_sanitize_choice( $input['icon'], ramisa_online_chat_allowed_icons(), 'chat' ) : 'chat';
	$output['autoshow']            = empty( $input['autoshow'] ) ? '0' : '1';
	$output['show_current_time']   = empty( $input['show_current_time'] ) ? '0' : '1';
	$output['open_in_new_tab']     = empty( $input['open_in_new_tab'] ) ? '0' : '1';
	$output['desktop_visibility']  = empty( $input['desktop_visibility'] ) ? '0' : '1';
	$output['mobile_visibility']   = empty( $input['mobile_visibility'] ) ? '0' : '1';
	$output['badge_enabled']       = empty( $input['badge_enabled'] ) ? '0' : '1';
	$output['badge_text']          = isset( $input['badge_text'] ) ? sanitize_text_field( wp_unslash( $input['badge_text'] ) ) : $defaults['badge_text'];
	$output['availability_enabled'] = empty( $input['availability_enabled'] ) ? '0' : '1';
	$output['timezone']            = isset( $input['timezone'] ) ? ramisa_online_chat_sanitize_timezone( $input['timezone'] ) : ramisa_online_chat_default_timezone();
	$output['work_start']          = isset( $input['work_start'] ) ? ramisa_online_chat_sanitize_time( $input['work_start'], '09:00' ) : '09:00';
	$output['work_end']            = isset( $input['work_end'] ) ? ramisa_online_chat_sanitize_time( $input['work_end'], '18:00' ) : '18:00';
	$output['workdays']            = array();
	$output['weekly_hours']        = array();

	$incoming_workdays     = isset( $input['workdays'] ) && is_array( $input['workdays'] ) ? $input['workdays'] : array();
	$incoming_weekly_hours = isset( $input['weekly_hours'] ) && is_array( $input['weekly_hours'] ) ? $input['weekly_hours'] : array();
	foreach ( ramisa_online_chat_weekdays() as $day_key => $day_label ) {
		$enabled = empty( $incoming_workdays[ $day_key ] ) ? '0' : '1';
		$day_in  = isset( $incoming_weekly_hours[ $day_key ] ) && is_array( $incoming_weekly_hours[ $day_key ] ) ? $incoming_weekly_hours[ $day_key ] : array();
		$output['workdays'][ $day_key ] = $enabled;
		$output['weekly_hours'][ $day_key ] = array(
			'enabled' => $enabled,
			'start'   => isset( $day_in['start'] ) ? ramisa_online_chat_sanitize_time( $day_in['start'], $output['work_start'] ) : $output['work_start'],
			'end'     => isset( $day_in['end'] ) ? ramisa_online_chat_sanitize_time( $day_in['end'], $output['work_end'] ) : $output['work_end'],
		);
	}

	$output['online_text']          = isset( $input['online_text'] ) ? sanitize_text_field( wp_unslash( $input['online_text'] ) ) : $defaults['online_text'];
	$output['offline_text']         = isset( $input['offline_text'] ) ? sanitize_text_field( wp_unslash( $input['offline_text'] ) ) : $defaults['offline_text'];
	$output['offline_message']      = isset( $input['offline_message'] ) ? sanitize_textarea_field( wp_unslash( $input['offline_message'] ) ) : $defaults['offline_message'];
	$output['hide_when_offline']    = empty( $input['hide_when_offline'] ) ? '0' : '1';
	$output['quick_replies_enabled'] = empty( $input['quick_replies_enabled'] ) ? '0' : '1';
	$output['quick_1_text']         = isset( $input['quick_1_text'] ) ? sanitize_text_field( wp_unslash( $input['quick_1_text'] ) ) : $defaults['quick_1_text'];
	$output['quick_1_url']          = isset( $input['quick_1_url'] ) ? esc_url_raw( wp_unslash( $input['quick_1_url'] ) ) : '';
	$output['quick_2_text']         = isset( $input['quick_2_text'] ) ? sanitize_text_field( wp_unslash( $input['quick_2_text'] ) ) : $defaults['quick_2_text'];
	$output['quick_2_url']          = isset( $input['quick_2_url'] ) ? esc_url_raw( wp_unslash( $input['quick_2_url'] ) ) : '';
	$output['quick_3_text']         = isset( $input['quick_3_text'] ) ? sanitize_text_field( wp_unslash( $input['quick_3_text'] ) ) : $defaults['quick_3_text'];
	$output['quick_3_url']          = isset( $input['quick_3_url'] ) ? esc_url_raw( wp_unslash( $input['quick_3_url'] ) ) : '';
	$output['secondary_enabled']    = empty( $input['secondary_enabled'] ) ? '0' : '1';
	$output['secondary_name']       = isset( $input['secondary_name'] ) ? sanitize_text_field( wp_unslash( $input['secondary_name'] ) ) : $defaults['secondary_name'];
	$output['secondary_title']      = isset( $input['secondary_title'] ) ? sanitize_text_field( wp_unslash( $input['secondary_title'] ) ) : $defaults['secondary_title'];
	$output['secondary_url']        = isset( $input['secondary_url'] ) ? esc_url_raw( wp_unslash( $input['secondary_url'] ) ) : '';
	$output['secondary_photo']      = isset( $input['secondary_photo'] ) ? esc_url_raw( wp_unslash( $input['secondary_photo'] ) ) : $defaults['secondary_photo'];
	$output['third_enabled']        = empty( $input['third_enabled'] ) ? '0' : '1';
	$output['third_name']           = isset( $input['third_name'] ) ? sanitize_text_field( wp_unslash( $input['third_name'] ) ) : $defaults['third_name'];
	$output['third_title']          = isset( $input['third_title'] ) ? sanitize_text_field( wp_unslash( $input['third_title'] ) ) : $defaults['third_title'];
	$output['third_url']            = isset( $input['third_url'] ) ? esc_url_raw( wp_unslash( $input['third_url'] ) ) : '';
	$output['third_photo']          = isset( $input['third_photo'] ) ? esc_url_raw( wp_unslash( $input['third_photo'] ) ) : $defaults['third_photo'];

	return $output;
}

function ramisa_online_chat_activate() {
	ramisa_online_chat_create_table();
	ramisa_online_chat_migrate_legacy_option();

	if ( array() === ramisa_online_chat_read_stored_options() ) {
		ramisa_online_chat_save_stored_options( ramisa_online_chat_default_options() );
	}
}
register_activation_hook( __FILE__, 'ramisa_online_chat_activate' );

function ramisa_online_chat_admin_storage_check() {
	ramisa_online_chat_prepare_storage();
}
add_action( 'admin_init', 'ramisa_online_chat_admin_storage_check', 5 );

function ramisa_online_chat_load_textdomain() {
	load_plugin_textdomain( 'ramisa-online-chat', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'ramisa_online_chat_load_textdomain' );

function ramisa_online_chat_action_links( $links ) {
	$settings_url = admin_url( 'admin.php?page=' . RAMISA_ONLINE_CHAT_MENU_SLUG );
	$links[]      = '<a href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Settings', 'ramisa-online-chat' ) . '</a>';
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ramisa_online_chat_action_links' );

function ramisa_online_chat_handle_save_settings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to save these settings.', 'ramisa-online-chat' ) );
	}

	check_admin_referer( 'ramisa_online_chat_save_settings', 'ramisa_online_chat_nonce' );

	$raw_input = array();
	if ( isset( $_POST[ RAMISA_ONLINE_CHAT_OPTION ] ) ) {
		$posted_options = wp_unslash( $_POST[ RAMISA_ONLINE_CHAT_OPTION ] );
		$raw_input      = is_array( $posted_options ) ? $posted_options : array();
	}

	$options = ramisa_online_chat_sanitize_options( $raw_input );
	ramisa_online_chat_save_stored_options( $options );
	delete_option( RAMISA_ONLINE_CHAT_OPTION );

	$redirect_url = add_query_arg(
		array(
			'page'             => RAMISA_ONLINE_CHAT_MENU_SLUG,
			'settings-updated' => 'true',
		),
		admin_url( 'admin.php' )
	);

	wp_safe_redirect( $redirect_url );
	exit;
}
add_action( 'admin_post_ramisa_online_chat_save_settings', 'ramisa_online_chat_handle_save_settings' );

function ramisa_online_chat_admin_menu() {
	add_menu_page(
		esc_html__( 'Ramisa Online Chat', 'ramisa-online-chat' ),
		esc_html__( 'Ramisa Chat', 'ramisa-online-chat' ),
		'manage_options',
		RAMISA_ONLINE_CHAT_MENU_SLUG,
		'ramisa_online_chat_render_settings_page',
		'dashicons-format-chat',
		56
	);
}
add_action( 'admin_menu', 'ramisa_online_chat_admin_menu' );

function ramisa_online_chat_admin_assets( $hook_suffix ) {
	if ( 'toplevel_page_' . RAMISA_ONLINE_CHAT_MENU_SLUG !== $hook_suffix ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_style( 'ramisa-online-chat-admin', RAMISA_ONLINE_CHAT_URL . 'assets/css/ramisa-online-chat-admin.css', array(), RAMISA_ONLINE_CHAT_VERSION );
	wp_enqueue_script( 'ramisa-online-chat-admin', RAMISA_ONLINE_CHAT_URL . 'assets/js/ramisa-online-chat-admin.js', array( 'jquery' ), RAMISA_ONLINE_CHAT_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'ramisa_online_chat_admin_assets' );

function ramisa_online_chat_enqueue_frontend_assets() {
	static $enqueued = false;
	if ( $enqueued ) {
		return;
	}

	$enqueued = true;
	wp_enqueue_style( 'ramisa-online-chat', RAMISA_ONLINE_CHAT_URL . 'assets/css/ramisa-online-chat.css', array(), RAMISA_ONLINE_CHAT_VERSION );
	wp_enqueue_script( 'ramisa-online-chat', RAMISA_ONLINE_CHAT_URL . 'assets/js/ramisa-online-chat.js', array(), RAMISA_ONLINE_CHAT_VERSION, true );
}

function ramisa_online_chat_frontend_assets() {
	$options = ramisa_online_chat_get_options();
	if ( '1' === $options['enabled'] ) {
		ramisa_online_chat_enqueue_frontend_assets();
	}
}
add_action( 'wp_enqueue_scripts', 'ramisa_online_chat_frontend_assets' );

function ramisa_online_chat_field_name( $key ) {
	return RAMISA_ONLINE_CHAT_OPTION . '[' . $key . ']';
}

function ramisa_online_chat_array_field_name( $key, $sub_key ) {
	return RAMISA_ONLINE_CHAT_OPTION . '[' . $key . '][' . $sub_key . ']';
}

function ramisa_online_chat_nested_array_field_name( $key, $sub_key, $field_key ) {
	return RAMISA_ONLINE_CHAT_OPTION . '[' . $key . '][' . $sub_key . '][' . $field_key . ']';
}

function ramisa_online_chat_checked_label( $key, $label, $description = '' ) {
	$options = ramisa_online_chat_get_options();
	?>
	<label class="ramisa-admin-switch-row">
		<input type="checkbox" name="<?php echo esc_attr( ramisa_online_chat_field_name( $key ) ); ?>" value="1" <?php checked( $options[ $key ], '1' ); ?>>
		<span class="ramisa-admin-switch" aria-hidden="true"></span>
		<span class="ramisa-admin-switch-text">
			<strong><?php echo esc_html( $label ); ?></strong>
			<?php if ( '' !== $description ) : ?>
				<em><?php echo esc_html( $description ); ?></em>
			<?php endif; ?>
		</span>
	</label>
	<?php
}

function ramisa_online_chat_select_field( $key, $label, $choices, $extra_class = '' ) {
	$options = ramisa_online_chat_get_options();
	?>
	<div class="ramisa-admin-field <?php echo esc_attr( $extra_class ); ?>">
		<label for="ramisa_online_chat_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
		<select id="ramisa_online_chat_<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( ramisa_online_chat_field_name( $key ) ); ?>" class="ramisa-admin-live-select" data-preview-target="<?php echo esc_attr( $key ); ?>">
			<?php foreach ( $choices as $choice_key => $choice_label ) : ?>
				<option value="<?php echo esc_attr( $choice_key ); ?>" <?php selected( $options[ $key ], $choice_key ); ?>><?php echo esc_html( $choice_label ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<?php
}

function ramisa_online_chat_media_field( $key, $label, $preview_target ) {
	$options = ramisa_online_chat_get_options();
	?>
	<div class="ramisa-admin-field">
		<label for="ramisa_online_chat_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
		<div class="ramisa-admin-media-row">
			<input type="url" class="regular-text ltr ramisa-admin-live-input" data-preview-target="<?php echo esc_attr( $preview_target ); ?>" id="ramisa_online_chat_<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( ramisa_online_chat_field_name( $key ) ); ?>" value="<?php echo esc_attr( $options[ $key ] ); ?>">
			<button type="button" class="button ramisa-admin-media-button" data-target="ramisa_online_chat_<?php echo esc_attr( $key ); ?>"><?php echo esc_html__( 'Choose image', 'ramisa-online-chat' ); ?></button>
		</div>
	</div>
	<?php
}

function ramisa_online_chat_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to access this page.', 'ramisa-online-chat' ) );
	}

	$options       = ramisa_online_chat_get_options();
	$plugin_header = ramisa_online_chat_get_plugin_header();
	$link_status   = ramisa_online_chat_get_destination_status( $options );
	$nav_items     = array(
		'ramisa-general'      => array( 'icon' => 'dashicons-admin-comments', 'title' => __( 'General', 'ramisa-online-chat' ), 'desc' => __( 'Destination and visibility', 'ramisa-online-chat' ) ),
		'ramisa-profile'      => array( 'icon' => 'dashicons-businessperson', 'title' => __( 'Support profile', 'ramisa-online-chat' ), 'desc' => __( 'Identity and welcome text', 'ramisa-online-chat' ) ),
		'ramisa-appearance'   => array( 'icon' => 'dashicons-art', 'title' => __( 'Appearance', 'ramisa-online-chat' ), 'desc' => __( 'Style, shape, and motion', 'ramisa-online-chat' ) ),
		'ramisa-agents'       => array( 'icon' => 'dashicons-groups', 'title' => __( 'Agents', 'ramisa-online-chat' ), 'desc' => __( 'Additional contact options', 'ramisa-online-chat' ) ),
		'ramisa-availability' => array( 'icon' => 'dashicons-clock', 'title' => __( 'Working hours', 'ramisa-online-chat' ), 'desc' => __( 'Availability schedule', 'ramisa-online-chat' ) ),
		'ramisa-quick'        => array( 'icon' => 'dashicons-list-view', 'title' => __( 'Quick replies', 'ramisa-online-chat' ), 'desc' => __( 'Helpful shortcuts', 'ramisa-online-chat' ) ),
		'ramisa-preview'      => array( 'icon' => 'dashicons-visibility', 'title' => __( 'Preview', 'ramisa-online-chat' ), 'desc' => __( 'Preview and shortcodes', 'ramisa-online-chat' ) ),
	);
	?>
	<div class="wrap ramisa-online-chat-admin-wrap" dir="rtl">
		<div class="ramisa-admin-shell">
			<header class="ramisa-admin-hero">
				<div>
					<span class="ramisa-admin-kicker"><?php echo esc_html__( 'WordPress.org ready edition', 'ramisa-online-chat' ); ?></span>
					<h1><?php echo esc_html__( 'Ramisa Online Chat', 'ramisa-online-chat' ); ?></h1>
					<p><?php echo esc_html__( 'Create a refined floating chat experience with a lightweight native settings panel and transparent WordPress-friendly code.', 'ramisa-online-chat' ); ?></p>
				</div>
				<div class="ramisa-admin-hero-actions">
					<button type="button" class="ramisa-admin-soft-button" data-ramisa-jump="ramisa-general"><?php echo esc_html__( 'Edit widget', 'ramisa-online-chat' ); ?></button>
					<button type="button" class="ramisa-admin-soft-button ramisa-admin-soft-button-light" data-ramisa-jump="ramisa-preview"><?php echo esc_html__( 'Preview design', 'ramisa-online-chat' ); ?></button>
				</div>
			</header>

			<?php if ( isset( $_GET['settings-updated'] ) && 'true' === sanitize_text_field( wp_unslash( $_GET['settings-updated'] ) ) ) : ?>
				<div class="notice notice-success is-dismissible ramisa-admin-notice"><p><?php echo esc_html__( 'Ramisa settings were saved successfully.', 'ramisa-online-chat' ); ?></p></div>
			<?php endif; ?>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="ramisa-admin-layout">
				<input type="hidden" name="action" value="ramisa_online_chat_save_settings">
				<?php wp_nonce_field( 'ramisa_online_chat_save_settings', 'ramisa_online_chat_nonce' ); ?>

				<aside class="ramisa-admin-nav-wrap">
					<nav class="ramisa-admin-nav" aria-label="<?php echo esc_attr__( 'Ramisa settings sections', 'ramisa-online-chat' ); ?>">
						<?php foreach ( $nav_items as $panel_id => $item ) : ?>
							<button type="button" class="<?php echo 'ramisa-general' === $panel_id ? 'is-active' : ''; ?>" data-ramisa-tab="<?php echo esc_attr( $panel_id ); ?>" aria-controls="<?php echo esc_attr( $panel_id ); ?>" aria-selected="<?php echo 'ramisa-general' === $panel_id ? 'true' : 'false'; ?>">
								<span class="dashicons <?php echo esc_attr( $item['icon'] ); ?>" aria-hidden="true"></span>
								<strong><?php echo esc_html( $item['title'] ); ?></strong>
								<small><?php echo esc_html( $item['desc'] ); ?></small>
							</button>
						<?php endforeach; ?>
					</nav>

					<div class="ramisa-admin-nav-card ramisa-admin-brand-card">
						<span><?php echo esc_html__( 'Developer', 'ramisa-online-chat' ); ?></span>
						<a href="<?php echo esc_url( RAMISA_ONLINE_CHAT_DEVELOPER_URL ); ?>" target="_blank" rel="noopener noreferrer">SHABNAM.DEV</a>
						<small><?php echo esc_html__( 'Plugin version', 'ramisa-online-chat' ); ?>: <?php echo esc_html( $plugin_header['version'] ); ?></small>
					</div>

					<div class="ramisa-admin-nav-card ramisa-admin-status-card <?php echo $link_status['ready'] ? 'is-ready' : 'is-missing'; ?>" data-ramisa-connection-status>
						<span class="ramisa-admin-status-dot" aria-hidden="true"></span>
						<strong data-ramisa-status-label><?php echo esc_html( $link_status['label'] ); ?></strong>
						<small data-ramisa-status-message><?php echo esc_html( $link_status['message'] ); ?></small>
					</div>
				</aside>

				<main class="ramisa-admin-main">
					<section id="ramisa-general" class="ramisa-admin-card ramisa-admin-tab-panel is-active" data-ramisa-panel="ramisa-general">
						<div class="ramisa-admin-section-head">
							<span class="dashicons dashicons-admin-comments" aria-hidden="true"></span>
							<div>
								<h2><?php echo esc_html__( 'General settings', 'ramisa-online-chat' ); ?></h2>
								<p><?php echo esc_html__( 'Set the chat destination, visibility rules, and the labels visitors will see.', 'ramisa-online-chat' ); ?></p>
							</div>
						</div>

						<div class="ramisa-admin-options-grid">
							<?php ramisa_online_chat_checked_label( 'enabled', __( 'Enable the floating widget', 'ramisa-online-chat' ), __( 'Show the chat bubble on public pages.', 'ramisa-online-chat' ) ); ?>
							<?php ramisa_online_chat_checked_label( 'open_in_new_tab', __( 'Open chat links in a new tab', 'ramisa-online-chat' ), __( 'Keep the website page open while the chat link launches.', 'ramisa-online-chat' ) ); ?>
							<?php ramisa_online_chat_checked_label( 'desktop_visibility', __( 'Show on desktop', 'ramisa-online-chat' ), __( 'Enable the widget for desktop visitors.', 'ramisa-online-chat' ) ); ?>
							<?php ramisa_online_chat_checked_label( 'mobile_visibility', __( 'Show on mobile', 'ramisa-online-chat' ), __( 'Enable the widget for mobile visitors.', 'ramisa-online-chat' ) ); ?>
						</div>

						<div class="ramisa-admin-field">
							<label for="ramisa_online_chat_chat_url"><?php echo esc_html__( 'Primary chat URL', 'ramisa-online-chat' ); ?></label>
							<input type="url" class="ltr ramisa-admin-live-input ramisa-admin-connection-input" data-preview-target="link" id="ramisa_online_chat_chat_url" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'chat_url' ) ); ?>" value="<?php echo esc_attr( $options['chat_url'] ); ?>" placeholder="https://wa.me/989120000000">
							<p><?php echo esc_html__( 'Paste a full public chat URL. If this field is empty, Ramisa can create a WhatsApp link from the number below.', 'ramisa-online-chat' ); ?></p>
						</div>

						<div class="ramisa-admin-field">
							<label for="ramisa_online_chat_whatsapp_number"><?php echo esc_html__( 'WhatsApp number', 'ramisa-online-chat' ); ?></label>
							<input type="text" class="ltr ramisa-admin-connection-input" id="ramisa_online_chat_whatsapp_number" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'whatsapp_number' ) ); ?>" value="<?php echo esc_attr( $options['whatsapp_number'] ); ?>" placeholder="989120000000" inputmode="tel">
							<p><?php echo esc_html__( 'Use digits only, including the country code. This is used only to build a click-to-chat link.', 'ramisa-online-chat' ); ?></p>
						</div>

						<div class="ramisa-admin-grid-2">
							<div class="ramisa-admin-field"><label for="ramisa_online_chat_button_text"><?php echo esc_html__( 'Primary button text', 'ramisa-online-chat' ); ?></label><input type="text" class="ramisa-admin-live-input" data-preview-target="button" id="ramisa_online_chat_button_text" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'button_text' ) ); ?>" value="<?php echo esc_attr( $options['button_text'] ); ?>"></div>
							<div class="ramisa-admin-field"><label for="ramisa_online_chat_bubble_label"><?php echo esc_html__( 'Bubble helper label', 'ramisa-online-chat' ); ?></label><input type="text" class="ramisa-admin-live-input" data-preview-target="label" id="ramisa_online_chat_bubble_label" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'bubble_label' ) ); ?>" value="<?php echo esc_attr( $options['bubble_label'] ); ?>"></div>
						</div>
					</section>

					<section id="ramisa-profile" class="ramisa-admin-card ramisa-admin-tab-panel" data-ramisa-panel="ramisa-profile" hidden>
						<div class="ramisa-admin-section-head"><span class="dashicons dashicons-businessperson" aria-hidden="true"></span><div><h2><?php echo esc_html__( 'Support profile', 'ramisa-online-chat' ); ?></h2><p><?php echo esc_html__( 'Write the welcome content and the support identity shown inside the chat card.', 'ramisa-online-chat' ); ?></p></div></div>

						<div class="ramisa-admin-grid-2">
							<div class="ramisa-admin-field"><label for="ramisa_online_chat_bubble_title"><?php echo esc_html__( 'Chat card title', 'ramisa-online-chat' ); ?></label><input type="text" class="ramisa-admin-live-input" data-preview-target="card_title" id="ramisa_online_chat_bubble_title" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'bubble_title' ) ); ?>" value="<?php echo esc_attr( $options['bubble_title'] ); ?>"></div>
							<div class="ramisa-admin-field"><label for="ramisa_online_chat_bubble_subtitle"><?php echo esc_html__( 'Chat card subtitle', 'ramisa-online-chat' ); ?></label><input type="text" class="ramisa-admin-live-input" data-preview-target="card_subtitle" id="ramisa_online_chat_bubble_subtitle" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'bubble_subtitle' ) ); ?>" value="<?php echo esc_attr( $options['bubble_subtitle'] ); ?>"></div>
							<div class="ramisa-admin-field"><label for="ramisa_online_chat_agent_name"><?php echo esc_html__( 'Support name', 'ramisa-online-chat' ); ?></label><input type="text" class="ramisa-admin-live-input" data-preview-target="name" id="ramisa_online_chat_agent_name" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'agent_name' ) ); ?>" value="<?php echo esc_attr( $options['agent_name'] ); ?>"></div>
							<div class="ramisa-admin-field"><label for="ramisa_online_chat_agent_title"><?php echo esc_html__( 'Support role', 'ramisa-online-chat' ); ?></label><input type="text" class="ramisa-admin-live-input" data-preview-target="title" id="ramisa_online_chat_agent_title" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'agent_title' ) ); ?>" value="<?php echo esc_attr( $options['agent_title'] ); ?>"></div>
						</div>
						<div class="ramisa-admin-field"><label for="ramisa_online_chat_agent_message"><?php echo esc_html__( 'Welcome message', 'ramisa-online-chat' ); ?></label><textarea rows="4" class="ramisa-admin-live-input" data-preview-target="message" id="ramisa_online_chat_agent_message" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'agent_message' ) ); ?>"><?php echo esc_textarea( $options['agent_message'] ); ?></textarea></div>
						<?php ramisa_online_chat_media_field( 'agent_photo', __( 'Profile image', 'ramisa-online-chat' ), 'photo' ); ?>
					</section>

					<section id="ramisa-appearance" class="ramisa-admin-card ramisa-admin-tab-panel" data-ramisa-panel="ramisa-appearance" hidden>
						<div class="ramisa-admin-section-head"><span class="dashicons dashicons-art" aria-hidden="true"></span><div><h2><?php echo esc_html__( 'Appearance and motion', 'ramisa-online-chat' ); ?></h2><p><?php echo esc_html__( 'Adjust the visual style, motion, and bubble behavior from prepared options.', 'ramisa-online-chat' ); ?></p></div></div>
						<div class="ramisa-admin-grid-3">
							<?php ramisa_online_chat_select_field( 'theme', __( 'Color palette', 'ramisa-online-chat' ), ramisa_online_chat_allowed_themes() ); ?>
							<?php ramisa_online_chat_select_field( 'position', __( 'Screen position', 'ramisa-online-chat' ), array( 'left' => __( 'Left', 'ramisa-online-chat' ), 'right' => __( 'Right', 'ramisa-online-chat' ) ) ); ?>
							<?php ramisa_online_chat_select_field( 'animation', __( 'Opening animation', 'ramisa-online-chat' ), ramisa_online_chat_allowed_animations() ); ?>
							<?php ramisa_online_chat_select_field( 'button_size', __( 'Button size', 'ramisa-online-chat' ), ramisa_online_chat_allowed_sizes() ); ?>
							<?php ramisa_online_chat_select_field( 'button_shape', __( 'Button shape', 'ramisa-online-chat' ), ramisa_online_chat_allowed_shapes() ); ?>
							<?php ramisa_online_chat_select_field( 'card_style', __( 'Chat card style', 'ramisa-online-chat' ), ramisa_online_chat_allowed_cards() ); ?>
							<?php ramisa_online_chat_select_field( 'label_mode', __( 'Helper label display', 'ramisa-online-chat' ), ramisa_online_chat_allowed_label_modes() ); ?>
							<?php ramisa_online_chat_select_field( 'header_alignment', __( 'Header alignment', 'ramisa-online-chat' ), ramisa_online_chat_allowed_header_alignments() ); ?>
							<div class="ramisa-admin-field"><label for="ramisa_online_chat_icon"><?php echo esc_html__( 'Bubble icon', 'ramisa-online-chat' ); ?></label><select id="ramisa_online_chat_icon" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'icon' ) ); ?>" class="ramisa-admin-live-select" data-preview-target="icon"><?php foreach ( ramisa_online_chat_allowed_icons() as $icon_key => $icon_value ) : ?><option value="<?php echo esc_attr( $icon_key ); ?>" <?php selected( $options['icon'], $icon_key ); ?>><?php echo esc_html( $icon_value . ' ' . $icon_key ); ?></option><?php endforeach; ?></select></div>
						</div>
						<div class="ramisa-admin-options-grid">
							<?php ramisa_online_chat_checked_label( 'autoshow', __( 'Open the chat card automatically', 'ramisa-online-chat' ), __( 'Show the chat card shortly after the page has loaded.', 'ramisa-online-chat' ) ); ?>
							<?php ramisa_online_chat_checked_label( 'show_current_time', __( 'Show visitor time', 'ramisa-online-chat' ), __( 'Display the visitor’s current time inside the chat card.', 'ramisa-online-chat' ) ); ?>
							<?php ramisa_online_chat_checked_label( 'badge_enabled', __( 'Show badge', 'ramisa-online-chat' ), __( 'Display a small notification badge above the bubble.', 'ramisa-online-chat' ) ); ?>
							<div class="ramisa-admin-field"><label for="ramisa_online_chat_badge_text"><?php echo esc_html__( 'Badge text', 'ramisa-online-chat' ); ?></label><input type="text" id="ramisa_online_chat_badge_text" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'badge_text' ) ); ?>" value="<?php echo esc_attr( $options['badge_text'] ); ?>" maxlength="4"></div>
						</div>
					</section>

					<section id="ramisa-agents" class="ramisa-admin-card ramisa-admin-tab-panel" data-ramisa-panel="ramisa-agents" hidden>
						<div class="ramisa-admin-section-head"><span class="dashicons dashicons-groups" aria-hidden="true"></span><div><h2><?php echo esc_html__( 'Additional support agents', 'ramisa-online-chat' ); ?></h2><p><?php echo esc_html__( 'Add more contact options so visitors can choose the right person or department.', 'ramisa-online-chat' ); ?></p></div></div>
						<div class="ramisa-admin-options-grid">
							<?php ramisa_online_chat_select_field( 'agent_list_layout', __( 'Contact list layout', 'ramisa-online-chat' ), ramisa_online_chat_allowed_agent_layouts() ); ?>
							<?php ramisa_online_chat_checked_label( 'agent_search_enabled', __( 'Enable contact search', 'ramisa-online-chat' ), __( 'Let visitors filter the available contacts inside the chat card.', 'ramisa-online-chat' ) ); ?>
							<?php ramisa_online_chat_checked_label( 'show_agent_avatars', __( 'Show contact images', 'ramisa-online-chat' ), __( 'Display profile images beside the contact options.', 'ramisa-online-chat' ) ); ?>
						</div>
						<div class="ramisa-admin-agent-grid">
							<div class="ramisa-admin-agent-box">
								<?php ramisa_online_chat_checked_label( 'secondary_enabled', __( 'Enable second contact', 'ramisa-online-chat' ), __( 'Show an additional contact option in the chat card.', 'ramisa-online-chat' ) ); ?>
								<div class="ramisa-admin-grid-2"><div class="ramisa-admin-field"><label for="ramisa_online_chat_secondary_name"><?php echo esc_html__( 'Name', 'ramisa-online-chat' ); ?></label><input type="text" id="ramisa_online_chat_secondary_name" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'secondary_name' ) ); ?>" value="<?php echo esc_attr( $options['secondary_name'] ); ?>"></div><div class="ramisa-admin-field"><label for="ramisa_online_chat_secondary_title"><?php echo esc_html__( 'Title', 'ramisa-online-chat' ); ?></label><input type="text" id="ramisa_online_chat_secondary_title" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'secondary_title' ) ); ?>" value="<?php echo esc_attr( $options['secondary_title'] ); ?>"></div></div>
								<div class="ramisa-admin-field"><label for="ramisa_online_chat_secondary_url"><?php echo esc_html__( 'Contact URL', 'ramisa-online-chat' ); ?></label><input type="url" class="ltr" id="ramisa_online_chat_secondary_url" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'secondary_url' ) ); ?>" value="<?php echo esc_attr( $options['secondary_url'] ); ?>"></div>
								<?php ramisa_online_chat_media_field( 'secondary_photo', __( 'Profile image', 'ramisa-online-chat' ), 'secondary_photo' ); ?>
							</div>

							<div class="ramisa-admin-agent-box">
								<?php ramisa_online_chat_checked_label( 'third_enabled', __( 'Enable third contact', 'ramisa-online-chat' ), __( 'Show another contact option in the chat card.', 'ramisa-online-chat' ) ); ?>
								<div class="ramisa-admin-grid-2"><div class="ramisa-admin-field"><label for="ramisa_online_chat_third_name"><?php echo esc_html__( 'Name', 'ramisa-online-chat' ); ?></label><input type="text" id="ramisa_online_chat_third_name" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'third_name' ) ); ?>" value="<?php echo esc_attr( $options['third_name'] ); ?>"></div><div class="ramisa-admin-field"><label for="ramisa_online_chat_third_title"><?php echo esc_html__( 'Title', 'ramisa-online-chat' ); ?></label><input type="text" id="ramisa_online_chat_third_title" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'third_title' ) ); ?>" value="<?php echo esc_attr( $options['third_title'] ); ?>"></div></div>
								<div class="ramisa-admin-field"><label for="ramisa_online_chat_third_url"><?php echo esc_html__( 'Contact URL', 'ramisa-online-chat' ); ?></label><input type="url" class="ltr" id="ramisa_online_chat_third_url" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'third_url' ) ); ?>" value="<?php echo esc_attr( $options['third_url'] ); ?>"></div>
								<?php ramisa_online_chat_media_field( 'third_photo', __( 'Profile image', 'ramisa-online-chat' ), 'third_photo' ); ?>
							</div>
						</div>
					</section>

					<section id="ramisa-availability" class="ramisa-admin-card ramisa-admin-tab-panel" data-ramisa-panel="ramisa-availability" hidden>
						<div class="ramisa-admin-section-head"><span class="dashicons dashicons-clock" aria-hidden="true"></span><div><h2><?php echo esc_html__( 'Working hours and availability', 'ramisa-online-chat' ); ?></h2><p><?php echo esc_html__( 'Choose one timezone and define clear availability for each day of the week.', 'ramisa-online-chat' ); ?></p></div></div>
						<div class="ramisa-admin-options-grid">
							<?php ramisa_online_chat_checked_label( 'availability_enabled', __( 'Enable scheduled availability', 'ramisa-online-chat' ), __( 'Change the status text automatically according to the weekly schedule.', 'ramisa-online-chat' ) ); ?>
							<?php ramisa_online_chat_checked_label( 'hide_when_offline', __( 'Hide the widget outside working hours', 'ramisa-online-chat' ), __( 'Keep the chat bubble hidden when your team is unavailable.', 'ramisa-online-chat' ) ); ?>
						</div>
						<div class="ramisa-admin-field">
							<label for="ramisa_online_chat_timezone"><?php echo esc_html__( 'Timezone', 'ramisa-online-chat' ); ?></label>
							<select id="ramisa_online_chat_timezone" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'timezone' ) ); ?>" class="ramisa-admin-select-scroll ltr">
								<?php foreach ( timezone_identifiers_list() as $timezone ) : ?>
									<option value="<?php echo esc_attr( $timezone ); ?>" <?php selected( $options['timezone'], $timezone ); ?>><?php echo esc_html( $timezone ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="ramisa-admin-weekly-hours">
							<?php foreach ( ramisa_online_chat_weekdays() as $day_key => $day_label ) : ?>
								<?php $day_hours = isset( $options['weekly_hours'][ $day_key ] ) && is_array( $options['weekly_hours'][ $day_key ] ) ? $options['weekly_hours'][ $day_key ] : array( 'enabled' => '0', 'start' => '09:00', 'end' => '18:00' ); ?>
								<div class="ramisa-admin-day-row">
									<label class="ramisa-admin-day-toggle"><input type="checkbox" name="<?php echo esc_attr( ramisa_online_chat_array_field_name( 'workdays', $day_key ) ); ?>" value="1" <?php checked( $day_hours['enabled'], '1' ); ?>><span><?php echo esc_html( $day_label ); ?></span></label>
									<label><span><?php echo esc_html__( 'From', 'ramisa-online-chat' ); ?></span><input type="time" name="<?php echo esc_attr( ramisa_online_chat_nested_array_field_name( 'weekly_hours', $day_key, 'start' ) ); ?>" value="<?php echo esc_attr( $day_hours['start'] ); ?>"></label>
									<label><span><?php echo esc_html__( 'To', 'ramisa-online-chat' ); ?></span><input type="time" name="<?php echo esc_attr( ramisa_online_chat_nested_array_field_name( 'weekly_hours', $day_key, 'end' ) ); ?>" value="<?php echo esc_attr( $day_hours['end'] ); ?>"></label>
								</div>
							<?php endforeach; ?>
						</div>
						<div class="ramisa-admin-grid-2"><div class="ramisa-admin-field"><label for="ramisa_online_chat_online_text"><?php echo esc_html__( 'Online text', 'ramisa-online-chat' ); ?></label><input type="text" id="ramisa_online_chat_online_text" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'online_text' ) ); ?>" value="<?php echo esc_attr( $options['online_text'] ); ?>"></div><div class="ramisa-admin-field"><label for="ramisa_online_chat_offline_text"><?php echo esc_html__( 'Offline text', 'ramisa-online-chat' ); ?></label><input type="text" id="ramisa_online_chat_offline_text" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'offline_text' ) ); ?>" value="<?php echo esc_attr( $options['offline_text'] ); ?>"></div></div>
						<div class="ramisa-admin-field"><label for="ramisa_online_chat_offline_message"><?php echo esc_html__( 'Offline message', 'ramisa-online-chat' ); ?></label><textarea rows="3" id="ramisa_online_chat_offline_message" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'offline_message' ) ); ?>"><?php echo esc_textarea( $options['offline_message'] ); ?></textarea></div>
					</section>

					<section id="ramisa-quick" class="ramisa-admin-card ramisa-admin-tab-panel" data-ramisa-panel="ramisa-quick" hidden>
						<div class="ramisa-admin-section-head"><span class="dashicons dashicons-list-view" aria-hidden="true"></span><div><h2><?php echo esc_html__( 'Quick reply buttons', 'ramisa-online-chat' ); ?></h2><p><?php echo esc_html__( 'Add short, helpful links below the welcome message.', 'ramisa-online-chat' ); ?></p></div></div>
						<?php ramisa_online_chat_checked_label( 'quick_replies_enabled', __( 'Enable quick reply buttons', 'ramisa-online-chat' ), __( 'Show the extra links beneath the welcome message.', 'ramisa-online-chat' ) ); ?>
						<div class="ramisa-admin-repeat-grid">
							<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
								<div class="ramisa-admin-repeat-item"><strong><?php echo esc_html__( 'Quick button', 'ramisa-online-chat' ) . ' ' . esc_html( (string) $i ); ?></strong><label for="ramisa_online_chat_quick_<?php echo esc_attr( (string) $i ); ?>_text"><?php echo esc_html__( 'Text', 'ramisa-online-chat' ); ?></label><input type="text" id="ramisa_online_chat_quick_<?php echo esc_attr( (string) $i ); ?>_text" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'quick_' . $i . '_text' ) ); ?>" value="<?php echo esc_attr( $options[ 'quick_' . $i . '_text' ] ); ?>"><label for="ramisa_online_chat_quick_<?php echo esc_attr( (string) $i ); ?>_url"><?php echo esc_html__( 'URL', 'ramisa-online-chat' ); ?></label><input type="url" class="ltr" id="ramisa_online_chat_quick_<?php echo esc_attr( (string) $i ); ?>_url" name="<?php echo esc_attr( ramisa_online_chat_field_name( 'quick_' . $i . '_url' ) ); ?>" value="<?php echo esc_attr( $options[ 'quick_' . $i . '_url' ] ); ?>"></div>
							<?php endfor; ?>
						</div>
					</section>

					<section id="ramisa-preview" class="ramisa-admin-card ramisa-admin-tab-panel" data-ramisa-panel="ramisa-preview" hidden>
						<div class="ramisa-admin-section-head"><span class="dashicons dashicons-visibility" aria-hidden="true"></span><div><h2><?php echo esc_html__( 'Preview and shortcodes', 'ramisa-online-chat' ); ?></h2><p><?php echo esc_html__( 'Review the widget content and copy shortcodes for posts, pages, or templates.', 'ramisa-online-chat' ); ?></p></div></div>
						<div class="ramisa-admin-preview-grid">
							<div class="ramisa-admin-preview-card ramisa-admin-preview-theme-<?php echo esc_attr( $options['theme'] ); ?>">
								<div class="ramisa-admin-preview-chat">
									<h3 data-preview-output="card_title"><?php echo esc_html( $options['bubble_title'] ); ?></h3>
									<p class="ramisa-admin-preview-subtitle" data-preview-output="card_subtitle"><?php echo esc_html( $options['bubble_subtitle'] ); ?></p>
									<div class="ramisa-admin-preview-header"><img src="<?php echo esc_url( $options['agent_photo'] ); ?>" alt="" data-preview-output="photo"><div><strong data-preview-output="name"><?php echo esc_html( $options['agent_name'] ); ?></strong><small data-preview-output="title"><?php echo esc_html( $options['agent_title'] ); ?></small></div></div>
									<p data-preview-output="message"><?php echo esc_html( $options['agent_message'] ); ?></p><a href="#" data-preview-output="button"><?php echo esc_html( $options['button_text'] ); ?></a>
								</div>
								<div class="ramisa-admin-preview-floating"><span data-preview-output="label"><?php echo esc_html( $options['bubble_label'] ); ?></span><button type="button" aria-hidden="true" data-preview-output="icon"><?php echo esc_html( ramisa_online_chat_allowed_icons()[ $options['icon'] ] ); ?></button></div>
							</div>
							<div class="ramisa-admin-shortcodes"><h3><?php echo esc_html__( 'Preview and shortcodes', 'ramisa-online-chat' ); ?></h3><code>[ramisa_online_chat]</code><code>[ramisa_online_chat_button]</code><code>[ramisa_online_chat text="Support" theme="blue"]</code><p><?php echo esc_html__( 'Use these shortcodes in posts, pages, or template areas.', 'ramisa-online-chat' ); ?></p></div>
						</div>
					</section>

					<div class="ramisa-admin-actions">
						<?php submit_button( __( 'Save changes', 'ramisa-online-chat' ), 'primary large', 'submit', false ); ?>
					</div>
				</main>
			</form>
		</div>
	</div>
	<?php
}

function ramisa_online_chat_get_icon( $icon_key ) {
	$icons = ramisa_online_chat_allowed_icons();
	return isset( $icons[ $icon_key ] ) ? $icons[ $icon_key ] : $icons['chat'];
}

function ramisa_online_chat_get_status( $options ) {
	$status = array(
		'online'  => true,
		'label'   => $options['online_text'],
		'message' => $options['agent_message'],
	);

	if ( '1' !== $options['availability_enabled'] ) {
		return $status;
	}

	try {
		$timezone = new DateTimeZone( $options['timezone'] );
		$now      = new DateTimeImmutable( 'now', $timezone );
	} catch ( Exception $e ) {
		$now = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
	}

	$day_key   = strtolower( $now->format( 'l' ) );
	$day_hours = isset( $options['weekly_hours'][ $day_key ] ) && is_array( $options['weekly_hours'][ $day_key ] ) ? $options['weekly_hours'][ $day_key ] : array();
	$workday   = isset( $day_hours['enabled'] ) ? '1' === $day_hours['enabled'] : ( isset( $options['workdays'][ $day_key ] ) && '1' === $options['workdays'][ $day_key ] );
	$start     = isset( $day_hours['start'] ) ? $day_hours['start'] : $options['work_start'];
	$end       = isset( $day_hours['end'] ) ? $day_hours['end'] : $options['work_end'];
	$now_minutes = (int) $now->format( 'H' ) * 60 + (int) $now->format( 'i' );
	list( $start_hour, $start_minute ) = array_map( 'intval', explode( ':', $start ) );
	list( $end_hour, $end_minute )     = array_map( 'intval', explode( ':', $end ) );
	$start_minutes = $start_hour * 60 + $start_minute;
	$end_minutes   = $end_hour * 60 + $end_minute;

	if ( $start_minutes <= $end_minutes ) {
		$in_time = $now_minutes >= $start_minutes && $now_minutes <= $end_minutes;
	} else {
		$in_time = $now_minutes >= $start_minutes || $now_minutes <= $end_minutes;
	}

	if ( ! $workday || ! $in_time ) {
		$status['online']  = false;
		$status['label']   = $options['offline_text'];
		$status['message'] = $options['offline_message'];
	}

	return $status;
}

function ramisa_online_chat_get_agent_actions( $options ) {
	$actions     = array();
	$primary_url = ramisa_online_chat_get_primary_chat_url( $options );
	if ( ! empty( $primary_url ) ) {
		$actions[] = array(
			'name'  => $options['agent_name'],
			'title' => $options['agent_title'],
			'url'   => $primary_url,
			'photo' => $options['agent_photo'],
			'main'  => true,
		);
	}

	if ( '1' === $options['secondary_enabled'] && ! empty( $options['secondary_url'] ) ) {
		$actions[] = array(
			'name'  => $options['secondary_name'],
			'title' => $options['secondary_title'],
			'url'   => $options['secondary_url'],
			'photo' => $options['secondary_photo'],
			'main'  => false,
		);
	}

	if ( '1' === $options['third_enabled'] && ! empty( $options['third_url'] ) ) {
		$actions[] = array(
			'name'  => $options['third_name'],
			'title' => $options['third_title'],
			'url'   => $options['third_url'],
			'photo' => $options['third_photo'],
			'main'  => false,
		);
	}

	return $actions;
}

function ramisa_online_chat_get_quick_replies( $options ) {
	$items = array();
	if ( '1' !== $options['quick_replies_enabled'] ) {
		return $items;
	}

	for ( $i = 1; $i <= 3; $i++ ) {
		$text = $options[ 'quick_' . $i . '_text' ];
		$url  = $options[ 'quick_' . $i . '_url' ];
		if ( '' !== $text && '' !== $url ) {
			$items[] = array(
				'text' => $text,
				'url'  => $url,
			);
		}
	}

	return $items;
}

function ramisa_online_chat_render_button( $atts = array(), $inline = false ) {
	$options = ramisa_online_chat_get_options();
	$atts    = shortcode_atts(
		array(
			'url'   => '',
			'text'  => '',
			'theme' => '',
			'icon'  => '',
			'size'  => '',
		),
		$atts,
		'ramisa_online_chat'
	);

	ramisa_online_chat_enqueue_frontend_assets();
	$chat_url = ! empty( $atts['url'] ) ? esc_url_raw( $atts['url'] ) : ramisa_online_chat_get_primary_chat_url( $options );
	$text     = ! empty( $atts['text'] ) ? sanitize_text_field( $atts['text'] ) : $options['button_text'];
	$theme    = ! empty( $atts['theme'] ) ? ramisa_online_chat_sanitize_choice( $atts['theme'], ramisa_online_chat_allowed_themes(), $options['theme'] ) : $options['theme'];
	$icon     = ! empty( $atts['icon'] ) ? ramisa_online_chat_sanitize_choice( $atts['icon'], ramisa_online_chat_allowed_icons(), $options['icon'] ) : $options['icon'];
	$size     = ! empty( $atts['size'] ) ? ramisa_online_chat_sanitize_choice( $atts['size'], ramisa_online_chat_allowed_sizes(), $options['button_size'] ) : $options['button_size'];
	$classes  = array( 'ramisa-online-chat-inline-button', 'ramisa-online-chat-theme-' . $theme, 'ramisa-online-chat-inline-' . $size );
	$url      = $chat_url ? $chat_url : '#';

	ob_start();
	?>
	<a class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" href="<?php echo esc_url( $url ); ?>"<?php if ( '1' === $options['open_in_new_tab'] && '#' !== $url ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?>>
		<span class="ramisa-online-chat-inline-icon" aria-hidden="true"><?php echo esc_html( ramisa_online_chat_get_icon( $icon ) ); ?></span>
		<span><?php echo esc_html( $text ); ?></span>
	</a>
	<?php
	return ob_get_clean();
}

function ramisa_online_chat_shortcode( $atts ) {
	return ramisa_online_chat_render_button( $atts, true );
}
add_shortcode( 'ramisa_online_chat', 'ramisa_online_chat_shortcode' );
add_shortcode( 'ramisa_online_chat_button', 'ramisa_online_chat_shortcode' );

function ramisa_online_chat_render_floating_button() {
	$options = ramisa_online_chat_get_options();
	if ( '1' !== $options['enabled'] ) {
		return;
	}

	$status = ramisa_online_chat_get_status( $options );
	if ( ! $status['online'] && '1' === $options['hide_when_offline'] ) {
		return;
	}

	$visibility = array();
	if ( '1' !== $options['desktop_visibility'] ) {
		$visibility[] = 'ramisa-online-chat-hide-desktop';
	}
	if ( '1' !== $options['mobile_visibility'] ) {
		$visibility[] = 'ramisa-online-chat-hide-mobile';
	}

	$classes = array_merge(
		array(
			'ramisa-online-chat-widget',
			'ramisa-online-chat-position-' . $options['position'],
			'ramisa-online-chat-theme-' . $options['theme'],
			'ramisa-online-chat-animation-' . $options['animation'],
			'ramisa-online-chat-size-' . $options['button_size'],
			'ramisa-online-chat-shape-' . $options['button_shape'],
			'ramisa-online-chat-card-' . $options['card_style'],
			'ramisa-online-chat-label-' . $options['label_mode'],
			'ramisa-online-chat-header-' . $options['header_alignment'],
			'ramisa-online-chat-agent-layout-' . $options['agent_list_layout'],
			'1' === $options['show_agent_avatars'] ? 'ramisa-online-chat-show-avatars' : 'ramisa-online-chat-hide-avatars',
			$status['online'] ? 'ramisa-online-chat-is-online' : 'ramisa-online-chat-is-offline',
		),
		$visibility
	);

	$panel_id        = wp_unique_id( 'ramisa-online-chat-panel-' );
	$should_autoshow = '1' === $options['autoshow'] ? '1' : '0';
	$actions         = ramisa_online_chat_get_agent_actions( $options );
	$quick_replies   = ramisa_online_chat_get_quick_replies( $options );
	?>
	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-ramisa-online-chat data-autoshow="<?php echo esc_attr( $should_autoshow ); ?>">
		<button class="ramisa-online-chat-toggle" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $panel_id ); ?>" aria-label="<?php echo esc_attr__( 'Open chat', 'ramisa-online-chat' ); ?>">
			<span class="ramisa-online-chat-toggle-ring" aria-hidden="true"></span>
			<?php if ( '1' === $options['badge_enabled'] ) : ?>
				<span class="ramisa-online-chat-notice-dot" aria-hidden="true"><?php echo esc_html( $options['badge_text'] ); ?></span>
			<?php endif; ?>
			<span class="ramisa-online-chat-toggle-open" aria-hidden="true"><?php echo esc_html( ramisa_online_chat_get_icon( $options['icon'] ) ); ?></span>
			<span class="ramisa-online-chat-toggle-close" aria-hidden="true">×</span>
		</button>

		<div class="ramisa-online-chat-panel" id="<?php echo esc_attr( $panel_id ); ?>" role="dialog" aria-label="<?php echo esc_attr__( 'Online chat', 'ramisa-online-chat' ); ?>">
			<div class="ramisa-online-chat-header">
				<div class="ramisa-online-chat-avatar">
					<img src="<?php echo esc_url( $options['agent_photo'] ); ?>" alt="<?php echo esc_attr( $options['agent_name'] ); ?>">
				</div>
				<div>
					<span class="ramisa-online-chat-status"><?php echo esc_html( $status['label'] ); ?></span>
					<h3><?php echo esc_html( $options['agent_name'] ); ?></h3>
					<p><?php echo esc_html( $options['agent_title'] ); ?></p>
				</div>
			</div>

			<div class="ramisa-online-chat-body">
				<div class="ramisa-online-chat-intro">
					<h4><?php echo esc_html( $options['bubble_title'] ); ?></h4>
					<p><?php echo esc_html( $options['bubble_subtitle'] ); ?></p>
				</div>

				<?php if ( '1' === $options['show_current_time'] ) : ?>
					<div class="ramisa-online-chat-time" data-ramisa-online-chat-time></div>
				<?php endif; ?>
				<p class="ramisa-online-chat-message"><?php echo esc_html( $status['message'] ); ?></p>

				<?php if ( ! empty( $actions ) ) : ?>
					<?php if ( '1' === $options['agent_search_enabled'] && count( $actions ) > 1 ) : ?>
						<label class="ramisa-online-chat-search">
							<span class="screen-reader-text"><?php echo esc_html__( 'Search support contacts', 'ramisa-online-chat' ); ?></span>
							<input type="search" data-ramisa-agent-search placeholder="<?php echo esc_attr__( 'Search support contacts', 'ramisa-online-chat' ); ?>">
						</label>
					<?php endif; ?>

					<div class="ramisa-online-chat-action-list">
						<?php foreach ( $actions as $action ) : ?>
							<?php
							$action_class = 'ramisa-online-chat-agent-action';
							if ( ! empty( $action['main'] ) ) {
								$action_class .= ' ramisa-online-chat-agent-action-main';
							}
							?>
							<a class="<?php echo esc_attr( $action_class ); ?>" href="<?php echo esc_url( $action['url'] ); ?>" data-ramisa-agent-action data-search-text="<?php echo esc_attr( $action['name'] . ' ' . $action['title'] ); ?>"<?php if ( '1' === $options['open_in_new_tab'] ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?>>
								<?php if ( '1' === $options['show_agent_avatars'] ) : ?>
									<img src="<?php echo esc_url( $action['photo'] ); ?>" alt="">
								<?php endif; ?>
								<span><strong><?php echo esc_html( $action['name'] ); ?></strong><small><?php echo esc_html( $action['title'] ); ?></small></span>
							</a>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<a class="ramisa-online-chat-send" href="#"><?php echo esc_html( $options['button_text'] ); ?></a>
				<?php endif; ?>

				<?php if ( ! empty( $quick_replies ) ) : ?>
					<div class="ramisa-online-chat-quick-list">
						<?php foreach ( $quick_replies as $reply ) : ?>
							<a href="<?php echo esc_url( $reply['url'] ); ?>"<?php if ( '1' === $options['open_in_new_tab'] ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?>><?php echo esc_html( $reply['text'] ); ?></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="ramisa-online-chat-label"><?php echo esc_html( $options['bubble_label'] ); ?></div>
	</div>
	<?php
}
add_action( 'wp_footer', 'ramisa_online_chat_render_floating_button' );
