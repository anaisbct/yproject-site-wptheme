<?php
global $campaign, $current_error;
if (!isset($campaign)) {
	$campaign = atcf_get_current_campaign();
}
$WDGUser_current = WDGUser::current();

if (isset($campaign)):
	$min_value = ypcf_get_min_value_to_invest();
	$max_value = ypcf_get_max_value_to_invest();
	$part_value = ypcf_get_part_value();
	$max_part_value = ypcf_get_max_part_value();
	
	if ($max_part_value > 0):
		global $edd_options;
		$page_invest = get_page_by_path('investir');
		$page_invest_link = get_permalink($page_invest->ID);
		$page_invest_link .= '?campaign_id=' . $_GET['campaign_id'];
?>
    
		<?php
		global $current_breadcrumb_step; $current_breadcrumb_step = 1;
		locate_template( 'invest/breadcrumb.php', true );
		?>
		
		<div class="invest_step1_generalities">
		<?php switch ($campaign->funding_type()) {
			case "fundingdonation":
				echo wpautop( ATCF_CrowdFunding::get_translated_setting('donation_generalities') );
				break;
		    default:
				echo wpautop( ATCF_CrowdFunding::get_translated_setting('investment_generalities') );
				break;
		} ?>
		</div>
		
		<div class="invest_step1_currentproject"><?php echo html_entity_decode( $campaign->investment_terms() ); ?></div>
			
		<?php locate_template( 'invest/input-lightbox-user-infos.php', true ); ?>
		<?php locate_template( 'invest/input-lightbox-orga-infos.php', true ); ?>
		
		<form id="invest_form" action="<?php echo $page_invest_link; ?>" method="post" enctype="multipart/form-data" data-campaignid="<?php echo $campaign->ID; ?>" data-hasfilledinfos="<?php echo ($WDGUser_current->has_filled_invest_infos($campaign->funding_type()) ? "1" : "0"); ?>">
			<input type="hidden" id="input_invest_min_value" name="old_min_value" value="<?php echo $min_value; ?>">
			<input type="hidden" id="input_invest_max_value" name="old_max_value" value="<?php echo $max_value; ?>">
			<input type="hidden" id="input_invest_part_value" name="part_value" value="<?php echo $part_value; ?>">
			<input type="hidden" id="input_invest_max_part_value" name="part_value" value="<?php echo $max_part_value; ?>">
			<input type="hidden" id="input_invest_amount_total" value="<?php echo ypcf_get_current_amount(); ?>">
			
			<?php switch ($campaign->funding_type()) {
				case 'fundingdonation':
					$rewards = atcf_get_rewards($campaign->ID);
					?>
					<span style="display:none;">(<span id="input_invest_amount">0</span> &euro;)</span><br />
				
					<?php if (isset($rewards->rewards_list)): ?>
						<p><?php _e("Choisissez votre contrepartie :", "yproject"); ?></p>
						<ul id="reward-selector">
							<li><label>
								<input type="radio" name="selected_reward" data-amount="0" value="-1" checked="checked">
								<span class="reward-amount" style="display:none"><?php echo $min_value; ?></span>
								<span class="reward-name"><?php _e("Je ne souhaite pas de contrepartie", 'yproject'); ?></span>.
							</label></li>

						<?php foreach ($rewards->rewards_list as $reward): ?>
							<li <?php if (!$rewards->is_available_reward($reward['id'])) { ?>class="unavailable-reward"<?php } ?>><label>

							<div>
								<input type="radio" name="selected_reward" 
										value="<?php echo $reward['id']; ?>"
										<?php if (!$rewards->is_available_reward($reward['id'])) { ?>disabled="disabled"<?php } ?> />

								<span class="reward-amount"><?php echo intval($reward['amount']); ?></span>&euro; <?php _e("ou plus", 'yproject'); ?>
							</div>
							<div class="reward-name reward-not-null"><?php echo $reward['name']; ?></div>

							<?php if ($rewards->is_limited_reward($reward['id'])): ?>
								<?php $remaining = (intval($reward['limit']) - intval($reward['bought'])); ?>
								<div>
									<span class="detail"><?php _e("Contrepartie limit&eacute;e :", 'yproject'); ?></span>
									<span class="reward-remaining"><?php echo $remaining; ?></span>
									<?php if ($remaining > 1) { _e("restants", 'yproject'); } else { _e("restant", 'yproject'); } ?>
									<?php _e("sur", 'yproject'); ?> <?php echo intval($reward['limit']); ?>
								</div>
							<?php endif; ?>

							</label></li>
						<?php endforeach; ?>
						</ul>
						<?php _e("Je souhaite donner", 'yproject'); ?> <input type="text" id="input_invest_amount_part" name="amount_part" placeholder="<?php echo $min_value; ?>"> &euro; <br />
					<?php endif;
				break;
				
				case 'fundingdevelopment':
				case 'fundingproject': ?>
					<input type="text" id="input_invest_amount_part" name="amount_part" placeholder="<?php echo $min_value; ?>" value="<?php echo (!empty($_GET["init_invest"]) ? $_GET["init_invest"] : ''); ?>"> &euro; <span id="input_invest_amount" class="hidden">0</span><br />
				<?php
				break;
			} ?>
					
			&nbsp;&nbsp;<center><a href="javascript:void(0);" id="link_validate_invest_amount" class="button"><?php _e("Valider", 'yproject'); ?></a></center><br /><br />
		
			<div id="validate_invest_amount_feedback" style="display: none;">
				<?php $temp_min_part = ceil($min_value / $part_value); ?>
		
				<?php switch ($campaign->funding_type()) {
					case 'fundingdevelopment':
					case 'fundingproject': ?>
						<span class="invest_error <?php if ($current_error != "min") { ?>hidden<?php } ?>" id="invest_error_min"><?php _e("Vous devez investir au moins", 'yproject'); ?> <?php echo $temp_min_part; ?> &euro;.</span>
						<span class="invest_error <?php if ($current_error != "max") { ?>hidden<?php } ?>" id="invest_error_max"><?php _e("Vous ne pouvez pas investir plus de", 'yproject'); ?> <?php echo $max_part_value; ?> &euro;.</span>
					<?php
					break;

					case 'fundingdonation': ?>
						<span class="invest_error <?php if ($current_error != "min") { ?>hidden<?php } ?>" id="invest_error_min"><?php _e("Le montant minimal de soutien est de", 'yproject'); ?> <?php echo $temp_min_part; ?> &euro;.</span>
						<span class="invest_error <?php if ($current_error != "max") { ?>hidden<?php } ?>" id="invest_error_max"><?php _e("Vous ne pouvez pas soutenir avec plus de", 'yproject'); ?> <?php echo $max_part_value; ?> &euro;.</span>
						<span class="invest_error <?php if ($current_error != "reward_remaining") { ?>hidden<?php } ?>" id="invest_error_reward_remaining"><?php _e("La contrepartie que vous avez choisi n'est plus disponible.", 'yproject'); ?></span>
						<span class="invest_error <?php if ($current_error != "reward_insufficient") { ?>hidden<?php } ?>" id="invest_error_reward_insufficient"><?php _e("Vous devez donner plus pour obtenir cette contrepartie.", 'yproject'); ?></span>
					<?php
					break;
				} ?>
						
				<span class="invest_error <?php if ($current_error != "interval") { ?>hidden<?php } ?>" id="invest_error_interval"><?php _e("Merci de ne pas laisser moins de", 'yproject'); ?> <?php echo $min_value; ?>&euro; <?php _e("&agrave; investir.", 'yproject'); ?></span>
				<span class="invest_error <?php if ($current_error != "integer") { ?>hidden<?php } ?>" id="invest_error_integer"><?php _e("Le montant que vous pouvez investir doit &ecirc;tre entier.", 'yproject'); ?></span>
				<span class="invest_error <?php if ($current_error != "general") { ?>hidden<?php } ?>" id="invest_error_general"><?php _e("Le montant saisi semble comporter une erreur.", 'yproject'); ?></span>
				<span class="invest_success hidden" id="invest_success_message" class="button">
					<?php if ($campaign->funding_type()=="fundingdonation"): ?>
                    <?php _e("Vous vous appr&ecirc;tez &agrave; donner", 'yproject'); ?> <strong><span id="invest_show_amount"></span>&euro;</strong> <?php _e("en &eacute;change de :", 'yproject'); ?> <strong><span id="invest_show_reward"></span></strong>.<br/><br/>
					<?php endif; ?>
					<?php _e("Gr&acirc;ce à vous, nous serons", 'yproject'); ?> <?php echo (ypcf_get_backers() + 1); ?> <?php _e("&agrave; soutenir le projet. La somme atteinte sera de", 'yproject'); ?> <span id="invest_success_amount"></span>&euro;.
				</span>
		
				<div class="invest_step1_conditions">
				<?php switch ($campaign->funding_type()) {
					case "fundingdonation":
						echo wpautop( ATCF_CrowdFunding::get_translated_setting('message_before_donation') );
					break;
					default:
						echo wpautop( ATCF_CrowdFunding::get_translated_setting('contract') );
					break;
				} ?>
				</div>
		
				<br />

				<p id="invest_form_button" class="align-center">
					<?php switch ($campaign->funding_type()) {
						case "fundingdonation": ?>
							<input type="hidden" name="invest_type" value="user" />
							<input type="submit" value="<?php _e("Confirmer mon don", 'yproject'); ?>" class="button" />
						<?php
						break;
					
						default: 
							$current_user = wp_get_current_user();
							$wdg_current_user = new WDGUser( $current_user->ID );
							$api_user_id = $wdg_current_user->get_api_id();
							$organizations_list = WDGWPREST_Entity_User::get_organizations_by_role($api_user_id, WDGWPREST_Entity_Organization::$link_user_type_creator);
							?>
							<input type="submit" value="<?php _e("Investir", 'yproject'); ?>" class="button" />
							<select id="invest_type" name="invest_type">
								<option value="user"><?php _e("En mon nom (personne physique)", 'yproject'); ?></option>
								<?php if (count($organizations_list) > 0): ?>
									<?php foreach ($organizations_list as $organization_item): ?>
										<option value="<?php echo $organization_item->wpref; ?>"><?php _e("Pour l'organisation", 'yproject'); ?> <?php echo $organization_item->name; ?></option>
									<?php endforeach; ?>
									<option value="new_organisation"><?php _e("Pour une nouvelle organisation (personne morale)...", 'yproject'); ?></option>
								<?php else: ?>
									<option value="new_organisation"><?php _e("Pour une organisation (personne morale)...", 'yproject'); ?></option>
								<?php endif; ?>
							</select>
						<?php
						break;
					} ?>
				</p>
				<p id="invest_form_loading" class="align-center hidden">
					<img id="ajax-loader-img" src="<?php echo get_stylesheet_directory_uri() ?>/images/loading.gif" alt="chargement" />
				</p>
			</div>
		
		</form>
		<br /><br />
	    
		
	<?php else: ?>
		<?php _e("Il n&apos;est plus possible d&apos;investir sur ce", 'yproject'); ?> <a href="<?php echo get_permalink($campaign->ID); ?>"><?php _e("projet", 'yproject'); ?></a> !
	<?php endif; ?>
	
<?php endif;