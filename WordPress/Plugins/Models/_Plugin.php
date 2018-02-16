<?php
namespace WordPress\Plugins\Models;

/**
 * Defines the structure for a single plugin
 */
abstract class _Plugin
{
    
    /**
     * Retrieve this plugin's ID
     *
     * @param type var Description
     * @return return type
     */
    abstract public function getID();
}
