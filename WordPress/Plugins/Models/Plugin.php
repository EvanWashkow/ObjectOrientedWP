<?php
namespace WordPress\Plugins\Models;

use PHP\Collections\IReadOnlyDictionary;
use WordPress\Sites;

/**
 * Represents a single WordPress plugin
 */
class Plugin extends \PHP\PHPObject implements IPlugin
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
     * @var IReadOnlyDictionary
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
     * @param string                 $relativePath File path to the main plugin file, relative to the plugins directory
     * @param IReadOnlyDictionary $properties   Mapped array of this plugin's properties
     */
    final public function __construct( string $relativePath, IReadOnlyDictionary $properties )
    {
        $this->id           = \WordPress\Plugins::ExtractID( $relativePath );
        $this->relativePath = $relativePath;
        $this->properties   = $properties;
    }
    
    final public function getID(): string
    {
        return $this->id;
    }
    
    final public function getAuthorName(): string
    {
        return $this->get( 'Author' );
    }
    
    final public function getAuthorURL(): string
    {
        return $this->get( 'AuthorURI' );
    }
    
    final public function getDescription(): string
    {
        return $this->get( 'Description' );
    }
    
    final public function getName(): string
    {
        return $this->get( 'Name' );
    }
    
    final public function getRelativePath(): string
    {
        return $this->relativePath;
    }
    
    final public function getVersion(): string
    {
        return $this->get( 'Version' );
    }
    
    final public function requiresGlobalActivation(): bool
    {
        return $this->get( 'Network', false );
    }
    
    
    /***************************************************************************
    *                                 ACTIVATING
    ***************************************************************************/
    
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
    
    final public function get( string $key, string $defaultValue = '' )
    {
        $value = $defaultValue;
        if ( $this->properties->hasKey( $key )) {
            $value = $this->properties->get( $key );
        }
        return $value;
    }
}
