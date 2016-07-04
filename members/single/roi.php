<?php
/**
 * Affichage des investissements de l'utilisateur
 */
$WDGUser_current = WDGUser::current();
$user_investments = $WDGUser_current->get_validated_investments();
?>

<?php if ( empty( $user_investments ) ): ?>
	<?php _e( "Vous n'avez encore aucun investissement valide sur le site.", 'yproject' ); ?>

<?php else: ?>

	<?php foreach ( $user_investments as $campaign_id => $campaign_investments ): ?>

		<?php if ( !empty($campaign_id) ): ?>
		
			<?php
			$campaign = atcf_get_campaign( $campaign_id );
			$campaign_amount = $campaign->current_amount( false );
			?>

			<?php if ( !empty ($campaign) && !empty ( $campaign->data ) ): ?>
			<div class="user-roi-item">

				<a href="<?php echo get_permalink( $campaign_id ); ?>"><h3><?php echo $campaign->data->post_title; ?></h3></a>
				
				<div class="percent33"><strong><?php _e("Montant lev&eacute; :", 'yproject'); ?></strong> <?php echo $campaign_amount; ?> &euro;</div>
				<div class="percent33"><strong><?php _e("Dur&eacute;e du versement :", 'yproject'); ?></strong> <?php echo $campaign->funding_duration(); ?> <?php _e("ans", 'yproject'); ?></div>
				<div class="percent33"><strong><?php _e("Pourcentage du versement :", 'yproject'); ?></strong> <?php echo $campaign->roi_percent(); ?>%</div>
				
				<div class="clear"></div>
				
				<?php
				/**
				 * Liste des investissements
				 */
				?>
				<?php if ( !empty ($campaign_investments ) ): ?>
				<h4 class="margin-top"><?php _e("Vos investissements sur ce projet", 'yproject'); ?></h4>
				
				<table>
					<thead>
						<tr>
							<td><?php _e("Date", 'yproject'); ?></td>
							<td><?php _e("Montant", 'yproject'); ?></td>
							<td><?php _e("Pourcentage du CA &agrave; percevoir", 'yproject'); ?></td>
						</tr>
					</thead>
					
					<tbody>
						<?php foreach ($campaign_investments as $investment_id): ?>
							<?php
							$payment_date = date_i18n( get_option('date_format'), strtotime( get_post_field( 'post_date', $investment_id ) ) );
							$payment_amount = edd_get_payment_amount( $investment_id );
							$investor_proportion = $payment_amount / $campaign_amount;
							$roi_percent_full = ($campaign->roi_percent() * $investor_proportion);
							$roi_percent_display = round($roi_percent_full * 10000) / 10000;
							?>
							<tr>
								<td><?php echo $payment_date; ?></td>
								<td><?php echo $payment_amount; ?> &euro;</td>
								<td><?php echo $roi_percent_display; ?>%</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<?php endif; ?>
				
				
				<?php
				/**
				 * Liste des ROIs reçus
				 */
				?>
				<?php $roi_list = WDGROI::get_roi_list_by_campaign_user( $campaign_id, $WDGUser_current->wp_user->ID ); ?>
				<?php $future_roi_list = WDGROIDeclaration::get_list_by_campaign_id( $campaign_id ); ?>
				
				<h4 class="margin-top"><?php _e("Vos royalties", 'yproject'); ?></h4>
				
				<table>
					<thead>
						<tr>
							<td><?php _e("Date", 'yproject'); ?></td>
							<td><?php _e("Montant", 'yproject'); ?></td>
						</tr>
					</thead>
					
					<tbody>
						<?php foreach ($roi_list as $roi): ?>
							<?php $roi_date = date_i18n( get_option('date_format'), strtotime( $roi->date_transfer ) ); ?>
							<tr>
								<td><?php echo $roi_date; ?></td>
								<td><?php echo $roi->amount; ?> &euro;</td>
							</tr>
						<?php endforeach; ?>
							
						<?php foreach ($future_roi_list as $roi_declaration): ?>
							<?php if ($roi_declaration->status != WDGROIDeclaration::$status_finished): ?>
								<?php $roi_declaration_date = date_i18n( get_option('date_format'), strtotime( $roi_declaration->date_due ) ); ?>
								<tr>
									<td><?php echo $roi_declaration_date; ?></td>
									<td><?php _e("A venir...", 'yproject'); ?></td>
								</tr>
							<?php endif; ?>
						<?php endforeach; ?>
					</tbody>
				</table>
				
			</div>
			<?php endif; ?>

		<?php endif; ?>

	<?php endforeach; ?>

<?php endif;