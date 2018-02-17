<?php
namespace WordPress;

/**
 * Manages WordPress plugins
 */
class Plugins
{
    
    /**
     * Retrieve all plugin(s)
     *
     * @return Plugins\Models\Plugin
     */
    public static function Get()
    {
        $plugins = [];
        foreach ( get_plugins() as $pluginFile => $pluginData ) {
            $plugin    = Plugins\Models::Create( $pluginFile, $pluginData );
            $plugins[] = $plugin;
        }
        return $plugins;
    }
}
