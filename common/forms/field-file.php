<?php global $wdg_current_field; ?>
<div class="field-description">
	<?php _e( "Les formats de documents autoris&eacute;s sont : PDF, JPG, JPEG, BMP, GIF, TIF, TIFF et PNG.", 'yproject' ); ?>
</div>
<input type="file" name="<?php echo $wdg_current_field[ 'name' ]; ?>" id="<?php echo $wdg_current_field[ 'name' ]; ?>">
<?php if ( !empty( $wdg_current_field[ 'value' ] ) ): ?>
	<br><br>
	<a id="<?php echo $wdg_current_field[ 'name' ]; ?>" class="button blue-pale download-file" target="_blank" href="<?php echo $wdg_current_field[ 'value' ]; ?>"><?php _e("Voir le fichier envoy&eacute; le"); ?> <?php echo $wdg_current_field[ 'options' ]; ?></a>
	<br>
<?php endif;