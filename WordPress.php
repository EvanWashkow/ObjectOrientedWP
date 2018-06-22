<?php

// Load composer's autoloader if on a local build
if ( file_exists( __DIR__ . '/vendor/autoload.php' )) {
    require_once( __DIR__ . '/vendor/autoload.php' );
}

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

$plugins = \WordPress\Plugins::Get();
// \PHP\Debug\Log::Write( $plugins );
