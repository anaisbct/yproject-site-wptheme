<?php
function query_projects_preview($nb=0){
	return queryHomeProjects($nb,'preview');
}
function query_projects_vote($nb=0){
	return queryHomeProjects($nb,'vote','desc');
}
function query_projects_collecte($nb=0){
	return queryHomeProjects($nb,'collecte');
}
function query_projects_funded($nb=0){
	return queryFinishedProjects($nb,'funded');
}
function query_projects_archive($nb=0){
	return queryFinishedProjects($nb,'archive');
}

function queryHomeProjects($nb,$type,$order = 'asc') {
	$query_options = array(
		'showposts' => $nb,
		'post_type' => 'download',
		'post_status' => 'publish',
		'meta_query' => array (

			array (
				'key' => 'campaign_vote',
				'value' => $type
				),
			array (
				'key' => 'campaign_end_date',
				'compare' => '>',
				'value' => date('Y-m-d H:i:s')
			)
		),
		'orderby' => 'post_date',
		'order' => $order
	);
	return query_posts( $query_options );

}

function queryFinishedProjects($nb,$type) {
	$query_options = array(
		'showposts' => $nb,
		'post_type' => 'download',
		'post_status' => 'publish',
		'meta_query' => array (
			array (
				'key' => 'campaign_vote',
				'value' => $type
			)
		),
		'meta_key' => 'campaign_end_date',
		'orderby' => 'meta_value',
		'order' => 'desc'
	);
	return query_posts( $query_options );
}

class YPProjectLib {
	public static function edit_team() {
		$buffer = '';
		if (isset($_REQUEST['action']) && isset($_GET['campaign_id'])) {
			switch ($_REQUEST['action']) {
				case 'yproject-add-member':
					if (!empty($_POST['new_team_member'])) {
						$user_by_login = get_user_by('login', $_POST['new_team_member']);
						$user_by_mail = get_user_by('email', $_POST['new_team_member']);
						if ($user_by_login === FALSE && $user_by_mail === FALSE) {
							$buffer = 'Nous n&apos;avons pas trouv&eacute; d&apos;utilisateur correspondant.';
						} else {
							//Récupération du bon id wordpress
							$user_wp_id = '';
							if ($user_by_login !== FALSE) $user_wp_id = $user_by_login->ID;
							else if ($user_by_mail !== FALSE) $user_wp_id = $user_by_mail->ID;
							//Récupération des infos existantes sur l'API
							$user_api_id = BoppLibHelpers::get_api_user_id($user_wp_id);
							$project_api_id = BoppLibHelpers::get_api_project_id($_GET['campaign_id']);
							BoppLibHelpers::check_create_role(BoppLibHelpers::$project_team_member_role['slug'], BoppLibHelpers::$project_team_member_role['title']);
							//Ajout à l'API
							BoppLib::link_user_to_project($project_api_id, $user_api_id, BoppLibHelpers::$project_team_member_role['slug']);
							$buffer = TRUE;
							do_action('wdg_delete_cache', array(
								'users/' . $user_api_id . '/roles/' . BoppLibHelpers::$project_team_member_role['slug'] . '/projects',
								'projects/' . $project_api_id . '/roles/' . BoppLibHelpers::$project_team_member_role['slug'] . '/members'
							));
						}
					} else {
						$buffer = 'Merci de renseigner un identifiant ou un email.';
					}
					break;
				    
				case 'yproject-remove-member':
					if (!isset($_POST['user_to_remove'])) {
						$buffer = 'BUMP';
					} else {
						//Récupération des infos existantes sur l'API
						$user_api_id = BoppLibHelpers::get_api_user_id($_POST['user_to_remove']);
						$project_api_id = BoppLibHelpers::get_api_project_id($_GET['campaign_id']);
						//Supprimer dans l'API
						BoppLib::unlink_user_from_project($project_api_id, $user_api_id, BoppLibHelpers::$project_team_member_role['slug']);
						$buffer = TRUE;
						do_action('wdg_delete_cache', array(
							'users/' . $user_api_id . '/roles/' . BoppLibHelpers::$project_team_member_role['slug'] . '/projects',
							'projects/' . $project_api_id . '/roles/' . BoppLibHelpers::$project_team_member_role['slug'] . '/members'
						));
					}
					break;
			}
		}
		return $buffer;
	}
	
	/**
	 * Gère le formulaire d'ajout d'actualité
	 */
	public static function form_validate_news_add($campaign_id) {
		$post_campaign = get_post($campaign_id);
		$campaign = atcf_get_campaign($post_campaign);
		if (!$campaign->current_user_can_edit() 
				|| !isset($_POST['action'])
				|| $_POST['action'] != 'ypcf-campaign-add-news') {
			return FALSE;
		}

		$current_user = wp_get_current_user();

		$category_slug = $post_campaign->ID . '-blog-' . $post_campaign->post_name;
		$category_obj = get_category_by_slug($category_slug);

		$blog = array(
			'post_title'    => $_POST['posttitle'],
			'post_content'  => $_POST['postcontent'],
			'post_status'   => 'publish',
			'post_author'   => $current_user->ID,
			'post_category' => array($category_obj->cat_ID)
		);

		wp_insert_post($blog, true);
		do_action('wdg_delete_cache', array( 'project-header-menu-'.$post_campaign->ID ));
	}
	
