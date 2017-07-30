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
}
?>
