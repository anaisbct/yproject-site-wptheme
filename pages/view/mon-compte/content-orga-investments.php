<?php
$page_controler = WDG_Templates_Engine::instance()->get_controler();
global $WDGOrganization;
?>
<h2 class="underlined"><?php _e( "Attestations de transactions annuelles", 'yproject' ); ?></h2>
<?php
$has_declaration = false;
$date_now = new DateTime();
?>
<?php for( $year = 2016; $year < $date_now->format('Y'); $year++ ): ?>
	<?php if ( $WDGOrganization->has_royalties_for_year( $year ) ): ?>
		<?php
		$has_declaration = true;
		$declaration_url = $WDGOrganization->get_royalties_certificate_per_year( $year );
		?>
		<a href="<?php echo $declaration_url; ?>" download="attestation-royalties-<?php echo $year; ?>.pdf" class="button red">Télécharger l'attestation <?php echo $year; ?></a><br /><br />
	<?php endif; ?>
<?php endfor; ?>
<?php if ( !$has_declaration ): ?>
	<?php _e( "Aucune", 'yproject' ); ?>
<?php endif; ?>