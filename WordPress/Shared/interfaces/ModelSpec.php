<?php
namespace WordPress\Shared;

/**
 * Defines the basic structure for a Model
 */
interface ModelSpec extends Model\ReadOnlyModelSpec
{
    
    /**
     * Set a property
     *
     * @param string $key   The property key
     * @param mixed  $value The new value for the property
     * @return bool If the property was successfully set or not
     */
    public function set( string $key, $value );
}
