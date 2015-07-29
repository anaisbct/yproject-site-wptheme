<?php
    if (is_user_logged_in()) wp_redirect(home_url());
?>
<?php get_header(); ?>
    <div id="content">
	<div class="padder">
	    <div class="center_small margin-height">
		    <?php if (isset($_GET["login"]) && $_GET["login"] == "failed") {?>
		    <div class="errors">
			    <?php _e('Erreur d&apos;identification', 'yproject'); ?>
		    </div>
		    <?php } ?>
		    <?php if (YPUsersLib::has_login_errors()): ?>
		    <div class="errors">
			    <?php echo YPUsersLib::display_login_errors(); ?>
		    </div>
		    <?php endif; ?>

		    <div style="text-transform: uppercase; margin-bottom: 10px; text-align: left;" id="submenu_item_connection_login"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/triangle_noir_connexion.jpg" alt="logo triangle" class="vert-align" width="25" height="25" />&nbsp;Connexion</div>

		    <form method="post" action="" name="login-form" id="sidebar-login-form" class="standard-form">
			    <label class="standard-label"><?php _e('Identifiant', 'yproject'); ?></label>
			    <input style="margin-bottom: 5px; width: 254px;" type="text" name="log" class="input" value="<?php if ( isset( $user_login) ) echo esc_attr(stripslashes($user_login)); ?>" placeholder="<?php _e('Identifiant', 'yproject'); ?>" />
			    <br />

			    <label class="standard-label"><?php _e('Mot de passe', 'yproject'); ?></label>
			    <input type="password" name="pwd" class="input" value="" /> 
			    <input type="submit" name="wp-submit" id="sidebar-wp-submit" style="width: 100px; background: #FFF;" value="<?php _e('Connexion', 'yproject'); ?>" />
			    <br />

			    <?php $page_forgotten = get_page_by_path('mot-de-passe-oublie'); ?>
			    <span class="link-forgotten">(<a href="<?php echo get_permalink($page_forgotten->ID); ?>">Mot de passe oubli&eacute;</a>)</span>

			    <p class="forgetmenot">
				    <input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" /> <label><?php _e('Se souvenir de moi', 'yproject'); ?></label>
			    </p>

			    <input type="hidden" name="redirect-page" value="<?php echo YPUsersLib::get_login_redirect_page(); ?>" />
			    <input type="hidden" name="login-form" value="1" />
		    </form>

		    <hr class="form-separator" />

		    <div class="align-center"><div id="connexion_facebook_container"><a href="javascript:void(0);" class="social_connect_login_facebook"><img style="border-right: 1px solid #FFFFFF; width:25px; height:25px;" src="<?php echo get_stylesheet_directory_uri(); ?>/images/facebook_connexion.jpg" alt="connexion facebook"class="vert-align"/><span style=" font-size:12px;">&nbsp;Se connecter avec Facebook</span></a></div></div>

		    <div class="hidden"><?php dynamic_sidebar( 'sidebar-1' ); ?></div>

		    <hr class="form-separator" />

		    <?php $page_connexion_register = get_page_by_path('register'); ?>

		    <div class="post_bottom_buttons_connexion align-center">
			<div style="display: inline-block; background-color: #3E3E40; text-align: left;" id="submenu_item_connection_register" class="dark">
			<a href="<?php echo get_permalink($page_connexion_register->ID); ?>"><img width="25" height="25" src="<?php echo get_stylesheet_directory_uri(); ?>/images/triangle_blc_connexion.jpg" alt="triangle blanc"><span style="font-size: 9pt; vertical-align: 8px; color: #FFF; ">Cr&eacute;er un compte</span></a>
			</div>
		    </div>
	    </div>
	</div>
    </div>

<?php get_footer(); ?>