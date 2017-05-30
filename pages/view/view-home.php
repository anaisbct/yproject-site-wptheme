<?php global $page_controler, $WDG_cache_plugin, $stylesheet_directory_uri; ?>

<header class="wdg-component-slider home-slider">
    <div class="slider-container"> 
        <div class="slider-choice">
            <span class="num-slide active-slide" id="span-1">1</span>
            <span class="num-slide inactive-slide" id="span-2">2</span>  
            <span class="num-slide inactive-slide" id="span-3">3</span>
        </div>  
        <div id="slider">
            <?php
            $slider = $page_controler->get_slider();
			$ii = 0;
            foreach ( $slider as $img => $text ): ?>
                <?php $ii++; ?>
				<div class="slider-item" id="slide-<?php echo $ii?>" style="<?php if ($ii > 1){ ?>display: none;<?php } else { ?>left: 0px;<?php } ?>">
                    <img class="slider-motif-left-haut" src="<?php echo $stylesheet_directory_uri; ?>/images/slider/slider-trame-haut-gauche-01.png" alt="Slider motif haut gauche" />
                    <img class="slider-motif-right" src="<?php echo $stylesheet_directory_uri; ?>/images/slider/slider-motif-trame-droite.png" alt="Slider motif droite" />
                    <img class="slide" id="img-slide-<?php echo $ii?>" src="<?php echo $stylesheet_directory_uri; ?>/images/slider/<?php echo $img; ?>" alt="Slider image <?php echo $ii; ?>" /> 
                    
					<div class="message-banner">
						<p class="screen-message"><?php echo $text; ?></p>
						<img class="slider-motif-left-bas" src="<?php echo $stylesheet_directory_uri; ?>/images/slider/slider-trame-bas-gauche-01.png" alt="Slider motif bas gauche" />
					</div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
	
	<div id="button-container">
		<a class="button-slide" href="<?php echo home_url( '/financement' ); ?>"><?php _e("Financer mon projet", "yproject") ?></a>
		<a class="button-slide" href="<?php echo home_url( '/investissement' ); ?>"><?php _e("Investir sur un projet", "yproject") ?></a>
	</div>
</header> <!-- .wdg-component-slider -->

<?php
/******************************************************************************/
// PROJECT LIST
/******************************************************************************/
$projects_html = $page_controler->get_projects_html();
?>

<?php if ( !$projects_html ): ?>

<?php
ob_start();
date_default_timezone_set("Europe/London");
?>

<section class="wdg-component-projects-preview">
    <h2 class="standard">/ <?php _e("les projets", "yproject") ?> /</h2>
	<div class="project-slider">
		<div class="block-projects">
			<?php
			global $project_id;
			// Affiche les 3 projets les plus récents entre ceux en cours, en vote et financés
			$all_projects = $page_controler->get_projects_list();
			foreach ($all_projects as $project_id) {
				locate_template( array("projects/preview.php"), true, false );
			}
			?>
		</div>
	</div>
	<a class="home-button-project see-more red" href="<?php echo home_url( '/les-projets' ); ?>"><?php _e("D&eacute;couvrir tous les projets","yproject" ) ?></a>
</section> <!-- section.wdg-component-projects-preview -->

<?php
	$cache_home = ob_get_contents();
	$page_controler->set_projects_html( $cache_home );
	ob_end_clean();
?>

<?php endif; ?>

<?php echo $page_controler->get_projects_html(); ?>

<?php
/******************************************************************************/
// FIN PROJECT LIST
/******************************************************************************/
?>


<section id="home-video">
    <div id="video-content">
        <h2 class="standard">/ <?php _e("Comment &ccedil;a marche ?", "yproject")?> /</h2>
        <div class="home_video">
            <div class="video-container w570 hidden"><?php echo wp_oembed_get('https://youtu.be/QJmhrCG5acU', array("width" => 570)); ?></div>
            <div class="video-container w320 hidden"><?php echo wp_oembed_get('https://youtu.be/QJmhrCG5acU', array("width" => 320)); ?></div>

            <div class="button-video"><img src="<?php echo $stylesheet_directory_uri; ?>/images/header-video-button-nb.jpg" alt="Bouton video" /></div>
            <div class="button-video-shadows hidden-inf997">
				<img src="<?php echo $stylesheet_directory_uri; ?>/images/header-video-button-shadow2.png" alt="Ombre video 1" />
				<img src="<?php echo $stylesheet_directory_uri; ?>/images/header-video-button-shadow1.png" alt="Ombre video 2" />
            </div>
        </div>
    </div>
</section> <!-- section#home-video -->


<div id="home-press" class="hidden-inf997">
	<a href="<?php echo home_url( '/press-book' ); ?>"><img id="press-banner" src="<?php echo $stylesheet_directory_uri; ?>/images/bandeau-presse-mars-2017.png" alt="presse" /></a>
</div> <!-- section#home-press -->