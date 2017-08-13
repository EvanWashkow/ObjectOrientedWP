<?php
namespace WordPress;

class TimeZone extends \DateTimeZone {
    
    //
    // CONSTANTS
    
    // Identify the type of timezone given
    const GMT_TYPE            = 1;  // '+00:00'
    const ABBREVIATION_TYPE   = 2;  // 'PST'
    const IDENTIFICATION_TYPE = 3;  // 'America/Los_Angeles'
}
?>
