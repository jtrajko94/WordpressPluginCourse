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
define('DB_NAME', 'wpc');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

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
define('AUTH_KEY',         '{)/YQ_KIV@u^Jg]?HJ}kB:Ol}ZON~Z [;i{Rtvn:fJcQzoAwp*1P#J0$;|eC3+RT');
define('SECURE_AUTH_KEY',  '6/e Ia%Z;pU/UY/SuXE]`j,AYEbl;6-tcS [Qa&7e`BCHR`(P3$(_Q;T9%7!(f{L');
define('LOGGED_IN_KEY',    'h0W1?~f.LAx&#}-D8ar3[)[rk;IkcQ@Lzl%LW)@#3qm?z~{T3^Sg=,&$@6KH~x/I');
define('NONCE_KEY',        'EbNu-@p.]MnbWI4=S!DhI,^s@y?Y(uGcR@7e0Iu23h~Z21UTbWCAiIlt9&v6(*sx');
define('AUTH_SALT',        'Aiu;y)GfKMHKzWG8->PtQ=6p`G^ySz>K/jVTQa1)CAd=#5~xAz:PV4B&[l >f.~b');
define('SECURE_AUTH_SALT', ';E<(JtW0S>*L]#j1P`3D$6k^xML}G7I1w(D,7}FM#;/(%9;>q`x+=Kmn,Hu7lP?S');
define('LOGGED_IN_SALT',   '=.#`A_7,qbRAY?uAWmiWv6kD+mR,h%dJ.M>E<Yd4&k0CgvIGbg)uDaRDSN2ev0k`');
define('NONCE_SALT',       ':94t@HMl6o$A ~NY#XFNA{_1:p|)Q9OFs:RxJ,`b?.rs L$e4xQ;9&)y!|(;9R8*');
define( 'WP_DEBUG', true );
define( 'SAVEQUERIES', true );
define( 'WP_DEBUG_LOG', true );
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
