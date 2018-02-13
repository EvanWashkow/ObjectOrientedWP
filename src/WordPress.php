<?php

require_once( __DIR__ . '/../vendor/autoload.php' );

/**
 * Root WordPress class
 */
class WordPress
{
    /**
     * Start the object-oriented WordPress controller
     */
    final public static function Initialize()
    {
        $directory = __DIR__ . '/WordPress';
        require_once( "{$directory}/Libraries.php" );
        require_once( "{$directory}/Sites.php" );
    }
}
WordPress::Initialize();
?>
