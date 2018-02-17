<?php
namespace WordPress\Plugins;

/**
 * Creates new plugin models using the factory method
 */
class Models
{
    
    /**
     * Create a new plugin model instance
     *
     * @param string $pluginFile The relative file path (from the template directory) to the plugin file
     * @param array  $pluginData The mapped array of plugin details
     * @return Models\Plugin
     */
    final public static function Create( string $pluginFile, array $pluginData )
    {
        $pieces = explode( '/', $pluginFile );
        $id     = $pieces[ 0 ];
        
        return new Models\Plugin( $id );
    }
}
