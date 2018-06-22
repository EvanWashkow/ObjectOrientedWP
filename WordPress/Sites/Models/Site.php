<?php
namespace WordPress\Sites\Models;

use WordPress\Sites;
use PHP\Models\IModel;

/**
 * Defines a single site
 */
class Site extends \PHP\PHPObject implements IModel
{
    
    /***************************************************************************
    *                               DATABASE KEYS
    ***************************************************************************/
    
    /**
     * Option key for currently-active theme ID
     *
     * @var string
     */
    const ACTIVE_THEME_ID_KEY = 'template';
    
    /**
     * Option key for the site's active plugins
     *
     * @var string
     */
    const ACTIVE_PLUGINS_KEY = 'active_plugins';
    
    /**
     * Option key for the site's administrator email address
     *
     * @var string
     */
    const ADMINISTRATOR_EMAIL_KEY = 'admin_email';
    
    /**
     * Option key for the site's default user role
     *
     * @var string
     */
    const DEFAULT_USER_ROLE_ID_KEY = 'default_role';
    
    /**
     * Option key for the site description
     *
     * @var string
     */
    const DESECRIPTION_KEY = 'blogdescription';
    
    /**
     * Option key for setting the GMT time
     *
     * @var string
     */
    const GMT_KEY = 'gmt_offset';
    
    /**
     * Option key for the site's home (front-facing) url
     *
     * @var string
     */
    const HOME_URL_KEY = 'home';
    
    /**
     * Option key for the site (backend) url
     *
     * @var string
     */
    const SITE_URL_KEY = 'siteurl';
    
    /**
     * Option key for the time zone identifier ('America/Los_Angeles')
     *
     * @var string
     */
    const TIME_ZONE_KEY = 'timezone_string';
    
    /**
     * Option key for the site title
     *
     * @var string
     */
    const TITLE_KEY = 'blogname';
    
    
    /***************************************************************************
    *                              SITE PROPERTIES
    ***************************************************************************/
    
    /**
     * The site (blog) ID: uniquely identifies this site.
     *
     * @var int
     */
    private $id;
    
    /**
     * Create new site instance
     *
     * @param int $id The site (blog) id
     */
    final public function __construct( int $id )
    {
        $this->id = $id;
    }
    
    
    /***************************************************************************
    *                             GENERAL INFORMATION
    ***************************************************************************/
    
    /**
     * Get the unique identifier for this site (blog)
     *
     * @return int
     */
    final public function getID(): int
    {
        return $this->id;
    }
    
    
    /**
     * Get the site title
     *
     * @return string
     */
    final public function getTitle(): string
    {
        return $this->get( self::TITLE_KEY, '' );
    }
    
    
    /**
     * Set the site title
     *
     * @param string $title The new site title
     * @return bool Whether or not the title change was successful
     */
    final public function setTitle( string $title ): bool
    {
        $isSuccessful = false;
        $title        = trim( $title );
        if ( '' !== $title ) {
            $isSuccessful = $this->set( self::TITLE_KEY, $title );
        }
        return $isSuccessful;
    }
    
    
    /**
     * Get the site description
     *
     * @return string
     */
    final public function getDescription(): string
    {
        return $this->get( self::DESECRIPTION_KEY, '' );
    }
    
    
    /**
     * Set the site description
     *
     * @param string $description The new site description
     * @return bool Whether or not the description change was successful
     */
    final public function setDescription( string $description ): bool
    {
        $description = trim( $description );
        return $this->set( self::DESECRIPTION_KEY, $description );
    }
    
    
    /***************************************************************************
    *                                    URLS
    ***************************************************************************/
    
