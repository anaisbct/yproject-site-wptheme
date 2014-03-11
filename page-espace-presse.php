<?php require_once("common.php"); ?>
<?php get_header(); ?>

<div id="content">
    <div class="padder">

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	    <?php printMiscPagesTop("Espace presse"); ?>
	    <div id="post_bottom_bg">
		<div id="post_bottom_content" class="center">
		    <div class="post_bottom_desc">
			<?php 
			query_posts( array(
			    'post_status' => 'publish',
			    'category_name' => 'revue-de-presse',
			    'orderby' => 'post_date',
			    'order' => 'desc'
			) );
			global $more;
			$more = 0;
			
			if ( have_posts() ) : ?>

				<?php bp_dtheme_content_nav( 'nav-above' ); ?>

				<?php while (have_posts()) : the_post(); ?>

					<?php do_action( 'bp_before_blog_post' ); ?>

					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

						<div class="post-content">
							<h2 class="posttitle"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							
							<p class="date"><?php echo get_the_date(); ?></p>

							<div class="entry">
							    <?php the_content( 'Lire la suite' ); ?>
							</div>
							
							<span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'buddypress' ), __( '1 Comment &#187;', 'buddypress' ), __( '% Comments &#187;', 'buddypress' ) ); ?></span></p>
						</div>

					</div>

					<?php do_action( 'bp_after_blog_post' ); ?>

				<?php endwhile; ?>

				<?php bp_dtheme_content_nav( 'nav-below' ); ?>

			<?php else : ?>
			    <div>Retrouvez bient&ocirc;t les actualit&eacute;s de l&apos;&eacute;quipe !</div>

			<?php endif; 
			
			wp_reset_query();
			?>
			    
			<?php the_content(); ?>
			
		    </div>
		</div>
	    </div>

	<?php endwhile; endif; ?>

    </div><!-- .padder -->
</div><!-- #content -->
	
<?php get_footer(); ?>