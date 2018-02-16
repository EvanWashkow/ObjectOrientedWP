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
            $pieces   = explode( '/', $pluginFile );
            $pluginID = $pieces[ 0 ];
            $plugins[ $pluginID ] = Plugins\Models::Create( $pluginID );
        }
        return $plugins;
    }
}
