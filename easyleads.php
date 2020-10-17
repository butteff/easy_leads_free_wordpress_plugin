<?php

/**
 * @package easyleads
 */
/*
Plugin Name: Easy Leads Free
Plugin URI: https://github.com/butteff/easy_leads_free_wordpress_plugin
Description: Easy Leads Free - collect leads and contacts from your website to the database. Send mails to your leads after from the admin panel.
Version: 1.0.0
Author: Butteff
Author URI: https://cvmkr.com/Ky4Z
License: GPLv2 or later
Text Domain: Easy Leads
*/

/*
"Easy Leads Free" is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
"Easy Leads Free" is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
*/

/**
 * Activate the plugin and create the database table;
 */

function easyleads_activate() { 
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	$table_one = "CREATE TABLE `{$wpdb->base_prefix}easyleads` (
	  id integer(11) NOT NULL AUTO_INCREMENT,
	  name varchar(255) NOT NULL,
	  mail varchar(255) NOT NULL,
	  phone varchar(255) NOT NULL,
	  created_at datetime NOT NULL,
	  sent_at datetime DEFAULT NULL,
	  is_sent boolean DEFAULT 0,
	  PRIMARY KEY  (id)
	) $charset_collate;";

	$table_two = "CREATE TABLE `{$wpdb->base_prefix}easyleads_mailtext` (
	  id integer(11) NOT NULL,
	  name varchar(255) NOT NULL,
	  mail varchar(255) NOT NULL,
	  topic varchar(255) NOT NULL,
	  mailtext text NOT NULL,
	  PRIMARY KEY  (id)
	) $charset_collate;";

	require_once(get_home_path() . 'wp-admin/includes/upgrade.php');
	dbDelta($table_one);
	dbDelta($table_two);
	dbDelta($table_three);
}

register_activation_hook( __FILE__, 'easyleads_activate' );
// =========================================================================
// Short code:

function easyleads_shortcode_form() {
	$theform = '<form method="POST" id="easy_leads_form" action="/wp-admin/admin-post.php">';
	$theform .= '<input type="hidden" name="action" value="easy_leads_form"/>';
	$theform .= '<input type="text" name="name" placeholder="Name"/>';
	$theform .= '<input type="text" name="phone" placeholder="Phone"/>';
	$theform .= '<input type="text" name="mail" placeholder="E-mail"/>';
	$theform .= '<input type="submit" value="Save" />';
	$theform .= '</form>';
	return htmlspecialchars_decode($theform);
}

// Leads Form Save:
function easy_leads_form() {
	//var_dump($_POST); die();
	$name = false;
	$phone = false;
	$mail = false;

	if (isset($_POST['mail']) && filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL) && (strlen($_POST['mail']) <= 255)) {
		$mail = $_POST['mail'];
	}

	if (isset($_POST['name']) && (strlen($_POST['name']) <= 255)) {
		$name = $_POST['name'];
	}

	if (isset($_POST['phone']) && (strlen($_POST['phone']) <= 255)) {
		$phone = $_POST['phone'];
	}

	if ($name && $phone && $mail) {
		// validated - can save:
		global $wpdb;
		$wpdb->insert("{$wpdb->base_prefix}easyleads",
			[
				'name' => htmlspecialchars_decode($name),
				'mail' => htmlspecialchars_decode($mail),
				'phone' => htmlspecialchars_decode($phone),
				'created_at' => date('Y-m-d h:i:s'),
			],
			['%s']
		);

	} else {
		// show error;
	}
	wp_redirect(get_site_url());
}

add_shortcode( 'easyleadsform' , 'easyleads_shortcode_form');
add_action('admin_post_easy_leads_form', 'easy_leads_form');

// =========================================================================
// Admin panel page:
add_action('admin_menu', 'easyleads_plugin_setup_menu');
 
