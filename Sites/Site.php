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
    
    // Constants
    const TIMEZONE_STRING = 'timezone-string';
    const TIMEZONE_GMT    = 'Timezone-gmt';
    
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
    public function getTimeZone( $format = self::TIMEZONE_GMT ) {
        
        // WordPress stores either the GMT or timezone string, but not both
        $timezone         = NULL;
        $_timezone_gmt    = $this->getAttribute( 'gmt_offset' );
        $_timezone_string = $this->getAttribute( 'timezone_string' );
        
        // No timezone set: default to GMT
        if ( empty( $_timezone_gmt ) && empty( $_timezone_string )) {
            $_timezone_gmt = '+0';
        }
        
        // Convert timezone string to GMT offset
        if ( empty( $_timezone_gmt )) {
            $now = new \DateTime();
            $now->setTimeZone( new \DateTimeZone( $_timezone_string ));
            $_timezone_gmt = $now->format( 'P' );
        }
        
        // Convert GMT to timezone string
        else if ( empty( $_timezone_string )) {
            $now = new \DateTime();
            $now->setTimeZone( new \DateTimeZone( $_timezone_gmt ));
            $_timezone_string = $now->format( 'e' );
        }
        
        // Format timezone
        switch ( $format ) {
            case self::TIMEZONE_STRING:
                $timezone = $_timezone_string;
                break;
            case self::TIMEZONE_GMT:
                $timezone = $_timezone_gmt;
                break;
        }
        return $timezone;
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
}
?>
