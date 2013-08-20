<?php get_header( 'buddypress' ); ?>

    <div id="content" class="center">
	<div class="padder_more">

	    <?php do_action( 'bp_before_register_page' ); ?>

	    <div class="page" id="register-page">

		    <form action="" name="signup_form" id="signup_form" class="standard-form" method="post" enctype="multipart/form-data">

		    <?php if ( 'registration-disabled' == bp_get_current_signup_step() ) : ?>
			    <?php do_action( 'template_notices' ); ?>
			    <?php do_action( 'bp_before_registration_disabled' ); ?>

				    <p><?php _e( 'Les inscriptions ne sont pas ouvertes pour l&apos;instant.', 'yproject' ); ?></p>

			    <?php do_action( 'bp_after_registration_disabled' ); ?>
		    <?php endif; // registration-disabled signup setp ?>

		    <?php if ( 'request-details' == bp_get_current_signup_step() ) : ?>

			    <h2><?php _e( 'Inscription', 'yproject' ); ?></h2>

			    <?php do_action( 'template_notices' ); ?>

			    <?php /***** Basic Account Details ******/ ?>
			    <?php do_action( 'bp_before_account_details_fields' ); ?>
			    <div class="register-section" id="basic-details-section">
				<h4><?php _e( 'Informations de base', 'yproject' ); ?></h4>
				<p><?php _e( 'Ces informations sont n&eacute;cessaires pour obtenir un compte.', 'yproject' ); ?></p>

				<label for="signup_username"><?php _e( 'Identifiant', 'yproject' ); ?> *</label>
				<?php do_action( 'bp_signup_username_errors' ); ?>
				<input type="text" name="signup_username" id="signup_username" value="<?php bp_signup_username_value(); ?>" /><br />

				<label for="signup_email"><?php _e( 'Adresse e-mail', 'yproject' ); ?> *</label>
				<?php do_action( 'bp_signup_email_errors' ); ?>
				<input type="text" name="signup_email" id="signup_email" value="<?php bp_signup_email_value(); ?>" /><br />

				<label for="signup_password"><?php _e( 'Mot de passe', 'yproject' ); ?> *</label>
				<?php do_action( 'bp_signup_password_errors' ); ?>
				<input type="password" name="signup_password" id="signup_password" value="" /><br />

				<label for="signup_password_confirm"><?php _e( 'Confirmation du mot de passe', 'yproject' ); ?> *</label>
				<?php do_action( 'bp_signup_password_confirm_errors' ); ?>
				<input type="password" name="signup_password_confirm" id="signup_password_confirm" value="" /><br />
			    </div>
			    <?php do_action( 'bp_after_account_details_fields' ); ?>

			    
			    
			    <?php /***** Extra Profile Details ******/ ?>
			    <?php if ( bp_is_active( 'xprofile' ) ) : ?>

				    <?php do_action( 'bp_before_signup_profile_fields' ); ?>

				    <div class="register-section" id="profile-details-section">

					    <h4><?php _e( 'Informations compl&eacute;mentaires', 'yproject' ); ?></h4>
					    <p><?php _e( 'Ces informations sont n&eacute;cessaires pour investir dans un projet.', 'yproject' ); ?></p>

					    <?php /* Use the profile field loop to render input fields for the 'base' profile field group */ ?>
					    <?php if ( bp_is_active( 'xprofile' ) ) : if ( bp_has_profile( 'profile_group_id=1' ) ) : while ( bp_profile_groups() ) : bp_the_profile_group(); ?>

					    <?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

						    <div class="editfield">

							    <?php if ( 'textbox' == bp_get_the_profile_field_type() ) : ?>

								    <label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
								    <?php do_action( bp_get_the_profile_field_errors_action() ); ?>
								    <input type="text" name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>" value="<?php bp_the_profile_field_edit_value(); ?>" />

							    <?php endif; ?>

							    <?php if ( 'textarea' == bp_get_the_profile_field_type() ) : ?>

								    <label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
								    <?php do_action( bp_get_the_profile_field_errors_action() ); ?>
								    <textarea rows="5" cols="40" name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_edit_value(); ?></textarea>

							    <?php endif; ?>

							    <?php if ( 'selectbox' == bp_get_the_profile_field_type() ) : ?>

								    <label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
								    <?php do_action( bp_get_the_profile_field_errors_action() ); ?>
								    <select name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>">
									    <?php bp_the_profile_field_options(); ?>
								    </select>

							    <?php endif; ?>

							    <?php if ( 'multiselectbox' == bp_get_the_profile_field_type() ) : ?>

								    <label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
								    <?php do_action( bp_get_the_profile_field_errors_action() ); ?>
								    <select name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>" multiple="multiple">
									    <?php bp_the_profile_field_options(); ?>
								    </select>

							    <?php endif; ?>

							    <?php if ( 'radio' == bp_get_the_profile_field_type() ) : ?>

								    <div class="radio">
									    <span class="label"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></span>

									    <?php do_action( bp_get_the_profile_field_errors_action() ); ?>
									    <?php bp_the_profile_field_options(); ?>

									    <?php if ( !bp_get_the_profile_field_is_required() ) : ?>
										    <a class="clear-value" href="javascript:clear( '<?php bp_the_profile_field_input_name(); ?>' );"><?php _e( 'Clear', 'buddypress' ); ?></a>
									    <?php endif; ?>
								    </div>

							    <?php endif; ?>

							    <?php if ( 'checkbox' == bp_get_the_profile_field_type() ) : ?>

								    <div class="checkbox">
									    <span class="label"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></span>

									    <?php do_action( bp_get_the_profile_field_errors_action() ); ?>
									    <?php bp_the_profile_field_options(); ?>
								    </div>

							    <?php endif; ?>

							    <?php if ( 'datebox' == bp_get_the_profile_field_type() ) : ?>

								    <div class="datebox">
									    <label for="<?php bp_the_profile_field_input_name(); ?>_day"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
									    <?php do_action( bp_get_the_profile_field_errors_action() ); ?>

									    <select name="<?php bp_the_profile_field_input_name(); ?>_day" id="<?php bp_the_profile_field_input_name(); ?>_day">
										    <?php bp_the_profile_field_options( 'type=day' ); ?>
									    </select>

									    <select name="<?php bp_the_profile_field_input_name(); ?>_month" id="<?php bp_the_profile_field_input_name(); ?>_month">
										    <?php bp_the_profile_field_options( 'type=month' ); ?>
									    </select>

									    <select name="<?php bp_the_profile_field_input_name(); ?>_year" id="<?php bp_the_profile_field_input_name(); ?>_year">
										    <?php bp_the_profile_field_options( 'type=year' ); ?>
									    </select>
								    </div>

							    <?php endif; ?>

							    <?php do_action( 'bp_custom_profile_edit_fields' ); ?>

							    <p class="description"><?php bp_the_profile_field_description(); ?></p>

						    </div>

					    <?php endwhile; ?>

					    <input type="hidden" name="signup_profile_field_ids" id="signup_profile_field_ids" value="<?php bp_the_profile_group_field_ids(); ?>" />

					    <?php endwhile; endif; endif; ?>

				    </div><!-- #profile-details-section -->

				    <?php do_action( 'bp_after_signup_profile_fields' ); ?>

			    <?php endif; ?>

				    
				    
			    <?php do_action( 'bp_before_registration_submit_buttons' ); ?>
			    <div class="submit">
				<input type="submit" name="signup_submit" id="signup_submit" />
			    </div>
			    <?php do_action( 'bp_after_registration_submit_buttons' ); ?>
				    

			    <?php wp_nonce_field( 'bp_new_signup' ); ?>

		    <?php endif; // request-details signup step ?>

				    
				    
		    <?php if ( 'completed-confirmation' == bp_get_current_signup_step() ) : ?>

			    <h2><?php _e( 'Un e-mail de confirmation vous a &eacute;t&eacute; envoy&eacute;.', 'yproject' ); ?></h2>

			    <?php do_action( 'template_notices' ); ?>
			    <?php do_action( 'bp_before_registration_confirmed' ); ?>

			    <?php if ( bp_registration_needs_activation() ) : ?>
				    <p><?php _e( 'Votre compte est maintenant cr&eacute;&eacute;. Pour l&apos;utiliser, rendez-vous sur l&apos;e-mail que nous avons envoy&eacute;.', 'yproject' ); ?></p>
			    <?php else : ?>
				    <p><?php _e( 'Votre compte est maintenant cr&eacute;&eacute;. Vous pouvez &agrave; pr&eacute;sent vous identifier.', 'yproject' ); ?></p>
			    <?php endif; ?>

			    <?php do_action( 'bp_after_registration_confirmed' ); ?>

		    <?php endif; // completed-confirmation signup step ?>

				    
				    
		    <?php do_action( 'bp_custom_signup_steps' ); ?>

		    </form>

	    </div>

	    <?php do_action( 'bp_after_register_page' ); ?>

	</div><!-- .padder -->
    </div><!-- #content -->

    <script type="text/javascript">
	jQuery(document).ready( function() {
	    if ( jQuery('div#blog-details').length && !jQuery('div#blog-details').hasClass('show') )
		    jQuery('div#blog-details').toggle();

	    jQuery( 'input#signup_with_blog' ).click( function() {
		    jQuery('div#blog-details').fadeOut().toggle();
	    });
	});
    </script>

<?php get_footer( 'buddypress' ); ?>
