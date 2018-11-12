<?php
global $stylesheet_directory_uri;
$page_controler = WDG_Templates_Engine::instance()->get_controler();
$fields_hidden = $page_controler->get_form_add_check()->getFields( WDG_Form_Dashboard_Add_Check::$field_group_hidden );
$fields_email = $page_controler->get_form_add_check()->getFields( WDG_Form_Dashboard_Add_Check::$field_group_user_email );
?>

<form action="" method="post" id="form-contacts-add-check" class="db-form v3 full center bg-white hidden">
	<div class="align-justify">
		<h3><?php _e( "Ajouter un investissement par ch&egrave;que", 'yproject' ); ?></h3>
		<?php _e( "Pour ajouter un investissement par ch&egrave;que, vous aurez besoin des informations compl&egrave;tes de votre investisseur (et de sa structure/entreprise si il investit en tant que personne morale).", 'yproject' ); ?><br>
		<?php _e( "Vous aurez aussi besoin de nous transmettre les fichiers permettant de l'authentifier (ainsi que la personne morale &eacute;ventuelle).", 'yproject' ); ?><br>
		<?php _e( "Enfin, il nous faudra une photo du ch&egrave;que ainsi que du contrat paraph&eacute; et sign&eacute; correspondant &agrave; l'investissement.", 'yproject' ); ?><br>
		<?php _e( "L'investissement sera mis en attente, en attendant que nos &eacute;quipes valident les informations.", 'yproject' ); ?>
		<br><br>
	</div>
	
	<?php foreach ( $fields_hidden as $field ): ?>
		<?php global $wdg_current_field; $wdg_current_field = $field; ?>
		<?php locate_template( array( 'common/forms/field.php' ), true, false );  ?>
	<?php endforeach; ?>

	<?php foreach ( $fields_email as $field ): ?>
		<?php global $wdg_current_field; $wdg_current_field = $field; ?>
		<?php locate_template( array( 'common/forms/field.php' ), true, false );  ?>
	<?php endforeach; ?>

	<div class="align-center">
		<button type="submit" id="button-contacts-add-check-search" class="button save red"><?php _e( "Rechercher l'utilisateur", 'yproject' ); ?></button>
		<br><br>
		<img id="add-check-search-loading" class="hidden" src="<?php echo $stylesheet_directory_uri; ?>/images/loading.gif" width="30" alt="loading">
		<span id="add-check-feedback-found-user" class="add-check-feedback hidden"><?php _e( "Voici les informations existantes li&eacute;es &agrave; cette adresse e-mail.", 'yproject' ); ?></span>
		<span id="add-check-feedback-found-orga" class="add-check-feedback hidden"><?php _e( "Une organisation (personne morale) correspond &agrave; cette adresse e-mail. Merci de saisir l'adresse e-mail d'une personne physique.", 'yproject' ); ?></span>
		<span id="add-check-feedback-not-found" class="add-check-feedback hidden"><?php _e( "Aucun compte ne correspond &agrave; cette adresse e-mail sur WE DO GOOD. Merci de saisir les informations correspondantes", 'yproject' ); ?></span>
	</div>
	
</form>