<?php
namespace WordPress\Sites;

class Site {
    
    // Constants
    const SITE_URL = 'siteurl';
    const HOME_URL = 'home';
    
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
    
    
    //
    // URLS
    
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
