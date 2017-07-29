<?php
namespace WordPress;

/*******************************************************************************
* WordPress Site (blog) manager
*******************************************************************************/

class Sites {
    
    //
    // METHODS
    
    // Get sites, indexed by id
    public static function Get() {
        
        // Exit. Sites were cached from last lookup.
        $sites = self::getSitesCache();
        if ( isset( $sites )) {
            return $sites;
        }
        
        // Lookup sites. For each, create a new site object instance.
        $sites    = [];
        $wp_sites = get_sites();
        foreach ( $wp_sites as $wp_site ) {
            self::loadComponents();
            $id           = $wp_site->blog_id;
            $sites[ $id ] = new Sites\Site( $id );
        }
        
        // Cache and return sites
        self::setSitesCache( $sites );
        return $sites;
    }
    
    
    //
    // CACHE
    
    // Sites cache
    private static $sites;
    
    // Get sites cache
    private static function getSitesCache() {
        return self::$sites;
    }
    
    // Set sites cache
    private static function setSitesCache( $sites ) {
        self::$sites = $sites;
    }
    
    
    //
    // COMPONENTS
    
    // Load components
    private static function loadComponents() {
        $directory = dirname( __FILE__ ) . '/Sites';
        require_once( "{$directory}/Site.php" );
    }
}
?>
