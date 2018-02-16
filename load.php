<?php
/**
 * Central "hub" for both local and exernal composer builds
 */

// This is being built locally, and has a local composer vendor library
if ( file_exists( __DIR__ . '/vendor/autoload.php' )) {
    require_once( __DIR__ . '/vendor/autoload.php' );
}

// Include main WordPress build
require_once( __DIR__ . '/WordPress.php' );
