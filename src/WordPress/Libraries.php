<?php
namespace WordPress;

// Shared, lazy-loaded, non-static members
class Libraries{
    
    public static function Load( $library ) {
        $directory = dirname( __FILE__ ) . '/Libraries';
        require_once( "{$directory}/{$library}.php" );
    }
}
?>
