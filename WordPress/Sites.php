<?php
namespace WordPress;

/**
 * Manages WordPress sites
 *
 * IMPORTANT: Some calls may bomb if the WordPress functions are not yet loaded.
 * If this happens, you will either want to 1) delay the call the routine, or
 * 2) load the needed WordPress files by hand.
 */
class Sites
{
    
    /**
     * Pseudo-ID for all sites
     *
     * @var int
     */
    const ALL = -1;
    
    /**
     * Pseudo-ID for the current site
     *
     * @var int
     */
    const CURRENT = 0;
    
    /**
     * Pseudo-ID for an invalid site ID
     *
     * @var int
     */
    const INVALID = -2;
    
    
    /**
     * Cache of all sites
     *
     * @var \PHP\Cache
     */
    private static $cache;
    
    
    /**
     * Initializes the sites manager (automatically invoked on class load)
     */
    public static function Initialize()
    {
        if ( !isset( self::$cache )) {
            self::$cache = new \PHP\Cache();
        }
    }
    
    
    /***************************************************************************
    *                                   MAIN
    ***************************************************************************/
    
    /**
     * Add a new site
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
        $siteID = static::SanitizeID( $siteID );
        if (
            is_multisite()                &&
            ( self::INVALID !== $siteID ) &&
            ( self::ALL     !== $siteID ) &&
            ( 1             !== $siteID )
        ) {
            // Include WordPress multisite functions before attempting to
            // delete the site
            require_once(ABSPATH . 'wp-admin/includes/ms.php');
            
            // Delete the site
            wpmu_delete_blog( $siteID, true );
            self::$cache->delete( $siteID );
        }
    }
    
    
    /**
     * Retrieve site(s)
     *
     * @param int $siteID The site ID, ALL, or CURRENT
     * @return Sites\Models\Site|array|null
     */
    public static function Get( int $siteID = self::ALL )
    {
        // Exit. Invalid site ID.
        $siteID = static::SanitizeID( $siteID );
        if ( self::INVALID === $siteID ) {
            return null;
        }
        
        // Route to corresponding lookup method
        if ( self::ALL === $siteID ) {
            return self::getAll();
        }
        else {
            return self::getSingle( $siteID );
        }
    }
    
    
    /**
     * Retrieve the current site object
     *
     * @return Sites\Models\Site
     */
    final public static function GetCurrent()
    {
        return self::Get( self::GetCurrentID() );
    }
    
    
    /**
     * Retrieve the current site ID
     *
     * @return int
     */
    final public static function GetCurrentID()
    {
        return get_current_blog_id();
    }
    
    
    /***************************************************************************
    *                     SITE ID SANITIZATION / VALIDATION
    ***************************************************************************/
    
    /**
     * Is the given site / pseudo ID valid?
     *
     * @param int $siteID The site (blog) ID to evaluate
     * @return bool
     */
    final public static function IsValidID( int $siteID )
    {
        return self::INVALID !== static::SanitizeID( $siteID );
    }
    
    
    /**
     * Sanitize the site ID, resolving any pseudo-identifiers to their
     * corresponding site ID
     *
     * Register all pseudo-IDs here
     *
     * @param int $siteID The site ID or pseudo-site ID
     * @return int The corresponding site ID; ALL, or INVALID
     */
    public static function SanitizeID( int $siteID )
    {
        // Resolve CURRENT pseudo identifier to the current site ID
        if ( self::CURRENT === $siteID ) {
            $siteID = self::GetCurrentID();
        }
        
        // Given an invalid site ID
        elseif (
            ( self::ALL !== $siteID ) &&
            (
                ( $siteID < 0 ) ||
                !array_key_exists( $siteID, self::getAll() )
            )
        ) {
            $siteID = self::INVALID;
        }
        return $siteID;
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
        $siteID = self::SanitizeID( $siteID );
        if (
            is_multisite()                &&
            ( self::INVALID !== $siteID ) &&
            ( self::ALL     !== $siteID )
        ) {
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
     * @param int $siteID The site ID to lookup
     * @return Sites\Models\Site|null
     */
    private static function getSingle( int $siteID )
    {
        $sites = self::getAll();
        return $sites[ $siteID ];
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
                    $siteID = $wp_site->blog_id;
                    if ( !self::$cache->isSet( $siteID )) {
                        $site = Sites\Models::Create( $siteID );
                        self::$cache->add( $siteID, $site );
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
}
Sites::Initialize();
