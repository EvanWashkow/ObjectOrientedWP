<?php
namespace WordPress\Sites;

class Site {
    
    //
    // CONSTANTS
    const SITE_URL = 'siteurl';
    const HOME_URL = 'homeurl';
    
    
    //
    // SITE ATTRIBUTES
    
    // Site properties
    private $id;
    
    // Create new site object instance
    public function __construct( $id ) {
        $this->id = $id;
    }
    
    // Get site ID
    public function getID() {
        return $this->id;
    }
    
    // Get site title
    public function getTitle() {
        return $this->getAttribute( 'title' );
    }
    
    // Get site description
    public function getDescription() {
        return $this->getAttribute( 'description' );
    }
    
    // Get site URL
    public function getURL() {
        return $this->getAttribute( self::SITE_URL );
    }
    
    // Get site URLs
    public function getURLs() {
        return [
            self::SITE_URL => $this->getURL(),
            self::HOME_URL => get_home_url()
        ];
    }
    
    // Get site details
    private $_attributes;
    private function getAttribute( $key ) {
        $failure = NULL;
        
        // Build essential attribute cache
        if ( !isset( $this->_attributes )) {
            $this->_attributes = get_blog_details();
            $this->_attributes = (array) $this->_attributes;
        }
        
        // Get cached attribute by key
        if ( !empty( $this->_attributes[ $key ] )) {
            return $this->_attributes[ $key ];
        }
        
        // Attempt to lookup the attribute via `get_bloginfo()`
        $attribute = get_bloginfo( $key );
        if ( !empty( $attribute )) {
            $this->_attributes[ $key ] = $attribute;
            return $attribute;
        }
        
        return $failure;
    }
}
?>
