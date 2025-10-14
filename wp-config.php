<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'c*~n^NO|8kOr+MINJe#K4_G!Z17<]]POUnAky(wJ&&s1va<3#;:OIH%)>QV&r>5>' );
define( 'SECURE_AUTH_KEY',   'D$m1`X`L{0=!WLgi.cE&FAtAuVMX{~`uy?)dLAwWJ~2bu%Qh^Mq*J[f~Y,qZ#P_f' );
define( 'LOGGED_IN_KEY',     'qu9X@S3}RZ><O&9]^LcukNm]3*AC!=v}{+73w[*3@hHb2ZMH:La#xbySxAKK2} (' );
define( 'NONCE_KEY',         'lHzW,[8z1taHt(&C-_U{2$f$Oj2>-f-zjWKHvzyW|R]4/ryO{c<1t5XRm }/#1P(' );
define( 'AUTH_SALT',         '^ 1P!_C#P1`7KZQ1,tY68BF>^&/9d?(wG&c.H.WDSAWC&rlb*Np2%$kC6jjyZPsi' );
define( 'SECURE_AUTH_SALT',  'CHK<D?R,XODHc-q:z8Nc`dL1Kg+MFVl#;j~%FizV<NF#=ZU{M]8(^laO}W ;Lv.$' );
define( 'LOGGED_IN_SALT',    'DDPQouJiN?~ %RJ8kc$m!ZF^0>n?WJe4LYBs(aJtf6Qe<=;Yb=2uEE|Ty~(aERyI' );
define( 'NONCE_SALT',        'Wc,/gx!5cN-r=u-@y8V.D9]6{D0vdC;fL?8Kpr4cA= O[it4nL2 s.K|suYPLXNE' );
define( 'WP_CACHE_KEY_SALT', '/l#/A=8 %klA`2cy}B$4,7G[ol`MD8~c-+{##(TMGM{B4A!.(YBQ/sQ/Fv5gqT$7' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
