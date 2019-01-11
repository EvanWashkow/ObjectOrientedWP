<?php
namespace WordPress\Plugins\Models;

use PHP\Collections\Dictionary;
use PHP\Models\IReadOnlyModel;
use WordPress\Sites;


/**
 * Represents a single WordPress plugin
 */
class Plugin extends \PHP\PHPObject implements IReadOnlyModel
{
    
    /**
     * The unique identifier for this plugin
     *
     * @var string
     */
    private $id;
    
    /**
     * Mapped array of arbitrary properties
     *
     * @var Dictionary
     */
    private $properties;
    
    /**
     * Path to plugin file, relative to the plugins directory
     *
     * @var string
     */
    private $relativePath;
    
    
    /**
     * Create a new plugin instance
     *
     * @param string     $relativePath File path to the main plugin file, relative to the plugins directory
     * @param Dictionary $properties   Mapped array of this plugin's properties
     */
    final public function __construct( string $relativePath, Dictionary $properties )
    {
        $this->id           = \WordPress\Plugins::ExtractID( $relativePath );
        $this->relativePath = $relativePath;
        $this->properties   = $properties;
    }
    
    
    /**
     * Retrieve this plugin's ID
     *
     * @return string
     */
    final public function getID(): string
    {
        return $this->id;
    }
    
    
    /**
     * Retrieves this plugin's author name
     *
     * @return string
     */
    final public function getAuthorName(): string
    {
        return $this->get( 'Author' );
    }
    
    
    /**
     * Retrieves this plugin author's website
     *
     * @return string
     */
    final public function getAuthorURL(): string
    {
        return $this->get( 'AuthorURI' );
    }
    
    
    /**
     * Retrieves the description of this plugin's purpose
     *
     * @return string
     */
    final public function getDescription(): string
    {
        return $this->get( 'Description' );
    }
    
    
    /**
     * Retrieves the user-friendly name for this plugin's
     *
     * @return string
     */
    final public function getName(): string
    {
        return $this->get( 'Name' );
    }
    
    
    /**
     * Retrieves the path to this plugin's file, relative to the plugins directory
     *
     * @return string
     */
    final public function getRelativePath(): string
    {
        return $this->relativePath;
    }
    
    
    /**
     * Retrieves this plugin's version number
     *
     * @return string
     */
    final public function getVersion(): string
    {
        return $this->get( 'Version' );
    }
    
    
    /**
     * Indicates this plugin requires global activation on all sites
     *
     * @return bool
     */
    final public function requiresGlobalActivation(): bool
    {
        return $this->get( 'Network', false );
    }
    
    
    /***************************************************************************
    *                                 ACTIVATING
    ***************************************************************************/
    
    /**
     * Activate the plugin
     *
     * @param int $siteID The site ID or a \WordPress\Sites constant
     * @return bool True if the plugin is active
     */
    final public function activate( int $siteID = Sites::ALL ): bool
    {
        if ( $this->canActivate( $siteID )) {
            
            // Variables
            $siteID = Sites::SanitizeID( $siteID );
            
            // Activate globally, for all sites
            if ( Sites::ALL === $siteID ) {
                $result = activate_plugin( $this->getRelativePath(), null, true );
            }
            
            // Activate on the single site
            else {
                \WordPress\Sites::SwitchTo( $siteID );
                $result = activate_plugin( $this->getRelativePath() );
                \WordPress\Sites::SwitchBack();
            }
        }
        
        return $this->isActive( $siteID );
    }
    
    
    /**
     * Can the plugin be activated?
     *
     * @param int $siteID The site ID or a \WordPress\Sites constant
     * @return bool
     */
    final public function canActivate( int $siteID = Sites::ALL ): bool
    {
        // Variables
        $siteID = Sites::SanitizeID( $siteID );
        
        // Evaluate
        return (
            Sites::INVALID !== $siteID  &&
            !$this->isActive( $siteID ) &&
            (
                !is_multisite()                    ||
                !$this->requiresGlobalActivation() ||
                Sites::ALL === $siteID
            )
        );
    }
    
    
    /**
     * Deactivate the plugin
     *
     * @param int $siteID The site ID or a \WordPress\Sites constant
     * @return bool True if the plugin is no longer active
     */
    final public function deactivate( int $siteID = Sites::ALL ): bool
    {
        // Variables
        $siteID = Sites::SanitizeID( $siteID );
        
        // Deactivate globally, for all sites
        if ( Sites::ALL === $siteID ) {
            deactivate_plugins( $this->getRelativePath(), false, true );
        }
        
        // Deactivate for a single site
        elseif ( Sites::INVALID !== $siteID ) {
            Sites::SwitchTo( $siteID );
            deactivate_plugins( $this->getRelativePath(), false, false );
            Sites::SwitchBack();
        }
        
        return !$this->isActive( $siteID );
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
    final public function isActive( int $siteID = Sites::ALL ): bool
    {
        // Variables
        $isActive = false;
        $siteID   = Sites::SanitizeID( $siteID );
        
        // Determine if plugin is activate globally, for all sites
        if ( Sites::ALL === $siteID ) {
            $isActive = is_plugin_active_for_network( $this->getRelativePath() );
        }
        
        // Determine if plugin is activated for a single site
        elseif ( Sites::INVALID !== $siteID ) {
            
            // Include files
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            
            // Lookup active plugins for the site
            Sites::SwitchTo( $siteID );
            $isActive = is_plugin_active( $this->getRelativePath() );
            Sites::SwitchBack();
        }
        
        // Plugin not active
        return $isActive;
    }
    
    
    /***************************************************************************
    *                               UTILITIES
    ***************************************************************************/
    
    /**
     * Retrieve a property
     *
     * @param string $key          The property key
     * @param string $defaultValue The property's default value
     * @return mixed The property value
     */
    final public function get( string $key, string $defaultValue = '' )
    {
        $value = $defaultValue;
        if ( $this->properties->hasKey( $key )) {
            $value = $this->properties->get( $key );
        }
        return $value;
    }
}
