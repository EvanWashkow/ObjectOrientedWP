<?php
namespace WordPress\Plugins\Models;

use WordPress\Shared\Model\IReadOnlyModel;
use WordPress\Sites;

/**
 * Defines the structure for a single plugin
 */
interface IPlugin extends IReadOnlyModel
{
    
    /**
     * Retrieve this plugin's ID
     *
     * @return string
     */
    public function getID(): string;
    
    /**
     * Retrieves this plugin's author name
     *
     * @return string
     */
    public function getAuthorName(): string;
    
    /**
     * Retrieves this plugin author's website
     *
     * @return string
     */
    public function getAuthorURL(): string;
    
    /**
     * Retrieves the description of this plugin's purpose
     *
     * @return string
     */
    public function getDescription(): string;
    
    /**
     * Retrieves the user-friendly name for this plugin's
     *
     * @return string
     */
    public function getName(): string;
    
    /**
    * Retrieves the path to this plugin's file, relative to the plugins directory
    *
    * @return string
    */
    public function getRelativePath(): string;
    
    /**
     * Retrieves this plugin's version number
     *
     * @return string
     */
    public function getVersion(): string;
    
    /**
     * Indicates this plugin requires global activation on all sites
     *
     * @return bool
     */
    public function requiresGlobalActivation(): bool;
    
    
    /***************************************************************************
    *                                 ACTIVATING
    ***************************************************************************/
    
    /**
     * Activate the plugin
     *
     * @param int $siteID The site ID or a \WordPress\Sites constant
     * @return bool True if the plugin is active
     */
    public function activate( int $siteID = Sites::ALL ): bool;
    
    /**
     * Can the plugin be activated?
     *
     * @param int $siteID The site ID or a \WordPress\Sites constant
     * @return bool
     */
    public function canActivate( int $siteID = Sites::ALL ): bool;
    
    /**
     * Deactivate the plugin
     *
     * @param int $siteID The site ID or a \WordPress\Sites constant
     * @return bool True if the plugin is no longer active
     */
    public function deactivate( int $siteID = Sites::ALL ): bool;
    
    /**
     * Is the plugin activated?
     *
     * When checking activated plugins for a single site, also check the
     * globally-activated plugins.
     *
     * @param int $siteID The site ID or a \WordPress\Sites constant
     * @return bool
     */
    public function isActive( int $siteID = Sites::ALL ): bool;
    
    
    /***************************************************************************
    *                               MODEL EXTENSIONS
    ***************************************************************************/
    
    /**
     * Retrieve a property
     *
     * @param string $key          The property key
     * @param string $defaultValue The property's default value
     * @return mixed The property value
     */
    public function get( string $key, string $defaultValue = '' );
}
