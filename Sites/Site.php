<?php
namespace WordPress\Sites;

class Site {
    
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
        return $this->getAttribute( 'blogname' );
    }
    
    // Get site description
    public function getDescription() {
        return $this->getAttribute( 'blogdescription' );
    }
    
    // Get timezone by the requested format
    public function getTimeZone() {
        $failure = NULL;
        
        // WordPress stores either the GMT or timezone string, but not both
        $_timezone_gmt    = $this->getAttribute( 'gmt_offset' );
        $_timezone_string = $this->getAttribute( 'timezone_string' );
        
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
        return $this->getAttribute( 'default_role' );
    }
    
    
    //
    // URLS
    
    // Constants
    const SITE_URL = 'siteurl';
    const HOME_URL = 'home';
    
    // Get site URL
    public function getURL() {
        return $this->getAttribute( self::SITE_URL );
    }
    
    // Get site URLs
    public function getURLs() {
        return [
            self::SITE_URL => $this->getURL(),
            self::HOME_URL => $this->getAttribute( self::HOME_URL )
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
        return $this->getAttribute( 'current_theme' );
    }
    
    
    //
    // HELPERS
    
    // Get site details
    private $_attributes = [];
    private function getAttribute( $key ) {
        $failure = NULL;
        
        // Get cached attribute by key
        if ( !empty( $this->_attributes[ $key ] )) {
            return $this->_attributes[ $key ];
        }
        
        // Lookup, cache, and return the attribute
        $attribute = get_option( $key, $failure );
        $this->_attributes[ $key ] = $attribute;
        return $attribute;
    }
    
    // Set site details
    private function setAttribute( $key, $value ) {
        if ( $isSuccessful = update_option( $key, $value )) {
            $_attributes[ $key ] = $value;
        }
    }
}
?>
