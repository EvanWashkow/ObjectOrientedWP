<?php
namespace WordPress\Sites;

class Site {
    
    // Site option keys
    const DESECRIPTION = 'blogdescription';
    const TITLE        = 'blogname';
    const HOME_URL = 'home';
    const SITE_URL = 'siteurl';
    const ADMINISTRATOR_EMAIL = 'admin_email';
    
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
        return get_option( self::TITLE );
    }
    
    // Set site title
    public function setTitle( $title ) {
        $return = NULL;
        $title  = trim( $title );
        if ( is_string( $title ) && !empty( $title )) {
            update_option( self::TITLE, $title );
            $return = $title;
        }
        return $return;
    }
    
    // Get site description
    public function getDescription() {
        return get_option( self::DESECRIPTION );
    }
    
    // Set site description
    public function setDescription( $description ) {
        $return      = NULL;
        $description = trim( $description );
        if ( is_string( $description ) && !empty( $description )) {
            update_option( self::DESECRIPTION, $description );
            $return = $description;
        }
        return $return;
    }
    
    
    //
    // URLS
    
    // Get site URL
    public function getURL() {
        return get_option( self::SITE_URL );
    }
    
    // Get site URLs
    public function getURLs() {
        return [
            self::HOME_URL => get_option( self::HOME_URL ),
            self::SITE_URL => $this->getURL()
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
    public function getTheme() {
        return get_option( 'current_theme' );
    }
    
    
    //
    // ADMINISTRATION
    
    // Get the administator email
    public function getAdministratorEmail() {
        return get_option( self::ADMINISTRATOR_EMAIL );
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
        \WordPress\Libraries::Load( 'TimeZone' );
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
