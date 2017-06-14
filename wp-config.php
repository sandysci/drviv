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
define('DB_NAME', 'drviv');

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
define('AUTH_KEY',         'W]cKuD$;vR}gWOY_q/ilCt.a!]ws_3io?-g}+rBM@#i]=<!fPPE#+^KCFyFK@A+ ');
define('SECURE_AUTH_KEY',  'T<r>J/,_9 [G>CS=vjG30y[C|kw1v@OE<we bP(1yL.,c{)qvt5#3qaISi2nll#b');
define('LOGGED_IN_KEY',    'v}@iVK?7INy/.Xl0~:LngFc~=o*vU#%i%_K%ls;>s(l*~*zUdrYi0Jy }#Rb2NiP');
define('NONCE_KEY',        ']o%Dzz)O#@3l&<Twars*x,Ju-ES$m3%p<wf7rRS3&^(/!Xaw>mJjPLSh?CPv++&1');
define('AUTH_SALT',        'thEvJg?lccRXZ[$X}(y4nk-~P^NhE,-:;BCNNa3o4mkoX=|PG&|IjBUY6;%iW410');
define('SECURE_AUTH_SALT', 'tZt4(m4Xf>>wj}<Fmct+h`:-}D!;d(Su^d{`G(`*!y(Fr] bU1lB^|_#hux]<~Oq');
define('LOGGED_IN_SALT',   'vz:FL!Ir)VSKl=E!z;X{eHtP_xwQ ,qH%wZ&)kK*m/GCm%)EtMP~2f*s3~)4}I9c');
define('NONCE_SALT',       '3x4kNX?p$Sr>gQ/[ekD!VO$#alxzx!,^-4ueb,{IjEy_>6_AoQe|L6Qmt(-~~AK6');

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
//define('WP_DEBUG', true);

// Turns WordPress debugging on
define('WP_DEBUG', true);

define( 'SAVEQUERIES', true );
 
// Tells WordPress to log everything to the /wp-content/debug.log file
define('WP_DEBUG_LOG', true);
 
// Doesn't force the PHP 'display_errors' variable to be on
define('WP_DEBUG_DISPLAY', true);
 
// Hides errors from being displayed on-screen
//@ini_set('display_errors', 0);



/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
