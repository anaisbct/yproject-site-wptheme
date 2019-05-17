<?php global $campaign, $stylesheet_directory_uri; ?>

<?php ob_start(); ?>
<div class="wdg-lightbox-ref">
	
	<p class="align-center">
		<img src="<?php echo $stylesheet_directory_uri; ?>/images/common/picto-stat-loupe.png" width="150">
	</p>
	<br>
	
	<div class="align-justify">
		<strong><?php _e( "WE DO GOOD est une plateforme d'investissement en &eacute;change de royalties.", 'yproject' ); ?></strong><br />
		<br />
		<?php if ( $campaign->campaign_status() == ATCF_Campaign::$campaign_status_vote ): ?>
			<?php echo sprintf( __( "Vous souhaitez acc&eacute;der à la pr&eacute;sentation du projet %s, actuellement en &eacute;valuation, phase pr&eacute;alable au lancement de la lev&eacute;e de fonds.", 'yproject' ), $campaign->data->post_title ); ?><br />
			<br />
		<?php endif; ?>
		<?php if ( $campaign->campaign_status() == ATCF_Campaign::$campaign_status_collecte ): ?>
			<?php echo sprintf( __( "Vous souhaitez acc&eacute;der &agrave; la pr&eacute;sentation du projet %s, actuellement en lev&eacute;e de fonds.", 'yproject' ), $campaign->data->post_title ); ?><br />
			<br />
		<?php endif; ?>
		<?php _e( "Vous pouvez y acc&eacute;der sans investir mais la r&eacute;glementation nous impose de vous informer que l'investissement dans des soci&eacute;t&eacute;s non cot&eacute;es comporte des risques sp&eacute;cifiques :", 'yproject' ); ?><br />
		<?php _e( "&gt; Le retour sur investissement d&eacute;pend de la r&eacute;ussite du projet financ&eacute;.", 'yproject' ); ?><br />
		<?php _e( "&gt; Risque de perte totale ou partielle du capital investi.", 'yproject' ); ?><br />
		<br />
		<?php if ( $campaign->has_category_slug( 'partners', 'investisur' ) ): ?>
		<?php _e( "Ce projet est n&eacute;anmoins labellis&eacute; &quot;InvestiS&ucirc;r&quot;, un syst&egrave;me de protection de l'investissement propos&eacute; par notre partenaire Le Fonds Compagnon.", 'yproject' ); ?>
		<?php _e( "Nous vous invitons &agrave; lire les conditions de ce label dans la pr&eacute;sentation du projet et sur leur site.", 'yproject' ); ?><br />
		<?php _e( "Rendez-vous sur le site", 'yproject' ); ?> https://www.investisur.com/.<br />
		<br />
		<?php endif; ?>
		<strong><?php _e( "Avez‐vous conscience que, dans le cas o&ugrave; vous investissez, vous pouvez perdre &eacute;ventuellement la totalit&eacute; de votre investissement ?", 'yproject' ); ?></strong><br />
		<br />
		<form class="db-form v3">
			<a href="<?php echo home_url( '/investissement/' ); ?>" class="button half left transparent"><?php _e( "Non / En savoir plus", 'yproject' ); ?></a>
			<button type="button" class="button half right close red" data-close="project-warning"><?php _e( "Oui / Continuer", 'yproject' ); ?></button>
		</form>
	</div>
	
</div>

<?php
$lightbox_content = ob_get_contents();
ob_end_clean();
echo do_shortcode('[yproject_lightbox_cornered id="project-warning" title="'.__( "Avertissement", 'yproject' ).'" autoopen="0" catchclick="0"]' . $lightbox_content . '[/yproject_lightbox_cornered]');