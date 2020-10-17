<?php
global $wpdb;
//require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
$leads = $wpdb->get_results( "SELECT * FROM `{$wpdb->base_prefix}easyleads` WHERE 1");

?>

<div class="elp-table-holder">
	<h3>Collected leads</h3>
	<table class="elp-table">
		<tr>
			<th>Id</th>
			<th>Name</th>
			<th>E-mail</th>
			<th>Phone</th>
			<th>Created at</th>
			<th>Actions</th>
		</tr>
		<?php 
		if ($leads) {
			foreach ($leads as $lead) {
		?>
		<tr>
			<td><?=$lead->id?></td>
			<td><?=$lead->name?></td>
			<td><?=$lead->mail?></td>
			<td><?=$lead->phone?></td>
			<td><?=$lead->created_at?></td>
			<td>
				<?php if ($lead->is_sent) {
					echo '<b class="green-text">Sent at</b> '.$lead->sent_at;	
				} else { ?>
					<form method="POST" action="/wp-admin/admin-post.php">
						<input type="hidden" name="action" value="el_lead_send"/>
						<input type="hidden" name="id" value="<?=$lead->id?>"/>
						<input type="submit" class="elp-accept" value="Send mail" />
					</form>
				<?php } ?>
					<form method="POST" action="/wp-admin/admin-post.php">
						<input type="hidden" name="action" value="el_lead_delete"/>
						<input type="hidden" name="id" value="<?=$lead->id?>"/>
						<input type="submit" class="elp-remove" value="Delete" />
					</form>
			</td>
		</tr>
		<?php } } ?>
	</table>
</div>