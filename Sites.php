<?php
namespace WordPress;

/*******************************************************************************
* WordPress Site (blog) manager
*******************************************************************************/

class Sites {
    
    // Get sites
    public static function Get() {
        self::loadComponents();
    }
    
    // Load components
    private static function loadComponents() {
        $directory = dirname( __FILE__ ) . '/Sites';
        require_once( "{$directory}/Site.php" );
    }
}
?>
