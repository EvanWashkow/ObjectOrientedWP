<?php
namespace WordPress;

use PHP\Collections\Dictionary\ReadOnlyDictionary;
use PHP\Collections\Dictionary\ReadOnlyDictionarySpec;

/**
 * Manages WordPress plugins
 */
class Plugins
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
            self::$cache = new \PHP\Cache( 'string', 'WordPress\Plugins\Models\PluginSpec' );
        }
    }
    
    
    /**
     * Retrieve a plugin's ID for its file path (relative to the plugins directory)
     *
     * @param string $relativePath Path to plugin file, relative to the plugins directory
     * @return string
     */
    final public static function ExtractID( string $relativePath ): string
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
     * @return Plugins\Models\Plugin|null|array
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
    
    
    /***************************************************************************
    *                               SUB-ROUTINES
    ***************************************************************************/
    
    /**
     * Retrieve all plugin(s)
     *
     * @return ReadOnlyDictionarySpec
     */
    private static function getAll(): ReadOnlyDictionarySpec
    {
        // Build cache
        if ( !self::$cache->isComplete() ) {
            
            // Include WordPress plugins function
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            
            // For each WordPress plugin, create and cache our own object
            $plugins = get_plugins();
            foreach ( $plugins as $pluginFile => $pluginData ) {
                $plugin = Plugins\Models::Create( $pluginFile, $pluginData );
                self::$cache->add( $plugin->getID(), $plugin );
            }
            
            // Mark cache complete
            self::$cache->markComplete();
        }
        
        // Return plugins
        return new ReadOnlyDictionary( self::$cache );
    }
    
    
    /**
     * Get multiple plugins by their IDs
     *
     * @param array $pluginIDs List of plugin ids to return
     * @return ReadOnlyDictionarySpec Plugins indexed by their corresponding IDs
     */
    private static function getMultiple( array $pluginIDs ): ReadOnlyDictionarySpec
    {
        // Variables
        $plugins  = self::getAll();
        $_plugins = new \PHP\Collections\Dictionary(
            'string', 'WordPress\Plugins\Models\PluginSpec'
        );
        
        // For each specified plugin ID, add it to the plugins dictionary
        foreach ( $pluginIDs as $pluginID ) {
            if ( $plugins->hasIndex( $pluginID )) {
                $_plugins->add( $pluginID, $plugins->get( $pluginID ));
            }
        }
        return new ReadOnlyDictionary( $_plugins );
    }
    
    
    /**
     * Retrieve a single plugin by its ID
     *
     * @param string $pluginID The plugin ID
     * @return Plugins\Models\Plugin|null
     */
    private static function getSingle( string $pluginID )
    {
        return self::getAll()->get( $pluginID, null );
    }
}
Plugins::Initialize();
