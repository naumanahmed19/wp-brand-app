<?php
/**
 * Plugin Name:       WP Brand Blocks
 * Description:       Buid your own woocommerce store app from wordPress 
 * Requires at least: 5.9
 * Requires PHP:      7.0
 * Version:           1.0.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mdc-tabs
 *
 * @package           create-block
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 * 
 */



if ( ! defined( 'ABSPATH' )) exit; // Exit if accessed directly

define( 'BRAND_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'BRAND_ASSETS_URL', plugins_url( '/assets', __FILE__ ) );
define( 'MY_PLUGIN_PATH', plugin_dir_url( __FILE__ ) );
define( 'BRAND_APP_NAME', 'Brand App' );


/**
 * List All the blocks supported by app
 */
define('BRAND_ALLOWD_BLOCK' , [
	'ibenic/inner-blocks',
	'xapp/container',
	'xapp/text',
	'brand/appbar',
	'brand/button',
	'brand/logo',
	'brand/searchbar',
	'brand/slider',
	'brand/splash',
	'brand/intro',
	'brand/screens',
	'brand/tabs',
	'brand/tab',
	'brand/categories',
	'brand/expcategories',
	'brand/banner-categories',
	'brand/banner-products',
	'brand/products',
	'brand/tile',
	'brand/divider',
	'brand/cart',
	'brand/button-cart',
	'brand/favourites',
 ]);


final class Brand_App {

	/**
	 * Plugin Version
	 *
	 * @since 1.2.0
	 * @var string The plugin version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.2.0
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '7.0';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		// Load translation
		add_action( 'init', array( $this, 'i18n' ) );

		// Init Plugin
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 * Fired by `init` action hook.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function i18n() {
		load_plugin_textdomain( 'brand' );
	}

	/**
	 * Initialize the plugin
	 *
	 * Validates that Elementor is already loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed include the plugin class.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function init() {


		/**
		 * 
		 * Register app blocks
		 */

		function brand_app_block_init() {
			register_block_type( __DIR__ . '/build/container' );
			register_block_type( __DIR__ . '/build/text' );
			register_block_type( __DIR__ . '/build/appbar' );
			register_block_type( __DIR__ . '/build/logo' );
			register_block_type( __DIR__ . '/build/button' );
			register_block_type( __DIR__ . '/build/searchbar' );
			register_block_type( __DIR__ . '/build/slider');
			register_block_type( __DIR__ . '/build/splash' );
			register_block_type( __DIR__ . '/build/screens' );
			register_block_type( __DIR__ . '/build/tabs' );
			register_block_type( __DIR__ . '/build/tab' );
			register_block_type( __DIR__ . '/build/categories');
			register_block_type( __DIR__ . '/build/expcategories');
			register_block_type( __DIR__ . '/build/banner-categories');
			register_block_type( __DIR__ . '/build/products');
			register_block_type( __DIR__ . '/build/intro');
			register_block_type( __DIR__ . '/build/tile');
			register_block_type( __DIR__ . '/build/divider');
			register_block_type( __DIR__ . '/build/cart');
			register_block_type( __DIR__ . '/build/button-cart');
			register_block_type( __DIR__ . '/build/favourites');
		}
		add_action( 'init', 'brand_app_block_init' );
		
		/**
		 * 
		 * Allow only follwoing blocks
		 * 
		 */
		function brand_allowed_block_types_when_post_provided( $allowed_block_types, $editor_context ) {
			global $blocks;
		
			if ( ! empty( $editor_context->post )  && XAPP_POST_TYPE == get_post_type()) {
				return  BRAND_ALLOWD_BLOCK ;
			}
			return $allowed_block_types;
		}
		 
		//add_filter( 'allowed_block_types_all', 'brand_allowed_block_types_when_post_provided', 10, 2 );

		/**
		 * 
		 * Register API
		 */
		require_once( __DIR__ . '/api/api.php' );
		require_once( __DIR__ . '/api/woofee.php' );
		

	
		function brand_wp_admin_scripts( $hook ) {
		
			// wp_enqueue_script( 'slick',
			//     plugin_dir_url( __FILE__ ) . 'assets/js/slick.min.js',
			//  	array('jquery'), '1.4.0', 
			// 	false 
			// );
			wp_enqueue_script( 'brand-main',
				plugin_dir_url( __FILE__ ) . 'assets/js/main.js', 
				array('jquery'), '1.0.0', 
				false 
			);
			

			//if(get_the_title() === BRAND_APP_NAME)

				global $post;
			//{
				$url  = plugins_url( '/web', __FILE__ ) . '/#/?home='.get_site_url().'&postId='.$post->ID; 
				wp_localize_script(
					'brand-main',
					'x_app',
					[
						'previewUrl' => $url 
					]
				);
			//}

		
			
		}
		add_action( 'admin_enqueue_scripts', 'brand_wp_admin_scripts' );


		
		

	}


}
new Brand_App();


add_filter( 'jwt_auth_whitelist', function ( $endpoints ) {
	$routes = array(
		'/wp-json/brand/*',
  
	);
	return array_unique( array_merge( $endpoints, $routes ) );
  } );
 


/**
 * Register block category
 */
  add_filter( 'block_categories_all' , function( $categories ) {

    // Adding a new category.
	$categories[] = array(
		'slug'  => 'brand',
		'title' => 'Brand App'
	);

	return $categories;
}); 