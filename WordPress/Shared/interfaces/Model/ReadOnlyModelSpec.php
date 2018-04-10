<?php
namespace WordPress\Shared\Model;

use PHP\PHPObjectSpec;

/**
 * Defines the basic structure for a read-only Model
 */
interface ReadOnlyModelSpec extends PHPObjectSpec
{
    
    /**
     * Retrieve a property
     *
     * @param string $key The property key
     * @return mixed The property value
     */
    public function get( string $key );
}
