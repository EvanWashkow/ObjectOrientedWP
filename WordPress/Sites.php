<?php
namespace WordPress;

use PHP\Collections\ReadOnlyDictionary;
use PHP\Collections\ReadOnlyDictionarySpec;
use WordPress\Sites\Models\SiteSpec;

/**
 * Manages WordPress sites
 *
 * IMPORTANT: Some calls may bomb if the WordPress functions are not yet loaded.
 * If this happens, you will either want to 1) delay the call the routine, or
 * 2) load the needed WordPress files by hand.
 */
final class Sites
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
            self::$cache = new \PHP\Cache( 'integer', 'WordPress\Sites\Models\SiteSpec' );
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
     * @return SiteSpec
     */
    public static function Add( string $url, string $title, int $adminID ): SiteSpec
    {
        
        // Error. Too early in the execution stack.
        if ( !did_action( 'after_setup_theme' )) {
            throw new \Exception( 'Too early to create site. Try creating it after the \'after_setup_theme\' action.' );
        }
        
        // Error. Multisite is not enabled.
        elseif ( !is_multisite() ) {
            throw new \Exception( 'The site could not be created: a multisite install is required.' );
        }
        
        // Error. Invalid URL.
        elseif ( !\PHP\URL::IsValid( $url )) {
            throw new \Exception( 'The site URL is invalid' );
        }
        
        // Try to create site
        $url = new \PHP\URL( $url );
        $domain = $url->getDomain();
        $path   = $url->getPath();
        $siteID = wpmu_create_blog( $domain, $path, $title, $adminID );
        
        // Error. Could not create site
        if ( is_wp_error( $siteID )) {
            $wp_error = $siteID;
            $message  = $wp_error->get_error_message( $wp_error->get_error_code() );
            throw new \Exception( $message );
        }
        
        // Return the newly-created site
        else {
            self::$cache->markIncomplete();
            return self::Get( $siteID );
        }
    }
    
    
    /**
     * Delete a site (permanent)
     *
     * Since WordPress does not allow you to delete the root site, neither do we.
     *
     * @param int $siteID The site (blog) ID to delete
     * @return bool Whether or not the site was deleted
     */
    public static function Delete( int $siteID ): bool
    {
        // Variables
        $isDeleted = false;
        $siteID   = self::SanitizeID( $siteID );
        
        // Try to delete the site
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
            self::$cache->remove( $siteID );
            $isDeleted = true;
        }
        return $isDeleted;
    }
    
    
    /**
     * Retrieve site(s)
     *
     * @param int $siteID The site ID, ALL, or CURRENT
     * @return SiteSpec|ReadOnlyDictionarySpec
     */
    public static function Get( int $siteID = self::ALL )
    {
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
     * @return SiteSpec
     */
    public static function GetCurrent(): SiteSpec
    {
        return self::Get( self::GetCurrentID() );
    }
    
    
    /**
     * Retrieve the current site ID
     *
     * @return int
     */
    public static function GetCurrentID(): int
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
    public static function IsValidID( int $siteID ): bool
    {
        return self::INVALID !== self::SanitizeID( $siteID );
    }
    
    
    /**
     * Sanitize the site ID, always returning 1) the actual site ID, 2) ALL, or
     * 3) INVALID.
     *
     * ALL implicitly resolves to the current site ID if on a single-site
     * install. The choice to do this should be self-evident, but, if not, think
     * of it this way: the context for "all sites" is actually the current site
     * context, since there is no global context in which to execute. In fact,
     * attempting to execute multi-site procedures will always bomb if on a
     * single-site install. If a user wants to retrieve users, plugins, themes,
     * etc. for all sites, the context should always resolve to the current site
     * in order to correctly execute.
     *
     * Register all pseudo-IDs here
     *
     * @param int $siteID The site ID or pseudo-site ID
     * @return int The corresponding site ID; ALL, or INVALID
     */
    public static function SanitizeID( int $siteID ): int
    {
        // Resolve CURRENT pseudo identifier to the current site ID
        if ( self::CURRENT === $siteID ) {
            $siteID = self::GetCurrentID();
        }
        
        // Convert ALL to the current site ID if on a single-site install
        elseif ( self::ALL === $siteID ) {
            if ( !is_multisite() ) {
                $siteID = self::GetCurrentID();
            }
        }
        
        // Given an invalid site ID
        elseif (( $siteID < 0 ) || !self::getAll()->hasKey( $siteID )) {
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
    public static function SwitchTo( int $siteID )
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
    public static function SwitchBack()
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
     * @return SiteSpec
     */
    private static function getSingle( int $siteID ): SiteSpec
    {
        $siteID = self::SanitizeID( $siteID );
        if ( self::INVALID === $siteID ) {
            throw new \Exception( "Cannot retrieve site: the site ID does not exist" );
        }
        return self::getAll()->get( $siteID );
    }
    
    
    /**
     * Retrieve all sites
     *
     * @return ReadOnlyDictionarySpec
     */
    private static function getAll(): ReadOnlyDictionarySpec
    {
        
        // Lookup sites
        if ( !self::$cache->isComplete() ) {
            
            // Retrieve sites from the multisite setup
            if ( is_multisite() ) {
                $wp_sites = get_sites();
                foreach ( $wp_sites as $wp_site ) {
                    $siteID = ( int ) $wp_site->blog_id;
                    if ( !self::$cache->hasKey( $siteID )) {
                        $site = Sites\Models::Create( $siteID );
                        self::$cache->set( $siteID, $site );
                    }
                }
            }
            
            // Retrieve site from default, non-multisite setup
            else {
                $site = Sites\Models::Create( 1 );
                self::$cache->set( 1, $site );
            }
            
            // Mark the cache complete
            self::$cache->markComplete();
        }
        
        // Read sites from cache
        return new ReadOnlyDictionary( self::$cache );
    }
}
Sites::Initialize();
