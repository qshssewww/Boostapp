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
define('DB_NAME', 'boostapp_wp');

/** MySQL database username */
define('DB_USER', 'boostapp_site');

/** MySQL database password */
define('DB_PASSWORD', 'yn4SQrkBSHTcZKAF');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'wc9gd?8rR;66j0 cl4:T(<Vixm1 ;ROLOpS0B(nt2l?r!IS*~JL,0d;Cu~iVFlC6');
define('SECURE_AUTH_KEY',  'SoxmK^q;OC>8@`!P{USJ>g.n;*Yk<8@>i49+-[mTvHpT!K }eG+Q*gD.bWR$QP$F');
define('LOGGED_IN_KEY',    '!?L8G{D}DKmH]UlxY#)^ ~a[AaPa{Y/nqSk5P8m?/zOTf;aiIe[Y*zE6d R/d[96');
define('NONCE_KEY',        'ON75KT48P/h}%+#$J;/E)L/wFfigE!2yH$vU+0C|9aRp,@uor|M{K8e {;ZqM;7|');
define('AUTH_SALT',        'd67$7wn_#2w;oK*P#/E|J)u:i.| K+Xa&;FVpwTCMp3-[I-x^p9W$L;ccfJjHU>s');
define('SECURE_AUTH_SALT', 'y.YLE342S4]VTWK?nzpjZE>)t*,c,e8~<m0L.3u}qW(7t9H7S5ZK/sF&8PK!r%>4');
define('LOGGED_IN_SALT',   'a<xBW@wsy_,F<hG@|.f-E[Y0<=7NQs<?kY0EGu>W`.<hA7Y^F.@`9rwQ:,)a{$@)');
define('NONCE_SALT',       'Oi^^:`END*aK]2?:o!Y@rjk4$cC>b)vn~+2YQJ>r)>(qk{N5]E9F{]<jNnHEKaj]');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
