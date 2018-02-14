<?php
namespace WordPress;

/**
 * Defines a time zone, compatible with WordPress's numeric GMT format
 */
class TimeZone extends \DateTimeZone
{
    
    /***************************************************************************
    *                               CONSTANTS
    ***************************************************************************/
    
    /**
     * Identifies GMT types (+00:00)
     *
     * @var int
     */
    const GMT_TYPE = 1;
    
    /**
     * Identifies abbreviated types ('PST')
     *
     * @var int
     */
    const ABBREVIATION_TYPE = 2;
    
    /**
     * Identifies fully-specified time zones ('America/Los_Angeles')
     *
     * @var int
     */
    const IDENTIFICATION_TYPE = 3;
    
    
    /***************************************************************************
    *                           INSTANCE PROPERTIES
    ***************************************************************************/
    
    /**
     * Timezone data ( '+00:00' / 'PST' / 'America/Los_Angeles' )
     *
     * @var string
     */
    protected $timezone;
    
    /**
     * Timezone type (see constants)
     *
     * @var int
     */
    protected $timezone_type;
    
    /**
     * DateTime instance used to convert this time zone to other formats
     *
     * @var \DateTime
     */
    private $dateTime;
    
    
    /**
     * Create new Time Zone instance
     *
     * @param mixed $mixed Time zone specifier ( '+00:00' / 'PST' / 'America/Los_Angeles' / WordPress numeric format )
     */
    public function __construct( $mixed )
    {
        // Compensate for WordPress' decimal timezones (ex: 0, -1, 1.5)
        if ( is_numeric( $mixed )) {
            
            // Ensure string has a decimal place
            $floatString = strval( $mixed );
            if ( false === strpos( $floatString, '.' )) {
                $floatString .= ".0";
            }
            
            // Extract string elements
            $isSuccessful = preg_match(
                '/([-+]{0,1})(\d+)\.(\d+)/',
                $floatString,
                $elements
            );
            
            // Build GMT string
            if ( $isSuccessful ) {
                $operand = $elements[ 1 ];
                $operand = empty( $operand ) ? '+' : $operand;
                $hour    = $elements[ 2 ];
                $hour    = str_pad( $hour, 2, '0', STR_PAD_LEFT );
                $minutes = $elements[ 3 ];
                switch ( $minutes ) {
                    case 75:
                        $minutes = '45';
                        break;
                    case 5:
                        $minutes = '30';
                        break;
                    default:
                        $minutes = '00';
                        break;
                }
                $mixed = "{$operand}{$hour}:{$minutes}";
            }
        }
        
        // Create new TimeZone
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
    
    
    /***************************************************************************
    *                               CONVERSION
    ***************************************************************************/
    
    /**
     * Try to convert to a time zone identifier ('America/Los_Angeles')
     *
     * @param bool $fallback If unable to convert to this format, try the next in line
     * @return string|null
     */
    public function toIdentifier( bool $fallback = true )
    {
        $format = null;
        if ( self::IDENTIFICATION_TYPE == $this->timezone_type ) {
            $format = $this->format( 'e' );
        }
        elseif ( $fallback ) {
            $format = $this->toAbbreviation();
        }
        return $format;
    }
    
    
    /**
     * Try to convert to a time zone abbreviation ('PST')
     *
     * @param bool $fallback If unable to convert to this format, try the next in line
     * @return string|null
     */
    public function toAbbreviation( bool $fallback = true )
    {
        $format = null;
        if ( self::ABBREVIATION_TYPE <= $this->timezone_type ) {
            $format = $this->format( 'T' );
        }
        elseif ( $fallback ) {
            $format = $this->toGMT();
        }
        return $format;
    }
    
    
    /**
     * Convert to a time zone GMT offset ('+00:00')
     *
     * @return string
     */
    public function toGMT() {
        return $this->format( 'P' );
    }
    
    /**
     * Convert this time zone to the given format string
     *
     * @see http://php.net/manual/en/function.date.php
     *
     * @param string $format The PHP date formatting string
     * @return string
     */
    private function format( string $format )
    {
        if ( !isset( $this->dateTime )) {
            $this->dateTime = new \DateTime();
            $this->dateTime->setTimeZone( $this );
        }
        return $this->dateTime->format( $format );
    }
}
?>
