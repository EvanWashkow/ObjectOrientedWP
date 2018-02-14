<?php
namespace WordPress\Sites;

/**
 * Defines a single site
 */
class Site
{
    
    /***************************************************************************
    *                               DATABASE KEYS
    ***************************************************************************/
    
    /**
     * Option key for the site's admistor email address
     *
     * @var string
     */
    const ADMINISTRATOR_EMAIL_KEY = 'admin_email';
    
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
     * Option key for the current theme name
     *
     * @var string
     */
    const THEME_NAME_KEY = 'current_theme';
    
    /**
     * Option key for current theme ID
     *
     * @var string
     */
    const THEME_ID_KEY = 'template';
    
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
    public function __construct( int $id )
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
    public function getID()
    {
        return $this->id;
    }
    
    
    /**
     * Get the site title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->get( self::TITLE_KEY, '' );
    }
    
    
    /**
     * Set the site title
     *
     * @param string $title The new site title
     */
    public function setTitle( string $title )
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
    public function getDescription()
    {
        return $this->get( self::DESECRIPTION_KEY, '' );
    }
    
    
    /**
     * Set the site description
     *
     * @param string $description The new site description
     */
    public function setDescription( string $description )
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
    public function getURL()
    {
        return $this->get( self::SITE_URL_KEY, '' );
    }
    
    
    /**
     * Retrieve all URLs associated with this site
     *
     * @return array
     */
    public function getURLs()
    {
        return [
            self::HOME_URL_KEY => $this->get( self::HOME_URL_KEY, '' ),
            self::SITE_URL_KEY => $this->getURL()
        ];
    }
    
    
    /**
     * Returns "http" or "https" for the primary URL
     *
     * @param type var Description
     * @return return type
     */
    public function getProtocol()
    {
        preg_match( '/^(\S+):\/\//', $this->getURL(), $protocol );
        $protocol = $protocol[ 1 ];
        return $protocol;
    }
    
    
    /**
     * Is this site secured on SSL?
     *
     * @return bool
     */
    public function isSSL()
    {
        return is_ssl();
    }
    
    
    //
    // PLUGINS/THEMES
    
    // Get the current theme
    public function getTheme( $format = self::THEME_ID_KEY ) {
        $failure = NULL;
        if ( $format == self::THEME_ID_KEY || $format == self::THEME_NAME_KEY ) {
            return $this->get( $format );
        }
        else {
            return $failure;
        }
    }
    
    // Switch theme
    public function setTheme( $themeFolder ) {
        $themeFolder = trim( $themeFolder );
        if ( !empty( $themeFolder )) {
            switch_theme( $themeFolder );
        }
    }
    
    
    //
    // ADMINISTRATION
    
    // Get the administator email
    public function getAdministratorEmail() {
        return $this->get( self::ADMINISTRATOR_EMAIL_KEY );
    }
    
    // Set the administator email
    public function setAdministratorEmail( $email ) {
        $failure = NULL;
        $email = trim( $email );
        if ( is_email( $email )) {
            $this->set( self::ADMINISTRATOR_EMAIL_KEY, $email );
            return $email;
        }
        else {
            return $failure;
        }
    }
    
    // Get the default user role
    public function getDefaultRole() {
        return $this->get( 'default_role' );
    }
    
    // Get timezone by the requested format
    public function getTimeZone() {
        $failure = NULL;
        
        // WordPress stores either the GMT or timezone string, but not both
        $_timezone_gmt    = $this->get( 'gmt_offset' );
        $_timezone_string = $this->get( 'timezone_string' );
        
        // Create timezone
        $timezone = $failure;
        if ( !empty( $_timezone_string )) {
            $timezone = new \WordPress\TimeZone( $_timezone_string );
        }
        elseif ( !empty( $_timezone_gmt )) {
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
            switch_to_blog( $this->getID() );
            $value = get_option( $key, $defaultValue );
            restore_current_blog();
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
            switch_to_blog( $this->getID() );
            update_option( $key, $value );
            restore_current_blog();
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
?>
