<?php

// Load composer's autoloader if on a local build
if ( file_exists( __DIR__ . '/vendor/autoload.php' )) {
    require_once( __DIR__ . '/vendor/autoload.php' );
}

// Set up class autoloading (if included by hand)
new \PHP\ClassFramework\Autoloader( 'WordPress', __DIR__ . '/WordPress' );
new \PHP\ClassFramework\Autoloader( 'WordPress', __DIR__ . '/WordPress/libraries' );

/**
 * Root WordPress class
 */
class WordPress
{
    
    /**
     * Retrieve current WordPress version
     *
     * @return string
     */
    final public static function GetVersion()
    {
        return get_bloginfo( 'version' );
    }
}
