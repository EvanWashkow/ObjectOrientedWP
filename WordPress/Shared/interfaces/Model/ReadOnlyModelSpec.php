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
     * @param string $key          The property key
     * @param mixed  $defaultValue The property's default value
     * @return mixed The property value
     */
    public function get( string $key, $defaultValue = null );
}