	/**
	 * Gère le formulaire de paramètres projets
	 */
	public static function form_validate_edit_parameters() {
		$buffer = TRUE;
	    
		if (!isset($_GET["campaign_id"])) { return FALSE; }
		$campaign_id = $_GET["campaign_id"];
		$post_campaign = get_post($campaign_id);
		$campaign = atcf_get_campaign($post_campaign);
		
		if (!$campaign->current_user_can_edit() 
				|| !isset($_POST['action'])
				|| $_POST['action'] != 'edit-project-parameters') {
			return FALSE;
		}
		
		$title = sanitize_text_field($_POST['project-name']);
		if (!empty($title)) {
			wp_update_post(array(
				'ID' => $campaign_id,
				'post_title' => $title
			));
		} else {
			$buffer = FALSE;
		}
		
		$cat_cat_id = -1; $cat_act_id = -1;
		if (isset($_POST['categories'])) { $cat_cat_id = $_POST['categories']; } else { $buffer = FALSE; }
		if (isset($_POST['activities'])) { $cat_act_id = $_POST['activities']; } else { $buffer = FALSE; }
		if ($cat_cat_id != -1 && $cat_act_id != -1) {
			$cat_ids = array_map( 'intval', array($cat_cat_id, $cat_act_id) );
			wp_set_object_terms($campaign_id, $cat_ids, 'download_category');
		}
                
                if (isset($_POST['phone'])) {
			update_post_meta($campaign_id, 'campaign_contact_phone', $_POST['phone']);
		} else {
			$buffer = FALSE;
		}
		
		if (isset($_POST['project-location'])) {
			update_post_meta($campaign_id, 'campaign_location', $_POST['project-location']);
		} else {
			$buffer = FALSE;
		}
		
		if (isset($_POST['video'])) {
			update_post_meta($campaign_id, 'campaign_video', esc_url($_POST['video']));
		}
		
		/* Gestion fichiers / images */
		$image_header = $_FILES[ 'image_header' ];
		if (!empty($image_header)) {
			$upload_overrides = array( 'test_form' => false );

			$upload = wp_handle_upload( $image_header, $upload_overrides );
			if (isset($upload[ 'url' ])) {
				$path = $_FILES['image_header']['name'];
				$ext = pathinfo($path, PATHINFO_EXTENSION);
				$is_image_accepted = true;
				switch (strtolower($ext)) {
					case 'png':
						$image_header = imagecreatefrompng($upload[ 'file' ]);
						break;
					case 'jpg':
					case 'jpeg':
						$image_header = imagecreatefromjpeg($upload[ 'file' ]);
						break;
					default:
						$is_image_accepted = false;
						break;
				}
				if ($is_image_accepted) {
					for ($i = 0; $i < 10; $i++) {
						imagefilter($image_header, IMG_FILTER_GAUSSIAN_BLUR);
						imagefilter($image_header, IMG_FILTER_SELECTIVE_BLUR);
					}
					$withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $upload[ 'file' ]);
					$img_name = $withoutExt.'_blur.jpg';
					imagejpeg($image_header,$img_name);

					//Suppression dans la base de données de l'ancienne image
					global $wpdb;
					$table_posts = $wpdb->prefix . "posts";
					$old_attachement_id = $wpdb->get_var( "SELECT * FROM ".$table_posts." WHERE post_parent=".$campaign_id." and post_title='image_header'" );
					wp_delete_attachment( $old_attachement_id, true );
					
					$attachment = array(
						'guid'           => $upload[ 'url' ], 
						'post_mime_type' => $upload[ 'type' ],
						'post_title'     => 'image_header',
						'post_content'   => '',
						'post_status'    => 'inherit'
					);
					$attach_id = wp_insert_attachment( $attachment, $img_name, $campaign_id );		

					wp_update_attachment_metadata( 
						$attach_id, 
						wp_generate_attachment_metadata( $attach_id, $img_name ) 
					);
					//Suppression de la position de la couverture
					delete_post_meta($campaign_id, 'campaign_cover_position');

					add_post_meta( $campaign_id, '_thumbnail_id', absint( $attach_id ) );
				}
			}
		}
		
		
		$image = $_FILES[ 'image_home' ];
		if (!empty($image)) {
			$upload_overrides = array( 'test_form' => false );
			$upload = wp_handle_upload( $image, $upload_overrides );
			if (isset($upload[ 'url' ])) {
				$attachment = array(
					'guid'           => $upload[ 'url' ], 
					'post_mime_type' => $upload[ 'type' ],
					'post_title'     => 'image_home',
					'post_content'   => '',
					'post_status'    => 'inherit'
				);

				//Suppression dans la base de données de l'ancienne image
				global $wpdb;
				$table_posts = $wpdb->prefix . "posts";
				$old_attachement_id = $wpdb->get_var( "SELECT * FROM ".$table_posts." WHERE post_parent=".$campaign_id." and post_title='image_home'" );
				wp_delete_attachment($old_attachement_id, true);

				$attach_id = wp_insert_attachment($attachment, $upload[ 'file' ], $campaign_id);		

				wp_update_attachment_metadata( 
					$attach_id, 
					wp_generate_attachment_metadata( $attach_id, $upload[ 'file' ] ) 
				);
			}
		}
		
