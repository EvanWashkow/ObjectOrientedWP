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
     * @param string $relativePath Path to plugin file, relative to the plugins directory
     * @param array  $pluginData The mapped array of plugin details
     * @return Models\Plugin
     */
    final public static function Create( string $relativePath, array $pluginData )
    {
        return new Models\Plugin( $relativePath, $pluginData );
    }
}
