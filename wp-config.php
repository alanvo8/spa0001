<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'alanvo_spa0001' );

/** MySQL database username */
define( 'DB_USER', 'alanvo_spa0001' );

/** MySQL database password */
define( 'DB_PASSWORD', '{2mfJ!cXd9@M' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '(EpzUmSQ{]&JV-O4<uN$&8(2av3hPj2?_ s=,>!rrBUv_@G^`(&0 0jxfQ~bXYqp' );
define( 'SECURE_AUTH_KEY',  '(>/$62Xk$M?_|ku*/*>c(u7xIYL4li[/0BGhtq<v*O^J2H~Avj~?y~/sT6N[YmIt' );
define( 'LOGGED_IN_KEY',    'T_se2&l!}be%8Cl8&SU!flf`sxT]]~u:KvF|N4cjL3vN<`P*!URn~f?jWEs17BzI' );
define( 'NONCE_KEY',        'a: {{hxb)gnJ-]. csxeQ}%`!*VCm4UpD|/=vr#9cz;JGLx?{S]98I2j?6F=lxn ' );
define( 'AUTH_SALT',        'KIYwCU;a$*$8Z>S|kf9-m}Et$B^w2UerAGH1^l8.PS%HJ.|gUYQb=yl,mRMKq2u}' );
define( 'SECURE_AUTH_SALT', 'Gox=c4R,e`3uBNPRw9F)!glFn*6{L^,rsG<0:18n#~b[G<t-TYYJGMT`R:){sL?N' );
define( 'LOGGED_IN_SALT',   'eFz7UnQg7p0R3%sriW-!ShQ*4wthM.qkpWs8-UVp{0#H<; 2*AlEZ.Xthzt{+x9j' );
define( 'NONCE_SALT',       'Gcl9V/x9vf/kI@Q7$IzEUc](!|UOWP_JGEo1tD`iDdFOeb8kaucSrui2 2R-1 9>' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'sp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
