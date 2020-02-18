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
define( 'DB_NAME', 'habitat' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         '9MR5!W$L_VOC`IK?(6UzR,li<F=eb<DoP{!PXeon=aB$Vj9F*11%p+=,s`8`@^Tb' );
define( 'SECURE_AUTH_KEY',  '-WT1mg1?~qGU,u002G4P<^=I(]H)NI?Nm]!uK94(.FtPQ=`Scr`EX.[F/CiV`^+;' );
define( 'LOGGED_IN_KEY',    'Fb[ZG.&AX;xm%G6~&Yr8N2}qH.Q^jysbNT)geKM7HL8N.ebCz*8E=]q&g}gyoS2p' );
define( 'NONCE_KEY',        'ARC!|(oyZtBH!*DJ6MxqaysuLU#}w#@3eI]APh1Kic1thTeGVUOl_aos=aZHkdY1' );
define( 'AUTH_SALT',        'Viw89;9F56K[e`w0,2.S_C31EP%_E%Rv@B3zwtj.aO^y&O_dw2u<i:Cn[X6,w>qR' );
define( 'SECURE_AUTH_SALT', ',]4q*@GFH!ZtoeNkZdY|F+P:7CH!y*W<2DL3s3*f$Mj1^xzul[Mlvs7bsNqzdZ@5' );
define( 'LOGGED_IN_SALT',   '1qczG:&yop1O[Y)G.S1sRvY<.I!=qi!cVejv-Yvx~_g_:nsiR0>E>5I+?Q5][>ie' );
define( 'NONCE_SALT',       'LEbK3w~<QWemlJYW/j;QQFbbcn2B.b_6{A(2+oz3Ow?1y}){oH50$%/AIa`pa|}w' );

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
