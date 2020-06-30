<?php
global $stylesheet_directory_uri;
$page_controler = WDG_Templates_Engine::instance()->get_controler();
?>

<header class="wdg-component-slider home-slider">
    <div class="slider-container">
        <div id="slider">
			<div class="slider-item" id="slide-1" style="left: 0px;">
				<img class="slider-motif-left-haut" src="<?php echo $stylesheet_directory_uri; ?>/images/slider/slider-trame-haut-gauche-01.png" alt="Slider motif haut gauche" />
				<img class="slider-motif-right" src="<?php echo $stylesheet_directory_uri; ?>/images/slider/slider-motif-trame-droite.png" alt="Slider motif droite" />
				<img class="slide" id="img-slide-1" src="<?php echo $stylesheet_directory_uri; ?>/images/slider/slider-01.jpg" alt="Image accueil WE DO GOOD" /> 

				<div class="message-banner">
					<p class="screen-message"><?php _e( "Nous activons<br />une finance à impact positif<br />en développant<br />les levées de fonds en royalties", 'yproject' ); ?></p>
					<img class="slider-motif-left-bas" src="<?php echo $stylesheet_directory_uri; ?>/images/slider/slider-trame-bas-gauche-01.png" alt="Slider motif bas gauche" />
				</div>
			</div>
        </div>
    </div>
	
	<div id="button-container">
		<a class="button-slide" href="<?php echo home_url( '/financement/' ); ?>"><?php _e("Financer mon projet", "yproject") ?></a>
		<a class="button-slide" href="<?php echo home_url( '/investissement/' ); ?>"><?php _e("Investir sur un projet", "yproject") ?></a>
	</div>
</header> <!-- .wdg-component-slider -->

<?php
/******************************************************************************/
// STATS PROJECTS
/******************************************************************************/
?>
			
<?php $stats_list = $page_controler->get_stats_list(); ?>
<section class="project-stats">
	<div>
		<div class="left">
			<div>
				<img src="<?php echo $stylesheet_directory_uri; ?>/images/template-home/picto-montgolfiere-noir-h100.png" alt="montgolfiere" width="115" height="100">
			</div>
			<div>
				<span><?php echo number_format( $stats_list[ 'count_amount' ], 0, '', ' ' ); ?> &euro;</span><br>
					<?php _e( "lev&eacute;s", 'yproject' ); ?>
			</div>
		</div>
		<div class="left">
			<div>
				<img src="<?php echo $stylesheet_directory_uri; ?>/images/template-home/picto-ensemble-noir-h100.png" alt="ensemble" width="139" height="100">
			</div>
			<div>
				<span><?php echo number_format( $stats_list[ 'count_people' ], 0, '', ' ' ); ?></span><br>
					<?php _e( "investisseurs", 'yproject' ); ?>
			</div>
		</div>
		<div class="left">
			<div>
				<img src="<?php echo $stylesheet_directory_uri; ?>/images/template-home/picto-monnaie-noir-h100.png" alt="monnaie" width="102" height="100">
			</div>
			<div>
				<span><?php echo number_format( $stats_list[ 'royaltying_projects' ], 0, '', ' ' ); ?></span><br>
				<?php _e( "entreprises royaltisent*", 'yproject' ); ?>
			</div>
		</div>
		
		<div class="clear"></div>
		<p><?php _e( "definition_royaltiser", 'yproject' ); ?></p>

	</div>
</section>
				
<?php
/******************************************************************************/
// FIN STATS PROJECTS
/******************************************************************************/
?>

<?php
/******************************************************************************/
// NEWS
/******************************************************************************/
?>
<section class="news">
	<div class="news-container">
		<div class="news-pic">
			<img src="<?php echo $stylesheet_directory_uri; ?>/images/template-home/txt-100-projets-finances.png" alt="100 projets financ&eacute;s">
		</div>
		<div class="news-text">
			<div class="news-text-bg">
				<span>
					<?php _e( "Plus de 100 projets financ&eacute;s en royalties avec&nbsp;WE&nbsp;DO&nbsp;GOOD", 'yproject' ); ?>
				</span>
				<a class="button transparent" href="https://blog.wedogood.co/retours-experience-entrepreneurs/100-levees-de-fonds-wedogood/" target="_blank"><?php _e( "D&eacute;couvrir le panorama", 'yproject' ); ?></a>
			</div>
		</div>
	</div>
</section>
<?php
/******************************************************************************/
// FIN NEWS
/******************************************************************************/
?>

<?php
/******************************************************************************/
// PROJECT LIST
/******************************************************************************/
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
	<a class="home-button-project see-more red" href="<?php echo home_url( '/les-projets/' ); ?>"><?php _e("D&eacute;couvrir tous les projets","yproject" ) ?></a>
</section> <!-- section.wdg-component-projects-preview -->

<?php
/******************************************************************************/
// FIN PROJECT LIST
/******************************************************************************/
?>


<section id="home-video">
    <div id="video-content">
        <h2 class="standard">/ <?php _e("Comment &ccedil;a marche ?", "yproject")?> /</h2>
        <div class="home_video">
            <div class="video-container w570 hidden"></div>
            <div class="video-container w320 hidden"></div>

            <div class="button-video"><img src="<?php echo $stylesheet_directory_uri; ?>/images/header-video-button-nb.jpg" alt="Bouton video" /></div>
            <div class="button-video-shadows hidden-inf997">
				<img src="<?php echo $stylesheet_directory_uri; ?>/images/header-video-button-shadow2.png" alt="Ombre video 1">
				<img src="<?php echo $stylesheet_directory_uri; ?>/images/header-video-button-shadow1.png" alt="Ombre video 2">
            </div>
        </div>
    </div>
</section> <!-- section#home-video -->


<div id="home-press" class="hidden-inf997">
	<a href="<?php echo home_url( '/a-propos/press-book/' ); ?>"><img id="press-banner" src="<?php echo $stylesheet_directory_uri; ?>/images/bandeau-presse-2019.png" alt="presse" /></a>
</div> <!-- section#home-press -->