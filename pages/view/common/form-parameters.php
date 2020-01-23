
<?php
    $page_controler = WDG_Templates_Engine::instance()->get_controler();
    $WDGUserDetailsForm = $page_controler->get_user_details_form();
    $fields_hidden = $WDGUserDetailsForm->getFields( WDG_Form_User_Details::$field_group_hidden );
    $fields_basics = $WDGUserDetailsForm->getFields( WDG_Form_User_Details::$field_group_basics );
    $fields_complete = $WDGUserDetailsForm->getFields( WDG_Form_User_Details::$field_group_complete );
    $fields_extended = $WDGUserDetailsForm->getFields( WDG_Form_User_Details::$field_group_extended );
    $form_feedback = $page_controler->get_user_form_feedback();
?>

<form method="POST" enctype="multipart/form-data" class="<?php echo $page_controler->get_form_css_classes();?>">
		
    <?php foreach ( $fields_hidden as $field ): ?>
        <?php global $wdg_current_field; $wdg_current_field = $field; ?>
        <?php locate_template( array( "common/forms/field.php" ), true, false );  ?>
    <?php endforeach; ?>

    <?php if ( !empty( $form_feedback[ 'errors' ] ) ): ?>
        <div class="wdg-message error">
            <?php _e( "Certaines erreurs ont bloqu&eacute; l'enregistrement de vos donn&eacute;es :", 'yproject' ); ?><br>
            <?php foreach ( $form_feedback[ 'errors' ] as $error ): ?>
                - <?php echo $error[ 'text' ]; ?><br>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if ( !empty( $form_feedback[ 'success' ] ) ): ?>
        <div class="wdg-message confirm">
            <?php foreach ( $form_feedback[ 'success' ] as $message ): ?>
                <?php echo $message; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php foreach ( $fields_basics as $field ): ?>
        <?php global $wdg_current_field; $wdg_current_field = $field; ?>
        <?php locate_template( array( "common/forms/field.php" ), true, false );  ?>
    <?php endforeach; ?>

    <?php if ( !empty( $fields_complete ) ): ?>
        <?php foreach ( $fields_complete as $field ): ?>
            <?php global $wdg_current_field; $wdg_current_field = $field; ?>
            <?php locate_template( array( "common/forms/field.php" ), true, false );  ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if ( !empty( $fields_extended ) ): ?>
        <?php foreach ( $fields_extended as $field ): ?>
            <?php global $wdg_current_field; $wdg_current_field = $field; ?>
            <?php locate_template( array( "common/forms/field.php" ), true, false );  ?>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <p class="align-left">
        <?php _e( "* Champs obligatoires", 'yproject' ); ?><br>
    </p>

    <div id="user-details-form-buttons">
        <button type="submit" class="button save red <?php if ($page_controler->get_controler_name() == 'tableau-de-bord' && !$page_controler->get_campaign()->is_preparing()){ ?>confirm<?php } ?>">
            <?php _e( "Enregistrer les modifications", 'yproject' ); ?>
        </button>
    </div>
    
</form>
    