<?php
namespace WordPress;

class TimeZone {
    
    // Timezone varients
    private $_timezone_string = NULL;    // America/Los_Angeles
    private $_timezone_gmt    = NULL;    // +00:00
    
    // Create new timezone instance
    public function __construct( $timezone ) {
        
        // Set timezone as GMT
        $gmt = self::tryParseGMT( $timezone );
        if ( isset( $gmt )) {
            $this->_timezone_gmt = $gmt;
        }
        
        // Set timezone as string
        else {
            $this->_timezone_string = $timezone;
        }
    }
    
    // Try to parse the given timezone string as '+00:00'
    private static function tryParseGMT( $timezone ) {
        $failure = NULL;
        
        // GMT regular expression parsing variables
        $gmt        = $failure; // Completed GMT in format +00:00
        $gmt_pieces = NULL;
        $_operand   = NULL;     // +/-
        $_hours     = NULL;     // 00
        $_fraction  = NULL;     // 00
        
        // GMT +0 and +00
        if ( preg_match( '/^([+-])(\d{1,2})$/', $timezone, $gmt_pieces )) {
            $_operand  = $gmt_pieces[ 1 ];
            $_hours    = $gmt_pieces[ 2 ];
            $_fraction = '00';
        }
        
        // GMT +0.0 and +00.0
        else if ( preg_match( '/^([+-])(\d{1,2})\.(\d{1})$/', $timezone, $gmt_pieces )) {
            $_operand  = $gmt_pieces[ 1 ];
            $_hours    = $gmt_pieces[ 2 ];
            $_fraction = $gmt_pieces[ 3 ];
        }
        
        // GMT +0000
        else if ( preg_match( '/^([+-])(\d{2})(\d{2})$/', $timezone, $gmt_pieces )) {
            $_operand  = $gmt_pieces[ 1 ];
            $_hours    = $gmt_pieces[ 2 ];
            $_fraction = $gmt_pieces[ 3 ];
        }
        
        // GMT +00:00
        else if ( preg_match( '/^([+-])(\d{2}):(\d{2})$/', $timezone, $gmt_pieces )) {
            $gmt = $timezone;
        }
        
        // Convert to +00:00
        if ( isset( $_operand ) && isset( $_hours ) && isset( $_fraction )) {
            $_hours    = str_pad( $_hours,    2, '0', STR_PAD_LEFT );
            $_fraction = str_pad( $_fraction, 2, '0', STR_PAD_RIGHT );
            $gmt = "{$_operand}{$_hours}:{$_fraction}";
        }
        
        return $gmt;
    }
}
?>
