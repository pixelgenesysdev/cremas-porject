<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'cremas-porject' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'od/xWnlGm]$P+?c-N}bsxy<xcDtO0)HN%7@?NM8$^FC$&E51PM51mpK-w,CEljDq' );
define( 'SECURE_AUTH_KEY',  ']<igEj@_akNO:g3~FIF_,JV8ExVza^<pFz*xfm~${s0or`9F}1f1kg=K0^kQ$Ik|' );
define( 'LOGGED_IN_KEY',    '<-Pg#:Lkuo^3.-Px7.*Cu.vS%^EA0tBw m;p@VwP-Sc5:f-n94$}biB!Z<D)FyQO' );
define( 'NONCE_KEY',        ')1C0KuO0~VW{YfUM@3Piq;?G,#|_Oeg]3;R%<wO;i7uX,6YX2(pRR;0?m,q|BqiH' );
define( 'AUTH_SALT',        '_c%,Kx{niQ&J$i[-z%FI.RfX#/7#e>[*D)-uBE*jy+1Tk!Qr98oY+v`hUV8C6o:t' );
define( 'SECURE_AUTH_SALT', 'Pyopqbbt~6H8P_tB(-o5l+Jh09T+@k>}Zz-7J+){4jogex`r4Hit%PSA!{d[.6-Q' );
define( 'LOGGED_IN_SALT',   ')B</.AW:M0xI0B3sJ +^a],dcdmtkhH_&5}O~m;iluQY8P%Tid[Xyb#,2YRimb~Q' );
define( 'NONCE_SALT',       '?z axDtS`kj[Y:HsSGyKk$j5gP0h$W*1%=xuJnSkWyT5g~X&~kkH)N~*(4_pF=Wd' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
