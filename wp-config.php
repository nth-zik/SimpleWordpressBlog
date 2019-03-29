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
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'hunghung');

/** MySQL hostname */
define('DB_HOST', 'localhost:3306');

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
define('AUTH_KEY',         'ueMlnCq(V1R5@g7GTAl=Y=<F#D3wkbGgS#06-$[g#on&}|Q`3Et4?Wgor~BljKKl');
define('SECURE_AUTH_KEY',  '4Xn+Z -53A:gG{sr6j!-%~&%m39$oQl-sR)%/dF[;ezR9l#qJcQnC(/#:Il$LP~[');
define('LOGGED_IN_KEY',    '!B)I[<O;.Vlz9%Y5<%PT,.uBwgIvE~`^Nm?_wbZ`52N[Q`~dS:@Pf!O,8-cz%}2M');
define('NONCE_KEY',        'qMau2(-t{QkQVwDv,DR!py^0w:/?g/@!{L?g);t/:2bjtkK#_]%jt9^<E&SUMzc9');
define('AUTH_SALT',        'Q3%+X_<g{sS]/qiQH9Px`Ew[b9}j8((pM``7};}}X{v`u}N}Yp3P+}fqL!]DasQA');
define('SECURE_AUTH_SALT', '>DL({<~}Zw|Z<ib1~,VlQk(8rv:rP2y^O5R*xY`ij?MNP#+`<j&bY-7hxAlhyZ/<');
define('LOGGED_IN_SALT',   'B-d?BXF&FEx)N`RY.~mJ<9sOfxrz@N>$QI@.$grQ4G$cq2d5hlLk`uiE*U n9K~d');
define('NONCE_SALT',       '2<,p gdl+nY#:9P~(,L0ny.;9zuh]D6PlFnhEZb&|l!}1&.|]X8EgK8zy.T]1]fP');

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
