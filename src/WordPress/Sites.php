<?php
namespace WordPress;

/**
 * Manages WordPress sites
 */
class Sites
{
    
    /***************************************************************************
    *                                   MAIN
    ***************************************************************************/
    
    /**
     * Retrieve site(s)
     *
     * @param int $id Site ID to lookup
     * @return Sites\Site|array
     */
    public static function Get( int $id = NULL )
    {
        
        // Get site by ID
        if ( isset( $id ) && is_numeric( $id )) {
            $site = self::getCache( $id );
            if ( !isset( $site )) {
                $site = new Sites\Site( $id );
                self::addCache( $site );
            }
            return $site;
        }
        
        // Return all sites
        else {
            return self::getAll();
        }
    }
    
    
    /***************************************************************************
    *                               SUB-ROUTINES
    ***************************************************************************/
    
    /**
     * Retrieve all sites
     *
     * @return array
     */
    private static function getAll()
    {
        
        // Exit. Return all sites from cache.
        if ( self::isCompleteCache() ) {
            return self::getCache();
        }
        
        // Retrieve sites from the multisite setup
        if ( is_multisite() ) {
            $wp_sites = get_sites();
            foreach ( $wp_sites as $wp_site ) {
                $id   = $wp_site->blog_id;
                $site = self::getCache( $id );
                
                // Cache new site object
                if ( !isset( $site )) {
                    $site = new Sites\Site( $id );
                    self::addCache( $site );
                }
            }
        }
        
        // Retrieve site from default, non-multisite setup
        else {
            $site = new Sites\Site( 1 );
            self::addCache( $site );
        }
        
        // Return sites. Mark cache complete.
        self::isCompleteCache( true );
        return self::getCache();
    }
    
    
    //
    // CACHE
    
    // Sites cache
    private static $isCompleteCache = false;
    private static $_sites          = [ /* site_id => site_object*/ ];
    
    // Are all sites in the cache?
    private static function isCompleteCache( $bool = NULL ) {
        if ( isset( $bool ) && is_bool( $bool )) {
            self::$isCompleteCache = $bool;
        }
        return self::$isCompleteCache;
    }
    
    // Add site to cache
    private static function addCache( $site ) {
        self::$_sites[ $site->getID() ] = $site;
    }
    
    // Get site or sites from cache
    private static function getCache( $siteID = NULL ) {
        if ( isset( $siteID )) {
            return empty( self::$_sites[ $siteID ] ) ? NULL : self::$_sites[ $siteID ];
        }
        else {
            return self::$_sites;
        }
    }
}
?>
