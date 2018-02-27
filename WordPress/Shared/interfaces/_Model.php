<?php
namespace WordPress\Shared;

/**
 * Defines the basic structure for a Model
 */
abstract class _Model
{
    
    /**
     * Retrieve a property
     *
     * @param string $key          The property key
     * @param mixed  $defaultValue The property's default value
     * @return mixed The property value
     */
    abstract public function get( string $key, $defaultValue = null );
}
