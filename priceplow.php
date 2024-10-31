<?php
/**
 * @package PricePlow Profit Plugin
 * @version 0.2.1
 */
/*
Plugin Name: PricePlow Profit Plugin
Plugin URI: http://www.priceplow.com/api
Description: A set of tools to insert <a href="https://www.PricePlow.com" title="Price comparisons for nutritional supplements">PricePlow.com</a>'s price comparison widgets into your site and provide revenue for health / nutrition / fitness / diet sites.  <strong><em>Note:</em></strong> By nature, this plugin will communicate with the <a href="https://www.PricePlow.com/api">PricePlow API</a>.  It will automatically register your site with the PricePlow Network.
Author: Mike Roberto
Version: 0.2.1
Author URI: http://www.priceplow.com
*/

/*  Copyright 2014  ClutchWave Inc.  (email : contact@priceplow.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 3, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('PRICEPLOW_PLUGIN_NAME', plugin_basename(__FILE__));
define('PRICEPLOW_PLUGIN_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('PRICEPLOW_PLUGIN_VERSION','0.1');
define('PRICEPLOW_PLUGIN_API_VERSION','v1');

require_once 'classes/PricePlowAPI.php';
require_once 'classes/PricePlowCore.php';
require_once 'classes/PricePlow_Widget.php';
require_once 'priceplow-ajax.php';
require_once 'classes/settings.php';
require_once 'classes/PricePlow_Meta.php';
require_once 'priceplow-staticFunctions.php';

if (!class_exists('Wp_PricePlow')) {

	class Wp_PricePlow{
		/**
		 * @var Wp_PricePlow
		 */
		static private $_instance = null;
		
		private $_priceplowapi;
		
		private $_priceplowcore;
		
		/**
		 * Get Wp_PricePlow object
		 *
		 * @return Wp_PricePlow
		 */
		static public function getInstance()
		{
			if (self::$_instance == null) {
				self::$_instance = new Wp_PricePlow();
			}

			return self::$_instance;
		}


		private function __construct()
		{

			register_activation_hook(PRICEPLOW_PLUGIN_NAME, array(&$this, 'pluginActivate'));
			register_deactivation_hook(PRICEPLOW_PLUGIN_NAME, array(&$this, 'pluginDeactivate'));
			register_uninstall_hook(PRICEPLOW_PLUGIN_NAME, array('wp-priceplow', 'pluginUninstall'));

			
			## Register plugin widgets
			add_action('init', array($this, 'load_priceplow_transl'));
			add_action('plugins_loaded', array(&$this, 'pluginLoad'));

			add_action( 'widgets_init', array(&$this, 'widgetsRegistration') );
			
			if (is_admin()) {
			add_action('wp_print_scripts', array(&$this, 'adminLoadScripts'));
			add_action('wp_print_styles', array(&$this, 'adminLoadStyles'));
			}
			else{

			add_action('wp_print_scripts', array(&$this, 'siteLoadScripts'));
			add_action('wp_print_styles', array(&$this, 'siteLoadStyles'));


			}

			add_action( 'wp_footer',array(&$this, 'footerScript'));

			$this->_priceplowapi = new PricePlowAPI();
			$this->_priceplowcore = new PricePlowCore();
		}

		public function load_priceplow_transl()
		{
			load_plugin_textdomain('wp-priceplow', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
		}

		##
		## Loading Scripts and Styles
		##
	
		public function adminLoadStyles()
		{
            	  

		}
	
		public function adminLoadScripts(){
		  
		  wp_enqueue_script( 'jquery' );
		          	wp_enqueue_script(
        			'priceplow-script',
        			plugins_url('js/priceplow-admin.js', __FILE__),
        			array('jquery')
        	);

		
		  wp_register_style('priceplow-admin-style',plugins_url('css/priceplow-admin.css', __FILE__));
        	  wp_enqueue_style( 'priceplow-admin-style' );
	
		}
	
	
	
		public function siteLoadStyles(){
			

            wp_register_style( 'priceplow-style', plugins_url('/css/priceplow.css', __FILE__));
            wp_enqueue_style( 'priceplow-style' );
            wp_register_style( 'priceplow-custom-style', plugins_url('/css/priceplow-custom.css', __FILE__));
            wp_enqueue_style( 'priceplow-custom-style' );
	
		}
	
	
		public function siteLoadScripts(){
		 wp_enqueue_script( 'jquery' );			  
		}



		##
		## Widgets initializations
		##

		public function widgetsRegistration()
		{
		  
		  register_widget('PricePlow_Widget');
		 		 
		}


		
		##
		## Plugin Activation and Deactivation
		##

		/**
		* Activate plugin
		* @return void
		*/
		public function pluginActivate()
		{
			$settings_advanced = $this->_priceplowcore->getAdvancedOptions();
			
			if($settings_advanced['priceplow_api_key'] == '' && $settings_advanced['priceplow_apiSecret_key'] == ''){
				
				$response = $this->_priceplowapi->RegisterPricePlow();

				
				
				if(empty($response->api_key)){
					wp_die( __( 'Unable to connect to Priceplow Api, Please contact the support!' ) );	
				}
				
				
				$api['priceplow_api_key'] = $response->api_key;
				$api['priceplow_apiSecret_key'] = $response->api_secret;
				$api['priceplow_incoming_campaign_id'] = $response->incoming_campaign_id;
				
				update_option('priceplow_advanced_settings', $api);
				
			}
			
			$settings_general = $this->_priceplowcore->getGeneralOptions();
			
		
			
			if(empty($settings_general['priceplow_featured_product'])){
				$settings_general['priceplow_featured_product'] = 6;
			}
	
			if(empty($settings_general['priceplow_default_items_per_row'])) {
				$settings_general['priceplow_default_items_per_row'] = 3;
			}
			
			if(empty($settings_general['priceplow_post_heading'])){
				$settings_general['priceplow_post_heading'] = 'Related Products';
			}

			if(empty($settings_general['priceplow_new_tab_links'])) {
				$settings_general['priceplow_new_tab_links'] = 'on';
			}
			else{
				$settings_general['priceplow_new_tab_links'] = 'off';
			}
			
			
			update_option('priceplow_general_settings', $settings_general);
			
			
		 
		}

		/**
		* Deactivate plugin
		* @return void
		*/
		public function pluginDeactivate(){
			
		}

		/**
		* Uninstall plugin
		* @return void
		*/
		static public function pluginUninstall()
		{

		}


		public function pluginLoad(){

		}
		
		public function footerScript(){
		  
            if( wp_script_is( 'jquery', 'done' ) ) {
              ?>
            <script type="text/javascript">
             // <![CDATA[
             <?php echo $this->_priceplowcore->getEmbedCode(); ?>
             (function () {
                  var s = document.createElement('script'); s.async = true;
                  s.type = 'text/javascript';
                  s.src = '<?php echo plugins_url('js/priceplow.js', __FILE__); ?>';

                  (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
              }());
              //]]>
            </script>

            <?php
            }
        }
    }
}



//instantiate the class
if (class_exists('Wp_PricePlow')) {
	$Wp_PricePlow =  Wp_PricePlow::getInstance();
}
