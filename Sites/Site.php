<?php
namespace WordPress\Sites;

class Site {
    
    // Site option keys
    const TITLE    = 'blogname';
    const HOME_URL = 'home';
    const SITE_URL = 'siteurl';
    
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
        $return = false;
        $title  = trim( $title );
        if ( is_string( $title ) && !empty( $title )) {
            update_option( self::TITLE, $title );
            $return = $title;
        }
        return $return;
    }
    
    // Get site description
    public function getDescription() {
        return get_option( 'blogdescription' );
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
    
    // Get the default user role
    public function getDefaultRole() {
        return get_option( 'default_role' );
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
        $url      = $this->getURL();
        $protocol = substr( $url, 0, strpos( $url, '://' ));
        return $protocol;
    }
    
    
    //
    // PLUGINS/THEMES
    
    // Get the current theme
    public function getTheme() {
        return get_option( 'current_theme' );
    }
}
?>
