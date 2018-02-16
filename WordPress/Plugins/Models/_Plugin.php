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
     * @return string
     */
    abstract public function getID();
}
