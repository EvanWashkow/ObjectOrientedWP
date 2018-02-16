<?php

// Exit. The class 'WordPress' already exists. This build may be corrupt.
if ( class_exists( 'WordPress' )) {
    return;
}

// Load composer's autoloader if on a local build
if ( file_exists( __DIR__ . '/vendor/autoload.php' )) {
    require_once( __DIR__ . '/vendor/autoload.php' );
}

// Set up class autoloading
new \PHP\ClassFramework\Autoloader( 'WordPress', __DIR__ . '/WordPress/libraries' );
new \PHP\ClassFramework\Autoloader( 'WordPress', __DIR__ . '/WordPress' );


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
