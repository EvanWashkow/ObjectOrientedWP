<?php
namespace WordPress;

/**
 * Manages WordPress sites
 */
class Sites
{
    
    /**
     * Cache of all sites
     *
     * @var \PHP\Cache
     */
    private static $cache;
    
    
    /***************************************************************************
    *                                   MAIN
    ***************************************************************************/
    
    /**
     * Add a new site. IMPORTANT: Call after `init`.
     *
     * @param string $url     The site URL
     * @param string $title   The site title
     * @param int    $adminID User ID for the site administrator
     * @return Sites\Models\Site|null Null on failure
     */
    public static function Add( string $url, string $title, int $adminID )
    {
        // Variables
        $site = null;
        
        // Exit. Multisite is not enabled.
        if ( !is_multisite() ) {
            return $site;
        }
        
        // Exit. Invalid URL.
        if ( !\PHP\URL::IsValid( $url )) {
            return $site;
        }
        
        // Extract url properties and create site
        \PHP\URL::Extract( $url, $protocol, $domain, $path );
        $siteID = wpmu_create_blog( $domain, $path, $title, $adminID );
        if ( !is_wp_error( $siteID )) {
            $site = self::Get( $siteID );
        }
        
        return $site;
    }
    
    
    /**
     * Retrieve site(s)
     *
     * @param int $id The site ID to lookup
     * @return Sites\Models\Site|array
     */
    public static function Get( int $id = NULL )
    {
        // Setup
        self::initializeCache();
        
        // Return site(s)
        if ( isset( $id )) {
            return self::getSingle( $id );
        }
        else {
            return self::getAll();
        }
    }
    
    
    /**
     * Retrieve the current site ID
     *
     * @return int
     */
    final public static function GetCurrentSiteID()
    {
        return get_current_blog_id();
    }
    
    
    /***************************************************************************
    *                              SITE SWITCHING
    ***************************************************************************/
    
    /**
     * Switch to different site context
     *
     * @param int $siteID Site (blog) ID to switch to
     */
    final public static function SwitchTo( int $siteID )
    {
        if ( is_multisite() ) {
            switch_to_blog( $siteID );
        }
    }
    
    
    /**
     * Switch back to the prior site context
     */
    final public static function SwitchBack()
    {
        if ( is_multisite() ) {
            restore_current_blog();
        }
    }
    
    
    /***************************************************************************
    *                               SUB-ROUTINES
    ***************************************************************************/
    
    
    /**
     * Retrieve single site
     *
     * @param int $id Site ID to lookup
     * @return Sites\Models\Site
     */
    private static function getSingle( int $id )
    {
        $site  = NULL;
        $sites = self::getAll();
        if ( array_key_exists( $id, $sites )) {
            $site = $sites[ $id ];
        }
        return $site;
    }
    
    
    /**
     * Retrieve all sites
     *
     * @return array
     */
    private static function getAll()
    {
        
        // Variables
        $sites = [];
        
        // Read all sites from cache.
        if ( self::$cache->isComplete() ) {
            $sites = self::$cache->get();
        }
        
        // Lookup sites
        else {
            
            // Retrieve sites from the multisite setup
            if ( is_multisite() ) {
                $wp_sites = get_sites();
                foreach ( $wp_sites as $wp_site ) {
                    $id = $wp_site->blog_id;
                    if ( !self::$cache->isSet( $id )) {
                        $site = Sites\Models::Create( $id );
                        self::$cache->add( $id, $site );
                    }
                }
            }
            
            // Retrieve site from default, non-multisite setup
            else {
                $site = Sites\Models::Create( 1 );
                self::$cache->add( 1, $site );
            }
            
            // Mark the cache complete
            self::$cache->markComplete();
        }
        
        // Read sites from cache
        $sites = self::$cache->get();
        return $sites;
    }
    
    
    /***************************************************************************
    *                               UTILITIES
    ***************************************************************************/
    
    /**
     * Create cache instance
     */
    protected static function initializeCache()
    {
        if ( !isset( self::$cache )) {
            self::$cache = new \PHP\Cache();
        }
    }
}
