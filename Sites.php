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
        
        // Exit. Return all sites from cache.
        if ( self::isCompleteCache() ) {
            return self::getCache();
        }
        
        // Lookup sites. For each, create and cache new a site instance.
        $wp_sites = get_sites();
        foreach ( $wp_sites as $wp_site ) {
            self::loadComponents();
            $id   = $wp_site->blog_id;
            $site = self::getCache( $id );
            
            // Cache new site object
            if ( !isset( $site )) {
                $site = new Sites\Site( $id );
                self::addCache( $site );
            }
        }
        
        // Return sites. Mark cache complete.
        self::isCompleteCache( true );
        return self::getCache();
    }
    
    
    //
    // CACHE
    
    // Sites cache
    private static $isCompleteCache = false;
    private static $cache           = [ /* site_id => site_object*/ ];
    
    // Are all sites in the cache?
    private static function isCompleteCache( $bool = NULL ) {
        if ( isset( $bool ) && is_bool( $bool )) {
            self::$isCompleteCache = $bool;
        }
        return self::$isCompleteCache;
    }
    
    // Add site to cache
    private static function addCache( $site ) {
        self::$cache[ $site->getID() ] = $site;
    }
    
    // Get site or sites from cache
    private static function getCache( $siteID = NULL ) {
        if ( isset( $siteID )) {
            return empty( self::$cache[ $siteID ] ) ? NULL : self::$cache[ $siteID ];
        }
        else {
            return self::$cache;
        }
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
