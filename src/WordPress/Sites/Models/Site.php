<?php
namespace WordPress\Sites\Models;

use \WordPress\Sites;

/**
 * Defines a single site
 */
class Site
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
     * Get the unique identifier for this site
     *
     * @return int
     */
    final public function getID()
    {
        return $this->id;
    }
    
    
    /**
     * Get the site title
     *
     * @return string
     */
    final public function getTitle()
    {
        return $this->get( self::TITLE_KEY, '' );
    }
    
    
    /**
     * Set the site title
     *
     * @param string $title The new site title
     */
    final public function setTitle( string $title )
    {
        $title = trim( $title );
        if ( !empty( $title )) {
            $this->set( self::TITLE_KEY, $title );
        }
    }
    
    
    /**
     * Get the site description
     *
     * @return string
     */
    final public function getDescription()
    {
        return $this->get( self::DESECRIPTION_KEY, '' );
    }
    
    
    /**
     * Set the site description
     *
     * @param string $description The new site description
     */
    final public function setDescription( string $description )
    {
        $description = trim( $description );
        $this->set( self::DESECRIPTION_KEY, $description );
    }
    
    
    /***************************************************************************
    *                                    URLS
    ***************************************************************************/
    
    /**
     * Retrieve the primary site URL
     *
     * If you want the front-facing home URL, see getURLs()
     *
     * @return string
     */
    final public function getURL()
    {
        return $this->get( self::SITE_URL_KEY, '' );
    }
    
    
    /**
     * Retrieve all URLs associated with this site
     *
     * @return array
     */
    final public function getURLs()
    {
        return [
            self::HOME_URL_KEY => $this->get( self::HOME_URL_KEY, '' ),
            self::SITE_URL_KEY => $this->getURL()
        ];
    }
    
    
    /**
     * Returns "http" or "https" for the primary URL
     *
     * @return string
     */
    final public function getProtocol()
    {
        preg_match( '/^(\S+):\/\//', $this->getURL(), $protocol );
        $protocol = $protocol[ 1 ];
        return $protocol;
    }
    
    
    /***************************************************************************
    *                           PLUGINS AND THEMES
    ***************************************************************************/
    
    /**
     * Retrieve the currently-active theme ID
     *
     * Use \WordPress\Themes for related theme management
     *
     * @return string
     */
    final public function getActiveThemeID()
    {
        return $this->get( self::ACTIVE_THEME_ID_KEY, '' );
    }
    
    
    /**
     * Retrieve the active plugin IDs for this site (does not include network)
     *
     * Use \WordPress\Plugins for related plugin management
     *
     * @return array
     */
    final public function getActivePluginIDs()
    {
        $pluginIDs   = [];
        $pluginFiles = $this->get( self::ACTIVE_PLUGINS_KEY, [] );
        foreach ( $pluginFiles as $pluginFile ) {
            $elements = explode( '/', $pluginFile );
            $pluginIDs[] = $elements[ 0 ];
        }
        return $pluginIDs;
    }
    
    
    /***************************************************************************
    *                               ADMINISTRATION
    ***************************************************************************/
    
    /**
     * Retrieve the administator's email address
     *
     * @return string
     */
    final public function getAdministratorEmail()
    {
        return $this->get( self::ADMINISTRATOR_EMAIL_KEY, '' );
    }
    
    
    /**
     * Change the administator's email address
     *
     * @param string $email The new administrator email address
     */
    final public function setAdministratorEmail( string $email )
    {
        $email = trim( $email );
        if ( is_email( $email )) {
            $this->set( self::ADMINISTRATOR_EMAIL_KEY, $email );
        }
    }
    
    
    /**
     * Get the default user role identifier
     *
     * Use \WordPress\Users\Roles for related user role management
     *
     * @return string
     */
    final public function getDefaultUserRoleID()
    {
        return $this->get( self::DEFAULT_USER_ROLE_ID_KEY, '' );
    }
    
    
    /**
     * Get timezone for this site
     *
     * @return \WordPress\TimeZone
     */
    final public function getTimeZone()
    {
        // WordPress stores either the GMT or timezone string, but not both
        $_timezone_gmt    = $this->get( 'gmt_offset' );
        $_timezone_string = $this->get( 'timezone_string' );
        
        // Create timezone
        $timezone = NULL;
        if ( '' != $_timezone_string ) {
            $timezone = new \WordPress\TimeZone( $_timezone_string );
        }
        elseif ( '' != $_timezone_gmt ) {
            $timezone = new \WordPress\TimeZone( $_timezone_gmt );
        }
        
        return $timezone;
    }
    
    
    /***************************************************************************
    *                               UTILITIES
    ***************************************************************************/
    
    /**
     * Retrieve a property for this site
     *
     * @param string $key          The property key
     * @param mixed  $defaultValue The property's default value
     * @return mixed The property value
     */
    final public function get( string $key, $defaultValue = NULL )
    {
        // Variables
        $key   = self::sanitizeKey( $key );
        $value = $defaultValue;
        
        // Retrieve value
        if ( '' != $key ) {
            Sites::SwitchTo( $this->getID() );
            $value = get_option( $key, $defaultValue );
            Sites::SwitchBack();
        }
        return $value;
    }
    
    /**
     * Set a property on this site
     *
     * @param string $key   The property key
     * @param mixed  $value The new value for the property
     */
    final public function set( string $key, $value )
    {
        // Variables
        $key = self::sanitizeKey( $key );
        
        // Set value
        if ( '' != $key ) {
            Sites::SwitchTo( $this->getID() );
            update_option( $key, $value );
            Sites::SwitchBack();
        }
    }
    
    
    /**
     * Sanitize the site property key
     *
     * @param string $key The property key
     * @return string
     */
    final protected static function sanitizeKey( string $key )
    {
        return trim( $key );
    }
}
