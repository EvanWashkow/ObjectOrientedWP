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
    
    /**
     * Retrieves this plugin's author name
     *
     * @return string
     */
    abstract public function getAuthorName();
    
    /**
     * Retrieves this plugin author's website
     *
     * @return string
     */
    abstract public function getAuthorURL();
    
    /**
     * Retrieves the description of this plugin's purpose
     *
     * @return string
     */
    abstract public function getDescription();
    
    /**
     * Retrieves the user-friendly name for this plugin's
     *
     * @return string
     */
    abstract public function getName();
    
    /**
    * Retrieves the path to this plugin's file, relative to the plugins directory
    *
    * @return string
    */
    abstract public function getRelativePath();
    
    /**
     * Retrieves this plugin's version number
     *
     * @return string
     */
    abstract public function getVersion();
}
