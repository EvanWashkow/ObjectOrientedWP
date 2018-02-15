<?php
namespace WordPress\Sites\Models;

/**
 * Defines a single site
 */
class Site extends _Site
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
    
    final public function getID()
    {
        return $this->id;
    }
    
    
    final public function getTitle()
    {
        return $this->get( self::TITLE_KEY, '' );
    }
    
    
    final public function setTitle( string $title )
    {
        $title = trim( $title );
        if ( !empty( $title )) {
            $this->set( self::TITLE_KEY, $title );
        }
    }
    
    
    final public function getDescription()
    {
        return $this->get( self::DESECRIPTION_KEY, '' );
    }
    
    
    final public function setDescription( string $description )
    {
        $description = trim( $description );
        $this->set( self::DESECRIPTION_KEY, $description );
    }
    
    
    /***************************************************************************
    *                                    URLS
    ***************************************************************************/
    
    final public function getURL()
    {
        return $this->get( self::SITE_URL_KEY, '' );
    }
    
    
    final public function getHomePageURL()
    {
        return $this->get( self::HOME_URL_KEY, '' );
    }
    
    
    final public function getProtocol()
    {
        preg_match( '/^(\S+):\/\//', $this->getURL(), $protocol );
        $protocol = $protocol[ 1 ];
        return $protocol;
    }
    
    
    /***************************************************************************
    *                           PLUGINS AND THEMES
    ***************************************************************************/
    
    final public function getActiveThemeID()
    {
        return $this->get( self::ACTIVE_THEME_ID_KEY, '' );
    }
    
    
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
    
    final public function getAdministratorEmail()
    {
        return $this->get( self::ADMINISTRATOR_EMAIL_KEY, '' );
    }
    
    
    final public function setAdministratorEmail( string $email )
    {
        $email = trim( $email );
        if ( is_email( $email )) {
            $this->set( self::ADMINISTRATOR_EMAIL_KEY, $email );
        }
    }
    
    
    final public function getDefaultUserRoleID()
    {
        return $this->get( self::DEFAULT_USER_ROLE_ID_KEY, '' );
    }
    
    
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
}
