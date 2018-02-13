<?php
namespace WordPress\Sites;

/**
 * Defines a single site
 */
class Site
{

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
    
    
    // Site properties
    private $id;
    
    // Create new site object instance
    public function __construct( $id ) {
        $this->id = $id;
    }
    
    
    //
    // GENERAL INFORMATION
    
    // Get site ID
    public function getID() {
        return $this->id;
    }
    
    // Get site title
    public function getTitle() {
        return get_option( self::TITLE_KEY );
    }
    
    // Set site title
    public function setTitle( $title ) {
        $return = NULL;
        $title  = trim( $title );
        if ( is_string( $title ) && !empty( $title )) {
            update_option( self::TITLE_KEY, $title );
            $return = $title;
        }
        return $return;
    }
    
    // Get site description
    public function getDescription() {
        return get_option( self::DESECRIPTION_KEY );
    }
    
    // Set site description
    public function setDescription( $description ) {
        $return      = NULL;
        $description = trim( $description );
        if ( is_string( $description ) && !empty( $description )) {
            update_option( self::DESECRIPTION_KEY, $description );
            $return = $description;
        }
        return $return;
    }
    
    
    //
    // URLS
    
    // Get site URL
    public function getURL() {
        return get_option( self::SITE_URL_KEY );
    }
    
    // Get site URLs
    public function getURLs() {
        return [
            self::HOME_URL_KEY => get_option( self::HOME_URL_KEY ),
            self::SITE_URL_KEY => $this->getURL()
        ];
    }
    
    // Get site URL protocol
    public function getProtocol() {
        preg_match( '/^(\S+):\/\//', $this->getURL(), $protocol );
        $protocol = $protocol[ 1 ];
        return $protocol;
    }
    
    // Is this site on SSL?
    public function isSSL() {
        return is_ssl();
    }
    
    
    //
    // PLUGINS/THEMES
    
    // Get the current theme
    public function getTheme( $format = self::THEME_ID_KEY ) {
        $failure = NULL;
        if ( $format == self::THEME_ID_KEY || $format == self::THEME_NAME_KEY ) {
            return get_option( $format );
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
        return get_option( self::ADMINISTRATOR_EMAIL_KEY );
    }
    
    // Set the administator email
    public function setAdministratorEmail( $email ) {
        $failure = NULL;
        $email = trim( $email );
        if ( is_email( $email )) {
            update_option( self::ADMINISTRATOR_EMAIL_KEY, $email );
            return $email;
        }
        else {
            return $failure;
        }
    }
    
    // Get the default user role
    public function getDefaultRole() {
        return get_option( 'default_role' );
    }
    
    // Get timezone by the requested format
    public function getTimeZone() {
        $failure = NULL;
        
        // WordPress stores either the GMT or timezone string, but not both
        $_timezone_gmt    = get_option( 'gmt_offset' );
        $_timezone_string = get_option( 'timezone_string' );
        
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
}
?>
