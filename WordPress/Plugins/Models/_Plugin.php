<?php
namespace WordPress\Plugins\Models;

/**
 * Defines the structure for a single plugin
 */
abstract class _Plugin
{
    
    /***************************************************************************
    *                                  PROPERTIES
    ***************************************************************************/
    
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
    
    /**
     * Indicates this plugin requires global activation on all sites
     *
     * @return bool
     */
    abstract public function requiresGlobalActivation();
    
    
    /***************************************************************************
    *                                 ACTIVATING
    ***************************************************************************/
    
    /**
     * Activate the plugin
     *
     * @return bool Whether or not the plugin was successfully activated.
     */
    abstract public function activate();
    
    /**
     * Can the plugin be activated?
     *
     * @return bool
     */
    abstract public function canActivate();
    
    /**
     * Deactivate the plugin
     *
     * @return bool Whether or not the plugin was successfully deactivated
     */
    abstract public function deactivate();
    
    /**
     * Is the plugin activated?
     *
     * @return bool
     */
    abstract public function isActivated();
    
    
    /***************************************************************************
    *                                 UTILITIES
    ***************************************************************************/
    
    
    /**
     * Retrieve a plugin's ID for its file path (relative to the plugins directory)
     *
     * @param string $relativePath Path to plugin file, relative to the plugins directory
     * @return string
     */
    final protected static function extractID( string $relativePath )
    {
        return explode( '/', $relativePath )[ 0 ];
    }
}