		$temp_blur = $_POST['image_header_blur'];
		if (empty($temp_blur)) $temp_blur = 'FALSE';
		update_post_meta($campaign_id, 'campaign_header_blur_active', $temp_blur);
		/* FIN Gestion fichiers / images */
		
		
		if (isset($_POST['project-organisation'])) {
			//Récupération de l'ancienne organisation
			$api_project_id = BoppLibHelpers::get_api_project_id($post_campaign->ID);
			$current_organisations = BoppLib::get_project_organisations_by_role($api_project_id, BoppLibHelpers::$project_organisation_manager_role['slug']);
			$current_organisation = FALSE;
			if (count($current_organisations) > 0) {
			    $current_organisation = $current_organisations[0];
			}
			
			$delete = FALSE;
			$update = FALSE;
			
			//On met à jour : si une nouvelle organisation est renseignée et différente de celle d'avant
			//On supprime : si la nouvelle organisation renseignée est différente de celle d'avant
			if (!empty($_POST['project-organisation'])) {
				$organisation_selected = new YPOrganisation($_POST['project-organisation']);
				if ($current_organisation === FALSE || $current_organisation->organisation_wpref != $organisation_selected->get_wpref()) {
					$update = TRUE;
					if ($current_organisation !== FALSE) {
						$delete = TRUE;
					}
				}
				
			//On supprime : si rien n'est sélectionné + il y avait quelque chose avant
			} else {
				if ($current_organisation !== FALSE) {
					$delete = TRUE;
				}
			}
			
			if ($delete) {
				BoppLib::unlink_organisation_from_project($api_project_id, $current_organisation->id);
			}
				
			if ($update) {
				$api_organisation_id = $organisation_selected->get_bopp_id();
				BoppLib::link_organisation_to_project($api_project_id, $api_organisation_id, BoppLibHelpers::$project_organisation_manager_role['slug']);
			}
		} else {
			$buffer = FALSE;
		}
		
		if (isset($_POST['fundingtype'])) { 
			if ($_POST['fundingtype'] == 'fundingdevelopment' || $_POST['fundingtype'] == 'fundingproject' || $_POST['fundingtype'] == 'fundingdonation') {
				update_post_meta($campaign_id, 'campaign_funding_type', $_POST['fundingtype']); 
			} else {
				$buffer = FALSE;
			}
		}
		if (isset($_POST['fundingduration'])) { 
			$duration = $_POST['fundingduration'];
			if (is_numeric($duration) && $duration > 0 && (int)$duration == $duration) {
				update_post_meta($campaign_id, 'campaign_funding_duration', $duration); 
			} else {
				$buffer = FALSE;
			}
		}
		if (isset($_POST['minimum_goal'])) {
			$minimum_goal = $_POST['minimum_goal'];
			if (is_numeric($minimum_goal) && $minimum_goal > 0 && (int)$minimum_goal == $minimum_goal) {
				update_post_meta($campaign_id, 'campaign_minimum_goal', $minimum_goal); 
			} else {
				$buffer = FALSE;
			}
		}
		if (isset($_POST['maximum_goal'])) {
			$goal = $_POST['maximum_goal'];
			if (is_numeric($goal) && $goal > 0 && (int)$goal == $goal) {
				if ($goal < $minimum_goal) $goal = $minimum_goal;
				update_post_meta($campaign_id, 'campaign_goal', $goal); 
			} else {
				$buffer = FALSE;
			}
		}
		
		do_action('wdg_delete_cache', array(
			'project-header-image-' . $campaign_id, 
			'project-content-summary-' . $campaign_id,
			'project-content-about-' . $campaign_id,
			'projects-current',
			'projects-others'
		));
		
		if ($buffer && isset($_POST['new_orga'])) {
			$page_new_orga = get_page_by_path('creer-une-organisation');
			header('Location: ' . get_permalink($page_new_orga->ID));
		}
		    
		return $buffer;
	}
	
	/**
	 * Gère le formulaire de paramètres projets
	 */
	public static function form_submit_yearly_account($year) {
		if (!isset($_GET["campaign_id"])) { return FALSE; }
		$campaign_id = $_GET["campaign_id"];
		
		$file = $_FILES[ 'accounts_year_' . $year ];
		if (!empty($file)) {
			$upload_overrides = array( 'test_form' => false );
			$upload = wp_handle_upload( $file, $upload_overrides );
			$file_name = $file['name'];
			if (isset($upload[ 'url' ])) {
				$attachment = array(
					'guid'           => $upload[ 'url' ], 
					'post_mime_type' => $upload[ 'type' ],
					'post_title'     => 'Yearly Accounts ' . $year,
					'post_content'   => '',
					'post_status'    => 'inherit'
				);
				wp_insert_attachment( $attachment, $file_name, $campaign_id );
			}
		}
	}
}
