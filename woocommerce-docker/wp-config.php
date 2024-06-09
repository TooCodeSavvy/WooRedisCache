<?php
// wp-config.local.php

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'exampledb' );

/** Database username */
define( 'DB_USER', 'exampleuser' );

/** Database password */
define( 'DB_PASSWORD', 'examplepass' );

/** Database hostname */
define( 'DB_HOST', 'db:3306' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
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
define('AUTH_KEY',         '#bs#BKg/MHazIMCB+Va&zQ|+7}Ck1G+fRo$Li-iwXZ[77*8[VE<vE+{@L+2=zqxq');
define('SECURE_AUTH_KEY',  '*;vg;ZjU70z;]@mqtjG[J<R>@y&whH3EWr[3+ N<6USZVTzt?{>Xfq ){+cVRO`z');
define('LOGGED_IN_KEY',    '|bIN$%46-[{{>En))1Id`iMrK+z5s__c-x*DXQa}M:8D6-IUJ#h;XyvnC6?my>|!');
define('NONCE_KEY',        '+yWuuO3X^FB9vQTVgBf2@o ^/?nc*j =unoNpxY{a%3Y(F{-mck~aET{!{+{7^pp');
define('AUTH_SALT',        'H |.65N2[6H_-yAdv:TmO9.jhaK7|6/z|s3/k1Zwl{SU]!BT)L_,<5{BVB+o|Fdw');
define('SECURE_AUTH_SALT', 'xH|]>zbv{{RCyrq&]Vve|WMk|`y|PA<#%AQG(6;.x8FyLW]1B_hgBgbk+`_a#Q=.');
define('LOGGED_IN_SALT',   'b/6}~s5zq6Jub +{{NH?p]b1=Fc2Y|>q_3>wecO?-Uyskj2>,C;x_0(4qla+6Ykb');
define('NONCE_SALT',       'kvxsp7(W,fr+#G8-ZeaxV$q0rYKus ZhM((ouu4x?.D&<->*-z4()W=HLQZImRhM');
	

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
