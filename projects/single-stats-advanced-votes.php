<?php
if (isset($_GET['campaign_id'])) {
	locate_template( array("requests/votes.php"), true ); ?>

	<h2>Statistiques des votes</h2>
	<?php
		locate_template( array("projects/stats-votes-public.php"), true );
		$vote_results = wdg_get_project_vote_results($_GET['campaign_id']);
		print_vote_results($vote_results);
	?>

	<h3>Conseils</h3>
	<?php if (!empty($vote_results['list_advice'])) { ?>
	<ul class="com-activity-list">
		<?php foreach ( $vote_results['list_advice'] as $advice ) { 
			$user_obj = get_user_by('id', $advice->user_id);
		?>
			<li>
			    <a href="<?php echo bp_core_get_userlink($advice->user_id, false, true); ?>"><?php echo $user_obj->display_name; ?></a> : <?php echo html_entity_decode($advice->advice, ENT_QUOTES | ENT_HTML401); ?>
			</li>
		<?php } ?>
	</ul>
	<?php } ?>
<?php } ?>