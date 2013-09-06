<?php 
    get_header();
    require_once("common.php");
    date_default_timezone_set("Europe/Paris");
?>

<div id="content">
    <div class="padder">

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	    <?php printCommunityTop("Communaute"); ?>
	    <div id="post_bottom_bg">
		<div id="post_bottom_content" class="center">
		    <div class="left post_bottom_desc">
			<?php the_content(); ?>

			<h2 class="underlined"><?php _e("Derniers inscrits", "yproject"); ?></h2>
			<?php 
			    $args = array (
				"orderby" => "registered", 
				"order" => "DESC", 
				"number" => 5
			    );
			    $user_query = new WP_User_Query( $args ); 
			    if ( ! empty( $user_query->results ) ) {
				echo '<ul class="last_subscribers">';
				foreach ( $user_query->results as $user ) {
				    $now = new DateTime(date("Y-m-d H:i:s"));
				    $registration = new DateTime($user->user_registered);
				    $diff = $now->diff($registration);
				    if ($diff->y > 0) $time = $diff->format("%y années");
				    else if ($diff->m > 0) $time = $diff->format("%m mois");
				    else if ($diff->days > 0) $time = $diff->format("%d jours");
				    else if ($diff->h > 0) $time = $diff->format("%h jours");
				    else if ($diff->i > 0) $time = $diff->format("%i minutes");
				    else if ($diff->s > 0) $time = $diff->format("%s secondes");
				    echo '<li>' . $user->display_name . __(" a rejoint WeDoGood - Il y a ", "yproject") . $time . '</li>';
				}
				echo '</ul>';
			    }
			?>
		    </div>

		    <?php printCommunityMenu(); ?>
		    <div style="clear: both"></div>
		</div>
	    </div>

	<?php endwhile; endif; ?>

    </div><!-- .padder -->
</div><!-- #content -->
	
<?php get_footer(); ?>