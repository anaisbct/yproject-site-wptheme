<?php
global $post, $campaign_id, $can_modify, $show_admin_bar;
if (!isset($campaign_id)) {
    if (isset($_GET['campaign_id'])) $campaign_id = $_GET['campaign_id'];
    else $campaign_id = get_the_ID();
}
$post_campaign = get_post($campaign_id);

if ($can_modify) {
	$show_admin_bar = TRUE;
	$campaign = atcf_get_campaign($post_campaign);
        $params_full = ''; $params_partial = '';
        if (isset($_GET['preview']) && $_GET['preview'] = 'true') { $params_full = '?preview=true'; $params_partial = '&preview=true'; }
        $campaign_id_param = '?campaign_id=';
        $campaign_id_param .= $campaign_id;                             // Page projet
        $page_dashboard = get_page_by_path('tableau-de-bord');          // Tableau de bord
		$page_wallet = get_page_by_path('gestion-financiere');		// Gestion financière
        // Statistiques avancées
        if (strtotime($post_campaign->post_date) < strtotime('2014-02')) {
            $pages_stats = get_page_by_path('vote');
        } else {
            $pages_stats = get_page_by_path('statistiques-avancees');
        }
       
        //Récupération de la page en cours
        $current_page = 'project';
        if (isset($post->post_name)) $current_page = $post->post_name;
        if (bp_is_group()) $current_page = 'group';
       
        //Lien vers le groupe d'investisseurs du projet
        //Visible si le groupe existe et que l'utilisateur est bien dans ce groupe
        $investors_group_id = get_post_meta($campaign_id, 'campaign_investors_group', true);
        $group_link = '';
        $group_exists = (is_numeric($investors_group_id) && ($investors_group_id > 0));
        $is_user_group_member = groups_is_user_member(bp_loggedin_user_id(), $investors_group_id);
        if ($group_exists && $is_user_group_member) {
            $group_obj = groups_get_group(array('group_id' => $investors_group_id));
            $group_link = bp_get_group_permalink($group_obj);
        }
?>
        <div id="single_project_admin_bar">
                <div class="center">
                        <a href="<?php echo get_permalink($page_dashboard->ID) . $campaign_id_param . $params_partial; ?>" <?php if ($current_page == 'tableau-de-bord') { echo 'class="selected"'; } ?>><?php _e('Tableau de bord', 'yproject'); ?></a>
                        |
                        <a href="<?php echo get_permalink($campaign_id) . $params_full; ?>" <?php if ($current_page == $post_campaign->post_name) { echo 'class="selected"'; } ?>><?php _e('Page projet', 'yproject'); ?></a>
                        
						|
                        <a href="<?php echo get_permalink($page_wallet->ID) . $campaign_id_param . $params_partial; ?>" <?php if ($current_page == 'gestion-financiere') { echo 'class="selected"'; } ?>><?php _e('Gestion financi&egrave;re', 'yproject'); ?></a>
                        
                        <?php if ($group_link != '') : ?>
                        |
                        <a href="<?php echo $group_link; ?>" <?php if ($current_page == 'group') { echo 'class="selected"'; } ?>>Groupe d&apos;investisseurs</a>
                        <?php endif; ?>
                </div>
        </div>
<?php }