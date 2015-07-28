<?php 
global $disable_logs, $WDG_cache_plugin; 
$disable_logs = TRUE;
if (isset($_GET["campaign_id"])) {
        $campaign_id = $_GET["campaign_id"];
	global $stylesheet_directory_uri;
?>

<?php
//*******************
//CACHE PROJECT PUBLIC STATS
$cache_stats = $WDG_cache_plugin->get_cache('project-stats-public-' . $campaign_id, 1);
if ($cache_stats !== FALSE) { echo $cache_stats; }
else {
	ob_start();
?>

<h2 class="expandator" data-target="votes">Votes <img src="<?php echo $stylesheet_directory_uri; ?>/images/plus.png" alt="signe plus"/></h2>
<div id="extendable-votes" class="expandable">
<?php
	$post_campaign = get_post($campaign_id);
	$upload_dir = wp_upload_dir();
	if (file_exists($upload_dir['basedir'] . '/projets/' . $post_campaign->post_name . '-stats.jpg')) { 
		echo '<img src="'.$upload_dir['baseurl'] . '/projets/' . $post_campaign->post_name . '-stats.jpg" alt="Statistiques du projet" />';
	} else {
		locate_template( array("requests/votes.php"), true );
		locate_template( array("projects/stats-votes-public.php"), true );
		$vote_results = wdg_get_project_vote_results($_GET['campaign_id']);
		print_vote_results($vote_results);
	}
?>
</div>
<?php
switch (atcf_get_campaign($campaign_id)->funding_type()) {
        case 'fundingdonation' :
            $investor_action = 'Contributions';
            break;
        default :
            $investor_action = 'Investissements';
    }?>
<h2 class="expandator" data-target="investments"><?php echo $investor_action;?> <img src="<?php echo $stylesheet_directory_uri; ?>/images/plus.png" alt="signe plus" /></h2>
<div id="extendable-investments" class="expandable">
<?php 
	locate_template( array("projects/stats-investments-public.php"), true );
        print_investments($campaign_id, false);
?>
</div>

<?php
	$cache_stats = ob_get_contents();
	$WDG_cache_plugin->set_cache('project-stats-public-' . $campaign_id, $cache_stats, 60*30, 1);
	ob_end_clean();
	echo $cache_stats;
}
//FIN CACHE MENU
//*******************

?>

<script type="text/javascript">
    jQuery(document).ready( function($) {
        <?php
        $status = (atcf_get_campaign($campaign_id)->campaign_status());
        if ($status == 'vote'){
            echo '$("#extendable-votes").show();';
        } else if ($status == 'collecte' || $status =='funded') {
            echo '$("#extendable-investments").show();';
        }
        ?>
    });
</script>

<?php 
} 
