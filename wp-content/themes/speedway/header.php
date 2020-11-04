<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package speedway
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<nav class="navbar navbar-inverse">		
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<?php			
			the_custom_logo();
			if ( is_front_page() && is_home() ) :
				?>
				<?php if ( bloginfo( 'name' ) ) : ?>
					<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
				<?php endif; ?>
				<?php
			else :
				?>
				<?php if ( bloginfo( 'name' ) ) : ?>
					<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
				<?php endif; ?>
				<?php
			endif;
			$ass_description = get_bloginfo( 'description', 'display' );
			if ( $ass_description || is_customize_preview() ) :
				?>
				<p class="site-description"><?php echo $ass_description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
		</div>
		<?php			
		wp_nav_menu( array(
			'theme_location'  => 'menu-1',
			'depth'           => 2, // 1 = no dropdowns, 2 = with dropdowns.
			'container'       => 'div',
			'container_class' => 'collapse navbar-collapse',
			'container_id'    => 'myNavbar',
			'menu_class'      => 'nav navbar-nav navbar-right',
			'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
			'walker'          => new WP_Bootstrap_Navwalker(),
		) );
		?>	
	</div>
</nav>	


