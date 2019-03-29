<?php
/*
PostType Page Template: Download Post Template
Description: This part is optional
*/
?>
<?php get_header(); ?>
	
		<div id="content" class="col-md-12">
			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'download' ); ?>
                <?php
					do_action( 'colormag_before_comments_template' );
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || '0' != get_comments_number() )
						comments_template();
	      		do_action ( 'colormag_after_comments_template' );
				?>
            <?php endwhile; ?>
            
		</div><!-- #content -->

	<?php do_action( 'colormag_after_body_content' ); ?>

<?php get_footer(); ?>