function easyleads_plugin_setup_menu(){
    add_menu_page( 'Easy Leads Free', 'Easy Leads Free', 'manage_options', 'easyleads_free', 'admin_page', 'dashicons-email-alt2' );
}
 
function admin_page() {
    require_once(get_home_path().'wp-content/plugins/easyleads/views/style.php');
    require_once(get_home_path().'wp-content/plugins/easyleads/views/admin_page.php');
    require_once(get_home_path().'wp-content/plugins/easyleads/views/admin_page_table.php');
    require_once(get_home_path().'wp-content/plugins/easyleads/views/admin_page_mail.php');
}
// ==========================================================================

// Mail text save:
function easyleads_mail_settings() {
		if( current_user_can( 'administrator' ) ){
    		$text = false;
    		$name = false;
    		$topic = false;
    		$mail = false;

    		if (isset($_POST['mailtext'])) {
    			$text = $_POST["mailtext"];
    		}

    		if (isset($_POST['name']) && (strlen($_POST['name']) <= 255)) {
    			$name = $_POST["name"];
    		}

    		if (isset($_POST['topic']) && (strlen($_POST['topic']) <= 255)) {
    			$topic = $_POST["topic"];
    		}

    		if (isset($_POST['mail']) && filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL) && (strlen($_POST['mail']) <= 255)) {
    			$mail = $_POST["mail"];
    		}

    		if ($text && $topic && $mail && $name) {
    			$text = filter_var($text, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    			$topic = filter_var($topic, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    			$name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	    		global $wpdb;
	    		require_once(get_home_path() . 'wp-admin/includes/upgrade.php');
				$wpdb->replace( "{$wpdb->base_prefix}easyleads_mailtext", [
					'id' => 1, 
					'mailtext' => $text,
					'name' => $name,
					'topic' => $topic,
					'mail' => $mail,
				], ['%s']);
			}
		}
	
	wp_redirect('/wp-admin/admin.php?page=easyleads_free');
}

add_action('admin_post_el_mail_form', 'easyleads_mail_settings');
// ==========================================================================

// Lead delete:
function easyleads_delete() {
	if (isset($_POST["id"])) {
		if( current_user_can( 'administrator' ) ){
			global $wpdb;
			$wpdb->delete( "{$wpdb->base_prefix}easyleads", ['id' => $_POST["id"]] );
		}
	}
	wp_redirect('/wp-admin/admin.php?page=easyleads_free');
}

add_action('admin_post_el_lead_delete', 'easyleads_delete');

// ==========================================================================



// Mail send:

function easyleads_send_mail($id) {
	global $wpdb;
	$text = $wpdb->get_row( "SELECT * FROM `{$wpdb->base_prefix}easyleads_mailtext` WHERE id = 1" );
	$to = $wpdb->get_row( "SELECT * FROM `{$wpdb->base_prefix}easyleads` WHERE id = $id" );
	if ($text) {
		$mail = $text->mail;
		$name = $text->name;
		$topic = $text->topic;
		$text = $text->mailtext;
	}
	
	$attachments = [];
	
	$headers = 'From: '.$name.' <'.$mail.'>' . "\r\n";
	$res = wp_mail($to->mail, $topic, $text, $headers, $attachments);
	if ($res) {
		return true;
	} else {
		//var_dump($res); die();
	}
}

function easyleads_send() {
	if (isset($_POST["id"])) {
		if( current_user_can( 'administrator' ) ){
			$is_sent = easyleads_send_mail($_POST["id"]);
			if ($is_sent) {
				global $wpdb;
				$wpdb->update( "{$wpdb->base_prefix}easyleads", 
					[
						'sent_at' => date('Y-m-d h:i:s'),
						'is_sent' => 1,
					], 
					['id' => $_POST["id"]],
					['%s']);
			}
		}
	}
	wp_redirect('/wp-admin/admin.php?page=easyleads_free');
} 

add_action('admin_post_el_lead_send', 'easyleads_send');
// ==========================================================================


?>