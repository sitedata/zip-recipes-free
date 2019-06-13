<?php
/*
Plugin Name: Zip Recipes
Text Domain: zip-recipes
Domain Path: /languages
Plugin URI: http://www.ziprecipes.net/
Plugin GitHub: https://github.com/hgezim/zip-recipes-plugin
Description: A plugin that adds all the necessary microdata to your recipes, so they will show up in Google's Recipe Search
Version: 6.0.0
Author: RogierLankhorst, markwolters
Author URI: http://www.really-simple-plugins.com/
License: GPLv3 or later

Copyright 2019 Rogier Lankhorst
This code is derived from the 2.6 version build of ZipList Recipe Plugin released by ZipList Inc.:
http://get.ziplist.com/partner-with-ziplist/wordpress-recipe-plugin/ and licensed under GPLv3 or later

*/

/*
    This file is part of Zip Recipes Plugin.

    Zip Recipes Plugin is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Zip Recipes Plugin is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Zip Recipes Plugin. If not, see <http://www.gnu.org/licenses/>.
*/

namespace ZRDN;
spl_autoload_register(__NAMESPACE__ . '\zrdn_autoload');

// Make sure we don't expose any info if called directly
defined('ABSPATH') or die("Error! Cannot be called directly.");

// Define constants
define('ZRDN_VERSION_NUM', '6.0.0');

define('ZRDN_FREE', true);
define('ZRDN_PLUGIN_DIRECTORY', plugin_dir_path( __FILE__ ));
define('ZRDN_PLUGIN_DIRECTORY_URL', plugin_dir_url( __FILE__ ));
define('ZRDN_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('ZRDN_PLUGIN_URL', sprintf('%s/%s/', plugins_url(), dirname(plugin_basename(__FILE__))));
define('ZRDN_API_URL', "https://api.ziprecipes.net");

//quick fix to disable registration requirement
update_option('zrdn_registered', true);


Util::log("Setting up init hooks.");

add_action('upgrader_process_complete', __NAMESPACE__ . '\ZipRecipes::plugin_updated', 10, 2);

// Leaving register_activation_hook here because it's using __FILE__ and it needs to use the main plugin file, which is
//  this file.
register_activation_hook(__FILE__, __NAMESPACE__ . '\ZipRecipes::init');

ZipRecipes::init();

function zrdn_autoload($className)
{
    global $wp_version;
    $path = __DIR__ . '/models/Recipe.php';

    require_once($path);
    require_once(__DIR__ . '/_inc/class.ziprecipes.util.php');
    require_once(ZRDN_PLUGIN_DIRECTORY . '_inc/class.ziprecipes.util.php');
    require_once(ZRDN_PLUGIN_DIRECTORY . 'class.ziprecipes.php');
    require_once(ZRDN_PLUGIN_DIRECTORY . '_inc/helper_functions.php');
    require_once(ZRDN_PLUGIN_DIRECTORY . '_inc/class.ziprecipes.shortcodes.php');
    require_once(ZRDN_PLUGIN_DIRECTORY . '_inc/PluginBase.php');

    require_once(ZRDN_PLUGIN_DIRECTORY . 'RecipeTable/RecipeMenu.php');
    if (is_admin()){
        require_once(ZRDN_PLUGIN_DIRECTORY . 'upgrade-zip.php');
        require_once(ZRDN_PLUGIN_DIRECTORY . 'class-field.php');
        //free
        require_once(ZRDN_PLUGIN_DIRECTORY . 'promo.php');

    }

    /**
     * API endpoint & Basic Auth
     */
    require_once(ZRDN_PLUGIN_DIRECTORY . "controllers/Response.php");
    require_once(ZRDN_PLUGIN_DIRECTORY . "controllers/AuthController.php");
    require_once(ZRDN_PLUGIN_DIRECTORY . "controllers/EndpointController.php");

    /**
     * Gutenberg
     */
    if (!function_exists('is_plugin_active'))
        require_once(ABSPATH . '/wp-admin/includes/plugin.php');
}

    /**
     * Load the translation files
     *
     *
     */
    add_action('init',  __NAMESPACE__ . '\zrdn_new_load_translation', 20);
    add_action('admin_init',  __NAMESPACE__ . '\zrdn_new_load_translation', 20);
    function zrdn_new_load_translation()
    {

        load_plugin_textdomain('zip-recipes', FALSE,  ZRDN_PLUGIN_DIRECTORY.'/languages/');
    }