    /**
     * Retrieve the primary site URL
     *
     * If you want to retrieve the front-facing home URL, see getHomePageURL()
     *
     * @return string
     */
    final public function getURL(): string
    {
        return $this->get( self::SITE_URL_KEY, '' );
    }
    
    
    /**
     * Set the primary site URL
     *
     * If you want to set the front-facing home URL, see setHomePageURL()
     *
     * @param string $url The new URL
     * @return bool Whether or not the URL change was successful
     */
    final public function setURL( string $url ): bool
    {
        // Variables
        $isSuccessful = true;
        $url          = \PHP\URL::Sanitize( $url );
        
        // Invalid URL.
        if ( '' == $url ) {
            $isSuccessful = false;
        }
        
        // Multi-site only: update blog table info
        elseif ( is_multisite() ) {
            
            // Primary URL for network cannot be changed.
            if ( 1 === $this->getID() ) {
                $isSuccessful = false;
            }
            
            // Change blog table url
            else {
                global $wpdb;
                $urlObject = new \PHP\URL( $url );
                $domain    = $urlObject->getDomain();
                $path      = $urlObject->getPath();
                $isSuccessful = false !== $wpdb->update(
                    $wpdb->blogs,
                    [
                        'domain' => $domain,
                        'path'   => $path
                    ],
                    [
                        'blog_id' => $this->getID()
                    ]
                );
            }
        }
        
        // Single-/Multi-site
        if ( $isSuccessful ) {
            $isSuccessful = $this->set( self::SITE_URL_KEY, $url );
        }
        
        return $isSuccessful;
    }
    
    
    /**
     * Retrieve the home page URL for this site
     *
     * @return string
     */
    final public function getHomePageURL(): string
    {
        return $this->get( self::HOME_URL_KEY, '' );
    }
    
    
    /**
     * Set the home page URL for this site
     *
     * @param string $url The new URL
     * @return bool Whether or not the URL change was successful
     */
    final public function setHomePageURL( string $url ): bool
    {
        $url = \PHP\URL::Sanitize( $url );
        $isSuccessful = false;
        if ( '' != $url ) {
            $isSuccessful = $this->set( self::HOME_URL_KEY, $url );
        }
        return $isSuccessful;
    }
    
    
    /***************************************************************************
    *                               ADMINISTRATION
    ***************************************************************************/
    
    /**
     * Retrieve the administator's email address
     *
     * @return string
     */
    final public function getAdministratorEmail(): string
    {
        return $this->get( self::ADMINISTRATOR_EMAIL_KEY, '' );
    }
    
    
    /**
     * Change the administator's email address
     *
     * @param string $email The new administrator email address
     * @return bool Whether or not the change was successful
     */
    final public function setAdministratorEmail( string $email ): bool
    {
        $email = trim( $email );
        $isSuccessful = false;
        if ( is_email( $email )) {
            $isSuccessful = $this->set( self::ADMINISTRATOR_EMAIL_KEY, $email );
        }
        return $isSuccessful;
    }
    
    
    /**
     * Get time zone for this site
     *
     * @return \WordPress\Sites\TimeZone
     */
    final public function getTimeZone(): \WordPress\Sites\TimeZone
    {
        // WordPress stores either the GMT or timezone string, but not both
        $_timezone_gmt    = $this->get( self::GMT_KEY );
        $_timezone_string = $this->get( self::TIME_ZONE_KEY );
        
        // Create timezone
        $timezone = NULL;
        if ( '' != $_timezone_string ) {
            $timezone = new \WordPress\Sites\TimeZone( $_timezone_string );
        }
        elseif ( '' != $_timezone_gmt ) {
            $timezone = new \WordPress\Sites\TimeZone( $_timezone_gmt );
        }
        
        return $timezone;
    }
    
    
    /**
     * Set time zone for this site
     *
     * @param \WordPress\Sites\TimeZone $timeZone
     * @return bool Whether or not the TimeZone change was successful
     */
    final public function setTimeZone( \WordPress\Sites\TimeZone $timeZone ): bool
    {
        // Variables
        $id           = $timeZone->toID( false );
        $isSuccessful = false;
        
        // Set floating-point GMT offset
        if ( '' === $id ) {
            $isSuccessful = $this->set( self::GMT_KEY, $timeZone->toFloat() );
            $isSuccessful = $isSuccessful && $this->set( self::TIME_ZONE_KEY, '' );
        }
        
        // Set timezone identifier string ('America/Los_Angeles')
        else {
            $isSuccessful = $this->set( self::GMT_KEY, '' );
            $isSuccessful = $isSuccessful && $this->set( self::TIME_ZONE_KEY, $id );
        }
        
        return $isSuccessful;
    }
    
    
    /***************************************************************************
    *                               UTILITIES
    ***************************************************************************/
    
    final public function get( string $key, $defaultValue = NULL )
    {
        // Variables
        $value = $defaultValue;
        
        // Retrieve value
        if ( '' != $key ) {
            Sites::SwitchTo( $this->getID() );
            $value = get_option( $key, $defaultValue );
            Sites::SwitchBack();
        }
        return $value;
    }
    
    
    final public function set( string $key, $value )
    {
        // Variables
        $isSuccessful = false;
        
        // Set value
        if ( '' != $key ) {
            Sites::SwitchTo( $this->getID() );
            update_option( $key, $value );
            $isSuccessful = true;
            Sites::SwitchBack();
        }
        
        return $isSuccessful;
    }
}
