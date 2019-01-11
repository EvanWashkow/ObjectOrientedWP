<?php
namespace WordPress;

use PHP\Collections\Dictionary;
use WordPress\Plugins\Models\Plugin;

/**
 * Manages WordPress plugins
 */
final class Plugins
{
    
    /**
     * Cache of all WordPress plugins
     *
     * @var \PHP\Cache
     */
    private static $cache;
    
    
    /**
     * Initializes the plugins manager (automatically-invoked on class load)
     */
    public static function Initialize()
    {
        if ( !isset( self::$cache )) {
            self::$cache = new \PHP\Cache( 'string', 'WordPress\Plugins\Models\Plugin' );
        }
    }
    
    
    /**
     * Retrieve a plugin's ID for its file path (relative to the plugins directory)
     *
     * @param string $relativePath Path to plugin file, relative to the plugins directory
     * @return string
     */
    public static function ExtractID( string $relativePath ): string
    {
        return explode( '/', $relativePath )[ 0 ];
    }
    
    
    /***************************************************************************
    *                                   MAIN
    ***************************************************************************/
    
    /**
     * Retrieve all plugin(s)
     *
     * @param mixed $mixed The plugin ID; array of plugin IDs; null to retrieve all
     * @return Dictionary|Plugin
     */
    public static function Get( $mixed = null )
    {
        // Route to the corresponding function
        if ( null === $mixed ) {
            return self::getAll();
        }
        elseif ( is_array( $mixed )) {
            return self::getMultiple( $mixed );
        }
        elseif ( is_string( $mixed )) {
            return self::getSingle( $mixed );
        }
        
        // Parameter was invalid. Returning null.
        return null;
    }
    
    
    /**
     * Determine whether or not the plugin ID is valid
     *
     * @param string $pluginID The plugin ID to check
     * @return bool
     */
    public static function IsValidID( string $pluginID ): bool
    {
        return self::getAll()->hasKey( $pluginID );
    }
    
    
    /***************************************************************************
    *                               SUB-ROUTINES
    ***************************************************************************/
    
    /**
     * Retrieve all plugin(s)
     *
     * @return Dictionary
     */
    private static function getAll(): Dictionary
    {
        // Build cache
        if ( !self::$cache->isComplete() ) {
            
            // Include WordPress plugins function
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            
            // For each WordPress plugin, create and cache our own object
            $plugins = get_plugins();
            foreach ( $plugins as $relativePath => $pluginData ) {
                $plugin = new Plugin(
                    $relativePath,
                    new Dictionary( 'string', '*', $pluginData )
                );
                self::$cache->set( $plugin->getID(), $plugin );
            }
            
            // Mark cache complete
            self::$cache->markComplete();
        }
        
        // Return plugins
        return clone self::$cache;
    }
    
    
    /**
     * Get multiple plugins by their IDs
     *
     * @param array $pluginIDs List of plugin ids to return
     * @return Dictionary Plugins indexed by their corresponding IDs
     */
    private static function getMultiple( array $pluginIDs ): Dictionary
    {
        // Variables
        $plugins  = self::getAll();
        $_plugins = new \PHP\Collections\Dictionary( 'string', Plugin::class );
        
        // For each specified plugin ID, add it to the plugins dictionary
        foreach ( $pluginIDs as $pluginID ) {
            if ( $plugins->hasKey( $pluginID )) {
                $_plugins->set( $pluginID, $plugins->get( $pluginID ));
            }
        }
        return $_plugins;
    }
    
    
    /**
     * Retrieve a single plugin by its ID
     *
     * @param string $pluginID The plugin ID
     * @return Plugin
     */
    private static function getSingle( string $pluginID ): Plugin
    {
        if ( !self::IsValidID( $pluginID )) {
            throw new \Exception( "Cannot retrieve invalid plugin ID: {$pluginID}" );
        }
        return self::getAll()->get( $pluginID );
    }
}
Plugins::Initialize();
