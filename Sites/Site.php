<?php
namespace WordPress\Sites;

class Site {
    
    // Site properties
    private $id;
    
    // Create new site object instance
    public function __construct( $id ) {
        $this->id = $id;
    }
    
    // Return site ID
    public function getID() {
        return $this->id;
    }
    
    // Return site description
    public function getDescription() {
        return $this->getAttribute( 'description' );
    }
    
    // Return site URL
    public function getURL() {
        return $this->getAttribute( 'siteurl' );
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
        
        // Return cached attribute by key
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
