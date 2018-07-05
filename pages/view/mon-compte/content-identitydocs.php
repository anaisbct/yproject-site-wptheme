<?php
$page_controler = WDG_Templates_Engine::instance()->get_controler();
$WDGUserIdentityDocsForm = $page_controler->get_user_identitydocs_form();
$fields_hidden = $WDGUserIdentityDocsForm->getFields(WDG_Form_User_Identity_Docs::$field_group_hidden );
$fields_files = $WDGUserIdentityDocsForm->getFields( WDG_Form_User_Identity_Docs::$field_group_files );
?>

<h2 class="underlined"><?php _e( "Mes justificatifs d'identit&eacute;", 'yproject' ); ?></h2>

<form method="POST" enctype="multipart/form-data" class="db-form v3 full">
		
	<?php foreach ( $fields_hidden as $field ): ?>
		<?php global $wdg_current_field; $wdg_current_field = $field; ?>
		<?php locate_template( array( "common/forms/field.php" ), true, false );  ?>
	<?php endforeach; ?>

	<?php foreach ( $fields_files as $field ): ?>
		<?php global $wdg_current_field; $wdg_current_field = $field; ?>
		<?php locate_template( array( "common/forms/field.php" ), true, false );  ?>
	<?php endforeach; ?>
	
	<p class="align-left">
		<?php _e( "* Champs obligatoires", 'yproject' ); ?><br>
	</p>
	
	<div id="user-identify-docs-form-buttons">
		<button type="submit" class="button save red"><?php _e( "Envoyer les documents", 'yproject' ); ?></button>
	</div>
	
</form>