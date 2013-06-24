<?php 
    global $facebook_infos;
    global $twitter_infos;
    
    /* Récupération des infos Facebook */
    require_once("_external/facebook/facebook.php");
    $facebook = new Facebook(array(
	'appId'  => '370169356424813',
	'secret' => 'e1f7ee659011a09c8765cf379719104f',
    ));
    $fb_infos = $facebook->api('http://graph.facebook.com/381460615282040'); 
    $facebook_infos = $fb_infos['likes'];
    /* Récupération des infos Twitter */
    $url = "http://twitter.com/users/show/yproject_co";
    $response = file_get_contents ( $url );
    $t_profile = new SimpleXMLElement ( $response );
    $twitter_infos = $t_profile->followers_count;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
		<?php if ( current_theme_supports( 'bp-default-responsive' ) ) : ?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" /><?php endif; ?>
		<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>
		
		<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

		<?php do_action( 'bp_head' ); ?>
		<?php wp_head(); ?>
		
		<script type="text/javascript" src="<?php if (WP_DEBUG) echo 'http://localhost/taffe/wp-yproject-site/wp-content/themes/yproject'; else echo get_stylesheet_directory_uri(); ?>/_inc/js/common.js"></script>
	</head>

	<body <?php body_class(); ?> id="bp-default">

		<?php do_action( 'bp_before_header' ); ?>

		<nav id="navigation" role="navigation">
		    <div class="center">
			<ul id="nav">
			    <li class="page_item"><a href=""><img src="" width="32" height="16" /></a></li>
			    <li class="page_item">
				<a href=""><?php echo __('Decouvrir les projets', 'yproject'); ?></a>
				<ul>
				    <li class="page_item_out"><a href="#">CAT1</a></li>
				    <li class="page_item_out"><a href="#">CAT2</a></li>
				</ul>
			    </li>
			    <li class="page_item"><a href=""><?php echo __('Proposer un projet', 'yproject'); ?></a></li>
			    <li class="page_item"><a href=""><?php echo __('Comment ca marche ?', 'yproject'); ?></a></li>
			    <li class="page_item_out">
				<a href=""><?php echo __('Communaute', 'yproject'); ?></a>
				<ul>
				    <li class="page_item_out"><a href="#"><?php echo __('Fil dactivite', 'yproject'); ?></a></li>
				    <li class="page_item_out"><a href="#"><?php echo __('Qui sommes-nous ?', 'yproject'); ?></a></li>
				    <li class="page_item_out"><a href="#"><?php echo __('Blog', 'yproject'); ?></a></li>
				</ul>
			    </li>
			    <li class="page_item_out" id="menu_item_facebook"><a href="https://www.facebook.com/pages/Y-Project/381460615282040" target="_blank" title="Notre page Facebook"><img src="" width="16" height="16" /></a></li>
			    <li class="page_item_out" id="menu_item_twitter"><a href="https://twitter.com/yproject_co" target="_blank" title="Notre compte Twitter"><img src="" width="16" height="16" /></a></li>
			    <li class="page_item_out page_item_inverted" id="menu_item_connection">
				<a class="page_item_inverted" href=""><?php echo __('Connexion', 'yproject'); ?></a>
				<ul>
				    <li class="page_item_out"><a href=""><?php echo __('CONNEXION', 'yproject'); ?></a></li>
				    <li class="page_item_out"><a href=""><?php echo __('FACEBOOK', 'yproject'); ?></a></li>
				    <li class="page_item_out"><a href=""><?php echo __('Sinscrire', 'yproject'); ?></a></li>
				</ul>
			    </li>
			</ul>
		    </div>
		</nav>
		<div id="fb_infos">
		    <?php echo $facebook_infos; ?>
		</div>
		<div id="twitter_infos">
		    <?php echo $twitter_infos; ?>
		</div>
	    
		<header>
		    <div id="site_name" class="center">
			    <h1 id="logo" role="banner"><a href="<?php echo home_url(); ?>" title="<?php _ex( 'Home', 'Home page banner link title', 'buddypress' ); ?>"><?php bp_site_name(); ?></a></h1>
			    <br />
		    </div>

		    <?php do_action( 'bp_header' ); ?>
		</header>

		<?php do_action( 'bp_after_header'     ); ?>
		<?php do_action( 'bp_before_container' ); ?>

		<div id="container" class="center">
