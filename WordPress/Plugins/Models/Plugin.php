<?php
namespace WordPress\Plugins\Models;

use WordPress\Sites;

/**
 * Represents a single WordPress plugin
 */
class Plugin extends _Plugin
{
    
    /**
     * This plugin's unique identifier
     *
     * @var string
     */
    private $id;
    
    /**
     * Name of the plugin's author
     *
     * @var string
     */
    private $authorName;
    
    /**
     * Link to the author's website
     *
     * @var string
     */
    private $authorURL;
    
    /**
     * Description of the plugin's purpose
     *
     * @var string
     */
    private $description;
    
    /**
     * User-friendly name for the plugin
     *
     * @var string
     */
    private $name;
    
    /**
    * Path to plugin file, relative to the plugins directory
    *
    * @var string
    */
    private $relativePath;
    
    /**
    * Indicates this plugin requires global activation on all sites
    *
    * @var string
    */
    private $requiresGlobalActivation;
    
    /**
     * Plugin version number
     *
     * @var string
     */
    private $version;
    
    
    /**
     * Instantiate a new Plugin instance
     *
     * @param string $relativePath Path to plugin file, relative to the plugins directory
     * @param bool   $requiresGlobalActivation Indicates this plugin requires global activation on all sites
     * @param string $version      Plugin version number
     * @param string $name         User-friendly name for the plugin
     * @param string $description  Description of the plugin's purpose
     * @param string $authorName   Name of the plugin's author
     * @param string $authorURL    Link to the author's website
     */
    final public function __construct( string $relativePath,
                                       bool   $requiresGlobalActivation,
                                       string $version,
                                       string $name,
                                       string $description,
                                       string $authorName,
                                       string $authorURL )
    {
        $this->id           = self::extractID( $relativePath );
        $this->relativePath = $relativePath;
        $this->requiresGlobalActivation = $requiresGlobalActivation;
        $this->version      = $version;
        $this->name         = $name;
        $this->description  = $description;
        $this->authorName   = $authorName;
        $this->authorURL    = $authorURL;
    }
    
    
    final public function getID()
    {
        return $this->id;
    }
    
    final public function getAuthorName()
    {
        return $this->authorName;
    }
    
    final public function getAuthorURL()
    {
        return $this->authorURL;
    }
    
    final public function getDescription()
    {
        return $this->description;
    }
    
    final public function getName()
    {
        return $this->name;
    }
    
    final public function getRelativePath()
    {
        return $this->relativePath;
    }
    
    final public function getVersion()
    {
        return $this->version;
    }
    
    final public function requiresGlobalActivation()
    {
        return $this->requiresGlobalActivation;
    }
    
    
    /***************************************************************************
    *                                 ACTIVATING
    ***************************************************************************/
    
    /**
     * Activate the plugin
     *
     * @param int $siteID The site ID or a \WordPress\Sites constant
     * @return bool Whether or not the plugin was successfully activated.
     */
    final public function activate( int $siteID = Sites::ALL )
    {
        // Variables
        $siteID   = Sites::SanitizeID( $siteID );
        $isActive = false;
        
        if ( $this->canActivate( $siteID )) {
            
            // Activate globally, for all sites
            if ( Sites::ALL === $siteID ) {
                $result   = activate_plugin( $this->getRelativePath(), null, true );
                $isActive = !is_wp_error( $result );
            }
            
            // Activate on the single site
            else {
                \WordPress\Sites::SwitchTo( $siteID );
                $result   = activate_plugin( $this->getRelativePath() );
                $isActive = !is_wp_error( $result );
                \WordPress\Sites::SwitchBack();
            }
        }
        return $isActive;
    }
    
    
    /**
     * Can the plugin be activated?
     *
     * @param int $siteID The site ID or a \WordPress\Sites constant
     * @return bool
     */
    final public function canActivate( int $siteID = Sites::ALL )
    {
        // Variables
        $siteID = Sites::SanitizeID( $siteID );
        
        // Evaluate
        return (
            Sites::INVALID !== $siteID     &&
            !$this->isActivated( $siteID ) &&
            (
                !is_multisite()                    ||
                !$this->requiresGlobalActivation() ||
                Sites::ALL === $siteID
            )
        );
    }
    
    
    /**
     * Is the plugin activated?
     *
     * When checking activated plugins for a single site, also check the
     * globally-activated plugins.
     *
     * @param int $siteID The site ID or a \WordPress\Sites constant
     * @return bool
     */
    final public function isActivated( int $siteID = Sites::ALL )
    {
        // Variables
        $isActivated = false;
        $pluginPaths = [];
        $siteID      = Sites::SanitizeID( $siteID );
        
        // Lookup globally-activated plugins
        if ( Sites::ALL === $siteID ) {
            $pluginPaths = get_site_option( 'active_sitewide_plugins', [] );
            $pluginPaths = array_keys( $pluginPaths );
        }
        
        // Lookup activated plugins for this site
        elseif ( Sites::INVALID !== $siteID ) {
            
            // EXIT if plugin is globally activated.
            if ( Sites::ALL === Sites::SanitizeID( Sites::ALL )) {
                $isActivated = $this->isActivated( Sites::ALL );
                if ( $isActivated ) {
                    return $isActivated;
                }
            }
            
            // Lookup active plugins for the site
            Sites::SwitchTo( $siteID );
            $pluginPaths = get_option( 'active_plugins', [] );
            Sites::SwitchBack();
        }
        
        // For each plugin path, convert to the plugin ID
        foreach ( $pluginPaths as $pluginPath ) {
            $pluginID = self::extractID( $pluginPath );
            if ( $this->getID() === $pluginID ) {
                $isActivated = true;
                break;
            }
        }
        
        // Plugin not active
        return $isActivated;
    }
}
