<?php
namespace WordPress;

class TimeZone extends \DateTimeZone {
    
    //
    // CONSTANTS
    
    // Identify the type of timezone given
    const GMT_TYPE            = 1;  // '+00:00'
    const ABBREVIATION_TYPE   = 2;  // 'PST'
    const IDENTIFICATION_TYPE = 3;  // 'America/Los_Angeles'
    
    
    //
    // INSTANCE
    
    // Variables
    protected $timezone;
    protected $timezone_type;
    
    // Create new TimeZone
    public function __construct( $mixed ) {
        parent::__construct( $mixed );
        
        // PHP doesn't allow us to access parent members. Let's change that.
        $variables = print_r( $this, true );
        preg_match_all( '/\[(\S+)\] => (\S+)/', $variables, $variables );
        $_variable_values = array_pop( $variables );
        $_variable_names  = array_pop( $variables );
        
        // Match each variable name to its value
        $variables = [];
        foreach ( $_variable_names as $i => $name ) {
            $value = $_variable_values[ $i ];
            $this->$name = $value;
        }
    }
    
    // Convert to Abbreviation (PST)
    public function toAbbreviation() {
        return $this->format( 'T' );
    }
    
    public function toIdentifier() {
        return $this->format( 'e' );
    }
    
    // Convert to GMT (+00:00)
    public function toGMT() {
        return $this->format( 'P' );
    }
    
    // Convert to given format
    private $dateTime;
    private function format( $format ) {
        if ( !isset( $this->dateTime )) {
            $this->dateTime = new \DateTime();
            $this->dateTime->setTimeZone( $this );
        }
        return $this->dateTime->format( $format );
    }
}
?>
