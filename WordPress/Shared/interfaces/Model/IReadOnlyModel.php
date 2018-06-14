<?php
namespace WordPress\Shared\Model;

use PHP\IPHPObject;

/**
 * Defines the basic structure for a read-only Model
 */
interface IReadOnlyModel extends IPHPObject
{
    
    /**
     * Retrieve a property
     *
     * @param string $key The property key
     * @return mixed The property value
     */
    public function get( string $key );
}
