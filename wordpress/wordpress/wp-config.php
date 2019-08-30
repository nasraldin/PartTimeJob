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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'nasr' );

/** MySQL database password */
define( 'DB_PASSWORD', 'P@$sw0rd' );

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
define( 'AUTH_KEY',         'a`~)t@3+Th+_E%PkHVKFo{A{tZog.4?a8~{B~JFim<Jfk,a-}aFr- T4}t_`3,T5' );
define( 'SECURE_AUTH_KEY',  ' j<wuwbg$P ^xS1<`8w5BN&p]C_F6vMdAsoiV,fKb>uy7YfA,uutE~)aOa^+3Oam' );
define( 'LOGGED_IN_KEY',    ':;pj+)>F?Pd_xsMie[LrNbs?M&_jT_;a[[e_43P)kWp<LNs<:q6Pecve16d$#l~U' );
define( 'NONCE_KEY',        '[eX`1i-VS|v9-1&d~^u|)s$Y~W),,Y(AhJb,ZQBQ+&SqE]zj^7^>j)y(tle9Y9G{' );
define( 'AUTH_SALT',        '7c,%x.HXhdPVU_ x7TzFX^&0,>*u2`uOtNXh$<&UEv4=H%Is}uW4#ZU4,d4_,m5E' );
define( 'SECURE_AUTH_SALT', 'Flb?*xqTwm+,_|F+;BA1f0ih?O0!J&!1g~Ds<`n%ES>QpE7o@+CF|`6{/Rj]T$|8' );
define( 'LOGGED_IN_SALT',   'UW<[%rfYtcrqBVQ`I3H_nyq]_(bg6J,k(MNDSBF+*kT`EQaN-e{}sBDAxr.Js<#S' );
define( 'NONCE_SALT',       'j:azRWqB<f}1s<RKHj^|HJp_f1w^y I 4]PmaWr|z/LO+7< xM7xE|z$i`%KM|F{' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
