<?php

// Load the autoloader locally
$autoloaderFile = __DIR__ . '/../vendor/autoload.php';
if ( file_exists( $autoloaderFile )) {
    require_once( $autoloaderFile );
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
