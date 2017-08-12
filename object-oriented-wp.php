<?php
/*
Plugin Name: Object-Oriented WordPress
Description: Access WordPress in a modern, object-oriented way
Version:     0.0.1
Author:      Evan Washkow
Author URI:  https://github.com/EvanWashkow
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

namespace WordPress;

$directory = dirname( __FILE__ );

// Shared
require_once( "{$directory}/Libraries.php" );   // Lazy-loaded, non-static members

// Components
require_once( "{$directory}/Sites.php" );
?>
