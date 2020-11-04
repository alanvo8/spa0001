<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package speedway
 */

?>
<div class="col-sm-6 col-md-6">	
	<div class="speedway-article">

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			
			<div class="row">
				<div class="col-xs-12 col-sm-6 col-md-6">
					<div class="post-category">
						<?php 
						$speed_post_category = get_the_category(); 			
						foreach( $speed_post_category  as $category ) {
							$category_link = get_category_link( $category->term_id );
							$category_name = $category->name;
							echo "<a href='{$category_link}'>{$category_name}</a>";
						}			
						?>
					</div>
				</div>
				<div class="col-xs-12 col-sm-6 col-md-6">
					<div class="post-date text-right">
						<?php echo get_the_date(); ?>
					</div>
				</div>
			</div>
			<!-- <div class="post-category-date-wrapper">
				
				
			</div> -->

			<?php speedway_post_thumbnail(); ?>

			<header class="entry-header">
				<?php
				if ( is_singular() ) :
					the_title( '<h1 class="entry-title">', '</h1>' );
				else :
					//the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
					echo '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . mb_strimwidth(get_the_title(), 0, 50, '...') . '</a></h2>';
				endif;

				if ( 'post' === get_post_type() ) :
					?>			
				<?php endif; ?>
			</header><!-- .entry-header -->

			

			<div class="entry-content">
				<?php
				$string = strip_tags( get_the_excerpt() );
				if (strlen($string) > 100) {

					// truncate string
					$stringCut = substr($string, 0, 100);
					$endPoint = strrpos($stringCut, ' ');

					//if the string doesn't contain any space then it will cut without word basis.
					$string = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
					//$string .= '... <a href="/this/story">Read More</a>';
				}
				echo $string;

				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'speedway' ),
						'after'  => '</div>',
					)
				);
				?>
			</div><!-- .entry-content -->

			<footer class="entry-footer">
				<?php edit_post_link(
					sprintf(
						wp_kses(
							/* translators: %s: Name of current post. Only visible to screen readers */
							__( 'Edit <span class="screen-reader-text">%s</span>', 'speedway' ),
							array(
								'span' => array(
									'class' => array(),
								),
							)
						),
						wp_kses_post( get_the_title() )
					),
					'<span class="edit-link">',
					'</span>'
				); ?>
			</footer><!-- .entry-footer -->
		</article><!-- #post-<?php the_ID(); ?> -->

	</div>
</div>