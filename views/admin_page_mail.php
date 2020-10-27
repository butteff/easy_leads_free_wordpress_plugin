<?php
global $wpdb;
//require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
$text = $wpdb->get_row( "SELECT * FROM `{$wpdb->base_prefix}easyleads_mailtext` WHERE id = 1" );

if ($text) {
	$mail = esc_textarea($text->mail);
	$name = esc_textarea($text->name);
	$topic = esc_textarea($text->topic);
	$text = esc_textarea($text->mailtext);
} else {
	$text = 'Input mail text here';
	$mail = 'E-mail';
	$name = 'Name';
	$topic = 'Topic';
}

?>

<div class="elp-mail-holder">
	<h3>Mail Text</h3>
	<form method="POST" action="/wp-admin/admin-post.php">
	<input type="hidden" name="action" class="elp-input" value="el_mail_form"/>
	<input type="text" name="mail" class="elp-input" value="<?=$mail?>" placeholder="E-mail"/>
	<input type="text" name="name" class="elp-input" value="<?=$name?>" placeholder="Name"/>
	<input type="text" name="topic" class="elp-input" value="<?=$topic?>" placeholder="Mail Topic"/>
	<textarea class="elp-textarea" name="mailtext"><?=$text?></textarea>
	<input type="submit" class="elp-accept elp-accept-big" value="Save" />
	</form>
	<br/><hr/>
	<h3>Need more?</h3>
	<p>Upgrade the plugin to the premium version to have file attachments, additional form fields generation, mail notifications about new leads, xls/csv export, banlist and other features in future updates.</p>

</div>