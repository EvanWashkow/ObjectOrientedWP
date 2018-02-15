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
            self::initializeCache();
            self::$cache->markIncomplete();
            $site = self::Get( $siteID );
        }
        
        return $site;
    }
    
    
    /**
     * Delete a site (permanent)
     *
     * Since WordPress does not allow you to delete the root site, neither do we.
     *
     * @param int $siteID The site (blog) ID to delete
     */
    final public static function Delete( int $siteID )
    {
        if ( is_multisite() && self::IsValidSiteID( $siteID ) && ( 1 !== $siteID )) {
            wpmu_delete_blog( $siteID, true );
        }
    }
    
    
    /**
     * Retrieve site(s)
     *
     * @param int $id The site ID to lookup
     * @return Sites\Models\Site|array
     */
    public static function Get( int $id = null )
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
    
    
    /**
     * Remove (or "Deactivate") site (not permanent)
     *
     * Since WordPress does not allow you to remove the root site, neither do we.
     *
     * @param int $siteID The site (blog) ID to remove
     */
    final public static function Remove( int $siteID )
    {
        if ( is_multisite() && self::IsValidSiteID( $siteID ) && ( 1 !== $siteID )) {
            wpmu_delete_blog( $siteID, false );
        }
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
        if ( is_multisite() && self::IsValidSiteID( $siteID )) {
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
    *                         SANITIZING / VALIDATION
    ***************************************************************************/
    
    /**
     * Is the given site ID valid?
     *
     * @param int $id The site (blog) ID to evaluate
     * @return bool
     */
    final public static function IsValidSiteID( int $id )
    {
        return ( 0 < $id && array_key_exists( $id, self::Get() ));
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
        // Exit. Invalid site id.
        $site = null;
        if ( !self::IsValidSiteID( $id )) {
            return $site;
        }
        
        // Retrieve site from site list array
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
