<?php

class Settings_API_Tabs_PricePlow_Plugin extends PricePlowCore {
	
	/*
	 * For easier overriding we declared the keys
	 * here as well as our tabs array which is populated
	 * when registering settings
	 */
	private $priceplow_general_settings_key = 'priceplow_general_settings';
	private $priceplow_advanced_settings_key = 'priceplow_advanced_settings';
	private $priceplow_buyitnow_settings_key = 'priceplow_buyitnow_settings';
	private $plugin_options_key = 'priceplow_plugin_options';
	private $plugin_settings_tabs = array();
	
	private $_priceplowapi;
	/*
	 * Fired during plugins_loaded (very very early),
	 * so don't miss-use this, only actions and filters,
	 * current ones speak for themselves.
	 */
	function __construct() {
		parent::__construct();
		add_action( 'init', array( &$this, 'load_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_priceplow_general_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_priceplow_advanced_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_priceplow_buyitnow_settings' ) );
		add_action( 'admin_menu', array( &$this, 'add_admin_menus' ) );
		
		add_filter( 'plugin_action_links_'.PRICEPLOW_PLUGIN_NAME, array( &$this, 'pluginSettingsLink' ) );
		
		
		$this->_priceplowapi = new PricePlowAPI();
	}
	
	/*
	 * Loads both the general and advanced settings from
	 * the database into their respective arrays. Uses
	 * array_merge to merge with default values if they're
	 * missing.
	 *
	 * To get settings, use a new PricePlowCore class and
	 *  call getGeneralOptions and getAdvancedOptions from there
	 */
	function load_settings() {
		$this->priceplow_general_settings = (array) get_option( $this->priceplow_general_settings_key );
		$this->priceplow_advanced_settings = (array) get_option( $this->priceplow_advanced_settings_key );
		$this->priceplow_buyitnow_settings = (array) get_option( $this->priceplow_buyitnow_settings_key );
		
		// Merge with defaults
		//$this->priceplow_general_settings = array_merge( array(
		//	'general_option' => 'General value'
		//), $this->priceplow_general_settings );

	}
	
	/*
	 * Registers the general settings via the Settings API,
	 * appends the setting to the tabs array of the object.
	 */
	function register_priceplow_general_settings() {
		$this->plugin_settings_tabs[$this->priceplow_general_settings_key] = __('General','priceplow');
		
		register_setting( $this->priceplow_general_settings_key, $this->priceplow_general_settings_key );
		add_settings_section( 'priceplow_section_general',__('PricePlow Profit Plugin','priceplow'), array( &$this, 'priceplow_section_general_desc' ), $this->priceplow_general_settings_key );
		add_settings_field( 'priceplow_featured_product',__('Default Number of Featured Products','priceplow') , array( &$this, 'field_priceplow_featured_product' ), $this->priceplow_general_settings_key, 'priceplow_section_general' );
		add_settings_field( 'priceplow_default_items_per_row',__('Default Featured Products Per Row','priceplow') , array( &$this, 'field_priceplow_default_items_per_row' ), $this->priceplow_general_settings_key, 'priceplow_section_general' );
                
                add_settings_field( 'priceplow_post_heading',__('Post Heading Text','priceplow') , array( &$this, 'field_priceplow_post_heading' ), $this->priceplow_general_settings_key, 'priceplow_section_general' );
                //add_settings_field( 'priceplow_sitecode',__('Site Code','priceplow') , array( &$this, 'field_priceplow_sitecode' ), $this->priceplow_general_settings_key, 'priceplow_section_general' );
                add_settings_field( 'priceplow_server_status',__('Server Status','priceplow') , array( &$this, 'field_priceplow_server_status' ), $this->priceplow_general_settings_key, 'priceplow_section_general' );
                add_settings_field( 'priceplow_disable_on_homepage',__('Do not show on homepage content','priceplow') , array( &$this, 'field_priceplow_disable_on_homepage' ), $this->priceplow_general_settings_key, 'priceplow_section_general' );
                add_settings_field( 'priceplow_powered_by',__('Make more money: Add "Powered by PricePlow" Link','priceplow') , array( &$this, 'field_priceplow_powered_by' ), $this->priceplow_general_settings_key, 'priceplow_section_general' );
                add_settings_field( 'priceplow_new_tab_links',__('Open all links in new tabs','priceplow') , array( &$this, 'field_priceplow_new_tab_links' ), $this->priceplow_general_settings_key, 'priceplow_section_general' );
	        add_settings_field( 'priceplow_terms',__('I agree to the <a href="https://www.priceplow.com/api/terms" target="_blank">PricePlow API Terms and Conditions</a>','priceplow') , array( &$this, 'field_priceplow_terms' ), $this->priceplow_general_settings_key, 'priceplow_section_general' );
		

	}

	/*
	 * Registers the general settings via the Settings API,
	 * appends the setting to the tabs array of the object.
	 */
	function register_priceplow_advanced_settings() {
		$this->plugin_settings_tabs[$this->priceplow_advanced_settings_key] = __('Advanced','priceplow');
		
		register_setting( $this->priceplow_advanced_settings_key, $this->priceplow_advanced_settings_key,array(&$this,'priceplow_advanced_settings_callback') );
		add_settings_section( 'priceplow_section_advanced',__('PricePlow Profit Plugin','priceplow'), array( &$this, 'priceplow_section_advanced_desc' ), $this->priceplow_advanced_settings_key );
		add_settings_field( 'priceplow_api_key',__('Public key','priceplow') , array( &$this, 'field_priceplow_api_key' ), $this->priceplow_advanced_settings_key, 'priceplow_section_advanced' );
		add_settings_field( 'priceplow_apiSecret_key',__('Secret key','priceplow') , array( &$this, 'field_priceplow_apiSecret_key' ), $this->priceplow_advanced_settings_key, 'priceplow_section_advanced' );
		add_settings_field( 'priceplow_disable_show_page_default',__('Do not show in page content by default','priceplow') , array( &$this, 'field_priceplow_disable_show_page_default'), $this->priceplow_advanced_settings_key, 'priceplow_section_advanced');
		add_settings_field( 'priceplow_disable_show_post_default',__('Do not show in post content by default','priceplow') , array( &$this, 'field_priceplow_disable_show_post_default' ), $this->priceplow_advanced_settings_key, 'priceplow_section_advanced' );
	
		add_settings_section( 'priceplow_section_default',__('Site Defaults for niche/brand/product-specific sites','priceplow'), array( &$this, 'priceplow_section_default_desc' ), $this->priceplow_advanced_settings_key );
		
		add_settings_field( 'priceplow_default_featured_brand',__('Featured Brand','priceplow') , array( &$this, 'field_priceplow_default_featured_brand' ), $this->priceplow_advanced_settings_key, 'priceplow_section_default' );
		add_settings_field( 'priceplow_default_featured_category',__('Featured Category','priceplow') , array( &$this, 'field_priceplow_default_featured_category' ), $this->priceplow_advanced_settings_key, 'priceplow_section_default' );
		add_settings_field( 'priceplow_default_featured_product',__('Featured Product','priceplow') , array( &$this, 'field_priceplow_default_featured_product' ), $this->priceplow_advanced_settings_key, 'priceplow_section_default' );
		add_settings_field( 'priceplow_default_stores_show',__('Default number of stores to show for Product Widgets','priceplow') , array( &$this, 'field_priceplow_default_stores_show' ), $this->priceplow_advanced_settings_key, 'priceplow_section_default' );
		
		
		add_settings_section( 'priceplow_section_campaign',__('Campaign Codes','priceplow'), array( &$this, 'priceplow_section_campaign_desc' ), $this->priceplow_advanced_settings_key );
		add_settings_field( 'priceplow_incoming_campaign_id',__('Campaign ID','priceplow') , array( &$this, 'field_priceplow_incoming_campaign_id' ), $this->priceplow_advanced_settings_key, 'priceplow_section_campaign' );
		add_settings_field( 'priceplow_affiliate_networks',__('Affilate Networks','priceplow') , array( &$this, 'field_priceplow_affiliate_networks' ), $this->priceplow_advanced_settings_key, 'priceplow_section_campaign' );
		
	}

	
	
	function register_priceplow_buyitnow_settings(){
		
		$this->plugin_settings_tabs[$this->priceplow_buyitnow_settings_key] = __('Buy it Now Links','priceplow');

		register_setting( $this->priceplow_buyitnow_settings_key, $this->priceplow_buyitnow_settings_key);
		add_settings_section( 'priceplow_section_buyitnow',__('Buy it Now Links','priceplow'), array( &$this, 'priceplow_section_buyitnow_desc' ), $this->priceplow_buyitnow_settings_key );
		add_settings_field( 'priceplow_buyitnow_brand',__('Brand','priceplow') , array( &$this, 'field_priceplow_buyitnow_brand' ), $this->priceplow_buyitnow_settings_key, 'priceplow_section_buyitnow' );
		add_settings_field( 'priceplow_buyitnow_product',__('Product','priceplow') , array( &$this, 'field_priceplow_buyitnow_product' ), $this->priceplow_buyitnow_settings_key, 'priceplow_section_buyitnow' );
		add_settings_field( 'priceplow_buyitnow_urls',__('Buy it Now URLs','priceplow') , array( &$this, 'field_priceplow_buyitnow_urls' ), $this->priceplow_buyitnow_settings_key, 'priceplow_section_buyitnow' );
		
	}
	
	
	
	/*
	 * The following methods provide descriptions
	 * for their respective sections, used as callbacks
	 * with add_settings_section
	 */
	function priceplow_section_general_desc() { echo '<p><a href="https://www.PricePlow.com">PricePlow</a> is a price comparison site for nutritional supplements and health products.  This plugin will allow you to add their price comparison widgets to your site and make revenue off of any sales generated.</p><p>You may also choose to allow the plugin to link to PricePlow\'s product page below.  You <em>will</em> get credit for any user you send to the site, just as you would get credit for sales generated with this plugin.</p><p>There are three places where these price comparisons can be added: on your posts, pages, and in your widgets page.</p>'; }
	
	
	function priceplow_section_advanced_desc() {
		echo '<p><strong>Your API Keys and Getting Connected</strong>
		<br>Upon new plugin activation, your site will have received an API key.
		If you do not see one below, please deactivate and re-activate the plugin to try again.
		You may also manually enter your API Key and Secret key below.</p>';
	}
	
        function priceplow_section_default_desc() {
		echo '<p><strong>Default Settings for Targeted MicroSites</strong>
		<br>This section contains default content options. If you are creating a microsite for a brand or a single product, you may choose it below.  You can also select a default category if you know <em>exactly</em> what you will normally be talking about.
		You will be able to set different brands/categories in the post editor for a specific post.</p>
		<p><em>Note for Product-Specific Widgets:</em>
		<br>In order to display <em>all</em> prices on featured products, you will need special API access.  Email <a href="mailto:contact@priceplow.com">contact@priceplow.com</a> to request special access.</p>';

	}
	function priceplow_section_campaign_desc() {
		echo '<p>Enter your campaign/site/affiliate codes at the following affiliate networks and sites below.';
		echo '<p>Your "Campaign ID" is provided by PricePlow upon initial registration and should not be changed.';
		echo '<p><em>Note: Before adding store/network campaign codes, you must have a working API Key / Secret saved up above.</em></p>';
	}
    
    
	function priceplow_section_buyitnow_desc(){
		echo '<p>Please select a brand, followed by a product.  We will then provide you with a permanent buy it now link for each size available for the product.</p>';
		echo '<p>This link will always go to the best deal for the product.</p>';
		echo '<p>You should always use these links instead of linking directly to any one store.  They do not expire and will provide you with more long-term revenue.</p>';
	
	}
	
	/*
	 * General Option field callback, renders a
	 * text input, note the name and value.
	 */
	function field_priceplow_featured_product() {
            
            $priceplow_featured_product = (isset($this->priceplow_general_settings['priceplow_featured_product'])?esc_attr( $this->priceplow_general_settings['priceplow_featured_product'] ):'6');
            
		?>
	     <select name="<?php echo $this->priceplow_general_settings_key; ?>[priceplow_featured_product]">
                        <option value=""><?php echo __('Choose','priceplow') ?></option>
                       <?php foreach($this->num_featured_products as $num_featured_product): ?>
                                <option value="<?php echo $num_featured_product; ?>" <?php selected($num_featured_product,$priceplow_featured_product); ?>><?php echo $num_featured_product; ?></option>
                       <?php endforeach; ?>	 
            </select>
           
		<?php
	}

    /*
     * General Option field callback, renders a
     * text input, note the name and value.
     */
    function field_priceplow_default_items_per_row() {

        $priceplow_default_items_per_row = (isset($this->priceplow_general_settings['c'])?esc_attr( $this->priceplow_general_settings['priceplow_default_items_per_row'] ):'3');

        ?>
        <select name="<?php echo $this->priceplow_general_settings_key; ?>[priceplow_default_items_per_row]">
            <option value=""><?php echo __('Choose','priceplow') ?></option>
            <?php foreach($this->num_featured_products_per_row as $num_featured_product_per_row): ?>
                <option value="<?php echo $num_featured_product_per_row; ?>" <?php selected($num_featured_product_per_row,$priceplow_default_items_per_row); ?>><?php echo $num_featured_product_per_row; ?></option>
            <?php endforeach; ?>
        </select>

    <?php
    }



    /*
     * General Option field callback, renders a
     * text input, note the name and value.
     */
	function field_priceplow_post_heading() {
            $priceplow_post_heading = (isset($this->priceplow_general_settings['priceplow_post_heading'])?esc_attr( $this->priceplow_general_settings['priceplow_post_heading'] ):'Related Products');
            
		?>
		<input type="text" name="<?php echo $this->priceplow_general_settings_key; ?>[priceplow_post_heading]" value="<?php echo $priceplow_post_heading; ?>" />
		<?php
	}	
	
	
	
	/*
	 * General Option field callback, renders a
	 * text input, note the name and value.
	 */
	/*function field_priceplow_sitecode() {
             $priceplow_sitecode = (isset($this->priceplow_general_settings['priceplow_sitecode'])?esc_attr( $this->priceplow_general_settings['priceplow_sitecode'] ):'');
		?>
		<input type="text"  readonly="readonly" name="<?php echo $this->priceplow_general_settings_key; ?>[priceplow_sitecode]" value="<?php echo $priceplow_sitecode; ?>" />
		<?php
	}*/
	
	
	/*
	 * General Option field callback, renders a
	 * text input, note the name and value.
	 */
	function field_priceplow_server_status() {
             //$priceplow_server_status = (isset($this->priceplow_general_settings['priceplow_server_status'])?esc_attr( $this->priceplow_general_settings['priceplow_server_status'] ):'OFF');
			 //$apiStatus = $this->checkApiStatus();
			 $apiStatus  = true;
			 if($apiStatus==true){
			    $priceplow_server_status = 'ON';	
			 }
			 else{
			     $priceplow_server_status = 'OFF';	
			 }
			
		?>
		<div class="onoffswitch">
		    <input type="checkbox" disabled="disabled" name="<?php echo $this->priceplow_general_settings_key; ?>[priceplow_server_status]" class="onoffswitch-checkbox" id="priceplow_server_status" value="ON" <?php checked($priceplow_server_status,'ON');?> readonly="readonly">
		    <label class="onoffswitch-label" for="priceplow_server_status">
			<div class="onoffswitch-inner"></div>
			<div class="onoffswitch-switch"></div>
		    </label>
		</div>
		
		<?php
	}	
	
	

	
	function field_priceplow_disable_on_homepage() {
		$priceplow_disable_on_homepage = (isset($this->priceplow_general_settings['priceplow_disable_on_homepage'])?esc_attr( $this->priceplow_general_settings['priceplow_disable_on_homepage'] ):'');
		?>
			<input type="checkbox" name="<?php echo $this->priceplow_general_settings_key; ?>[priceplow_disable_on_homepage]"  <?php checked( $priceplow_disable_on_homepage, 'on'); ?> />
			
			<?php
	}	

	function field_priceplow_powered_by() {
		$priceplow_powered_by = (isset($this->priceplow_general_settings['priceplow_powered_by'])?esc_attr( $this->priceplow_general_settings['priceplow_powered_by'] ):'');
		?>
			<input type="checkbox" name="<?php echo $this->priceplow_general_settings_key; ?>[priceplow_powered_by]"  <?php checked( $priceplow_powered_by, 'on'); ?> />
			<br/><em><?php echo __('You will get credit for all traffic your site generate to PricePlow. Will open in new tab by default.'); ?></em>
			<?php
		}	
			
					

	function field_priceplow_new_tab_links() {
        $priceplow_new_tab_links = (isset($this->priceplow_general_settings['priceplow_new_tab_links'])?esc_attr( $this->priceplow_general_settings['priceplow_new_tab_links'] ):'');
		?>
		<input type="checkbox" name="<?php echo $this->priceplow_general_settings_key; ?>[priceplow_new_tab_links]"  <?php checked( $priceplow_new_tab_links, 'on'); ?> />
		<?php
	}	
	
	/*
	 * General Option field callback, renders a
	 * text input, note the name and value.
	 */
	function field_priceplow_terms() {
                    $priceplow_terms = (isset($this->priceplow_general_settings['priceplow_terms'])?esc_attr( $this->priceplow_general_settings['priceplow_terms'] ):'');
		?>
		<input type="checkbox" name="<?php echo $this->priceplow_general_settings_key; ?>[priceplow_terms]"  <?php checked( $priceplow_terms, 'on'); ?> />
		<?php
	}	
	

        //###################### Advanced Settings Starts#############################

	function field_priceplow_api_key() {
            $priceplow_api_key =  (isset($this->priceplow_advanced_settings['priceplow_api_key'])?esc_attr( $this->priceplow_advanced_settings['priceplow_api_key'] ):'');
		?>
		<input type="text" name="<?php echo $this->priceplow_advanced_settings_key; ?>[priceplow_api_key]" value="<?php echo $priceplow_api_key; ?>" />
		<?php
	}


	function field_priceplow_apiSecret_key(){
            $priceplow_apiSecret_key =  (isset($this->priceplow_advanced_settings['priceplow_apiSecret_key'])?esc_attr( $this->priceplow_advanced_settings['priceplow_apiSecret_key'] ):'');
		?>
		<input type="password" name="<?php echo $this->priceplow_advanced_settings_key; ?>[priceplow_apiSecret_key]" id="priceplow_apiSecret_key" value="<?php echo $priceplow_apiSecret_key; ?>" />
		<input type="checkbox" id="priceplow_show_apiSecret"> <label for="priceplow_show_apiSecret"><?php echo __("Display my secret key");?></label>
		<?php
			
	}
	
	
	function field_priceplow_disable_show_page_default() {
		$priceplow_disable_show_page_default =  (isset($this->priceplow_advanced_settings['priceplow_disable_show_page_default'])?esc_attr( $this->priceplow_advanced_settings['priceplow_disable_show_page_default'] ):'');
		?>
		<input type="checkbox" name="<?php echo $this->priceplow_advanced_settings_key; ?>[priceplow_disable_show_page_default]"  <?php checked( $priceplow_disable_show_page_default, 'on'); ?> />
            <br/><small><em><?php echo __('You can manually add a Related Products widget on each page\'s edit screen.'); ?></em></small>
			<?php
		}	
	
	function field_priceplow_disable_show_post_default() {
			$priceplow_disable_show_post_default =  (isset($this->priceplow_advanced_settings['priceplow_disable_show_post_default'])?esc_attr( $this->priceplow_advanced_settings['priceplow_disable_show_post_default'] ):'');
			?>
			<input type="checkbox" name="<?php echo $this->priceplow_advanced_settings_key; ?>[priceplow_disable_show_post_default]"  <?php checked( $priceplow_disable_show_post_default, 'on'); ?> />
            <br/><small><em><?php echo __('You can manually add a Related Products widget on each post\'s edit screen.'); ?></em></small>
			<?php
	}		
	



	
//###############Default settings fields####################	

	
	function field_priceplow_default_featured_brand(){
		$priceplow_default_featured_brand =  (isset($this->priceplow_advanced_settings['priceplow_default_featured_brand'])?esc_attr( $this->priceplow_advanced_settings['priceplow_default_featured_brand'] ):'');
	
		$featured_brands =$this->getAllBrands();		
		 
		 
		?>
	     <select name="<?php echo $this->priceplow_advanced_settings_key; ?>[priceplow_default_featured_brand]" id="priceplow_default_featured_brand">
                        <option value=""><?php echo __('Choose','priceplow') ?></option>
                       <?php
		       if(count($featured_brands)):
		       foreach($featured_brands as $featured_brand): ?>
                                <option value="<?php echo $featured_brand['id']; ?>" <?php selected($featured_brand['id'],$priceplow_default_featured_brand); ?>><?php echo $featured_brand['name']; ?></option>
                       <?php endforeach;  endif;?>	 
            </select>
	    <?php
	    
	}
	
	function field_priceplow_default_featured_category(){
		$priceplow_default_featured_category =  (isset($this->priceplow_advanced_settings['priceplow_default_featured_category'])?esc_attr( $this->priceplow_advanced_settings['priceplow_default_featured_category'] ):'');
				
		$nested_categories = $this->getAllCategories();
		$featured_categories = $this->flatten_categories($nested_categories);

		 
		?>
	     <select name="<?php echo $this->priceplow_advanced_settings_key; ?>[priceplow_default_featured_category]" id="priceplow_default_featured_category">
                        <option value=""><?php echo __('Choose','priceplow') ?></option>
                       <?php
		       if(count($featured_categories)):
		       foreach($featured_categories as $category_id => $this_category): ?>
                                <option value="<?php echo $category_id; ?>" <?php selected($category_id,$priceplow_default_featured_category); ?>><?php
				
				for($i=0; $i<$this_category['depth']; $i++) {
					// Add spaces for depth
					echo  "&nbsp;&nbsp;";
				}
				echo $this_category['name']; ?></option>
                       <?php endforeach;  endif;?>	 
            </select>
	    <?php
	    
	    
	}
	
	function field_priceplow_default_featured_product(){
		 $priceplow_default_featured_product = (isset($this->priceplow_advanced_settings['priceplow_default_featured_product'])?esc_attr( $this->priceplow_advanced_settings['priceplow_default_featured_product'] ):'');
		 $priceplow_default_featured_brand =  (isset($this->priceplow_advanced_settings['priceplow_default_featured_brand'])?esc_attr( $this->priceplow_advanced_settings['priceplow_default_featured_brand'] ):'');
		 
		?>
	     <select name="<?php echo $this->priceplow_advanced_settings_key; ?>[priceplow_default_featured_product]" id="priceplow_default_featured_product">
                        <option value=""><?php echo __('Choose','priceplow') ?></option>
                       <?php
			if(isset($priceplow_default_featured_product) && $priceplow_default_featured_product !=""){
			echo $this->getPricePlowProductOptions($priceplow_default_featured_brand,$priceplow_default_featured_product);	
			}
		       
		       
			?>
            </select>
	    <?php
	}
	
	
	function field_priceplow_default_stores_show(){
		
		$priceplow_default_stores_show = (isset($this->priceplow_advanced_settings['priceplow_default_stores_show'])?esc_attr( $this->priceplow_advanced_settings['priceplow_default_stores_show'] ):'');
		?>
		<input type="number" name="<?php echo $this->priceplow_advanced_settings_key; ?>[priceplow_default_stores_show]" value="<?php echo $priceplow_default_stores_show; ?>" />
		<em><?php echo __('(Requires advanced API permissions)'); ?></em>
		<?php 
	}
	
//###################Campaign Section#################################


	function field_priceplow_incoming_campaign_id() {
            $priceplow_incoming_campaign_id =  (isset($this->priceplow_advanced_settings['priceplow_incoming_campaign_id'])?esc_attr( $this->priceplow_advanced_settings['priceplow_incoming_campaign_id'] ):'');
		?>
		<input type="text" name="<?php echo $this->priceplow_advanced_settings_key; ?>[priceplow_incoming_campaign_id]" value="<?php echo $priceplow_incoming_campaign_id; ?>" />
		<br/>
		<em><small><?php echo __('Do not change this unless you really know what you are doing!'); ?></small></em>
		<?php
		
	
	}
	
	
	function field_priceplow_affiliate_networks(){
		$affiliateNetworks = $this->getAffiliateCampaigns();
		
		
	?>
	<table>
	
	<?php
	
	
	if(count($affiliateNetworks)):
		foreach($affiliateNetworks as $network):
		
		?>
		<tr>
			<td><?php echo $network->affiliate_network_name; ?></td>
			<td><input type="text" name="<?php echo $this->priceplow_advanced_settings_key; ?>[priceplow_affiliate_networks][<?php echo $network->affiliate_network_id; ?>]" value="<?php echo $network->campaign_code; ?>"/></td>
		</tr>
		<?php
		endforeach;
	endif;
		?>
		</table>
	<?php
	}
	
	
	
	
//###############Buy it Now settings fields####################	

	
	function field_priceplow_buyitnow_brand(){
		$priceplow_buyitnow_brand =  (isset($this->priceplow_buyitnow_settings['priceplow_buyitnow_brand'])?esc_attr( $this->priceplow_buyitnow_settings['priceplow_buyitnow_brand'] ):'');
	
		$_brands =$this->getAllBrands();		
		 
		 
		?>
	     <select name="priceplow_buyitnow_brand" id="priceplow_buyitnow_brand">
                        <option value=""><?php echo __('Choose','priceplow') ?></option>
                       <?php
		       if(count($_brands)):
		       foreach($_brands as $_brand): ?>
                                <option value="<?php echo $_brand['id']; ?>"><?php echo $_brand['name']; ?></option>
                       <?php endforeach;  endif;?>	 
            </select>
	    <?php
	    
	}


	function field_priceplow_buyitnow_product(){
		 
		?>
	     <select name="priceplow_buyitnow_product" id="priceplow_buyitnow_product">
                        <option value=""><?php echo __('Choose','priceplow') ?></option>
                 
            </select>
	    <?php
	}


	function field_priceplow_buyitnow_urls(){
	?>
	<div id="buyitnow-container"></div>
	<?php

	}


	
	/*
	 * Called during admin_menu, adds an options
	 * page under Settings called My Settings, rendered
	 * using the plugin_options_page method.
	 */
	function add_admin_menus() {
		add_options_page(__('PricePlow Profit Plugin','priceplow'),__('PricePlow Profit Plugin','priceplow'), 'manage_options', $this->plugin_options_key, array( &$this, 'plugin_options_page' ) );
	}
	
	
	#
	# Plugin Settings link
	#

	public function pluginSettingsLink($links){
	   $settings_link = '<a href="options-general.php?page='.$this->plugin_options_key.'.php">'.__('Settings').'</a>'; 
	   array_unshift($links, $settings_link); 
	  return $links; 
	}
	
	/*
	 * This function will run when the Advanced Settings get changed.
	 *  Go through each affiliate network, set up the campaign code (even if it's blank), and save it.
	 *  This will then get sent to saveAffiliateCampaigns, which saves using the API.
	 */
	public function priceplow_advanced_settings_callback($input){

        $priceplow_affiliate_networks = array();
        if(!empty($input['priceplow_affiliate_networks'])) {
            $priceplow_affiliate_networks = $input['priceplow_affiliate_networks'];
        }

        $affiliate_campaigns = array();  // Build a big array here

        if(!empty($priceplow_affiliate_networks)) {
            foreach($priceplow_affiliate_networks as $network_id=>$campaign_code) {
                $affiliate_campaigns[] = array('affiliate_network_id'=>$network_id,'campaign_code'=>$campaign_code);
            }
        }

        $af_response = $this->saveAffiliateCampaigns($affiliate_campaigns);

        if(isset($af_response->message)) {
            add_settings_error('priceplow-affiliate','priceplow-affiliate',"Error saving the affiliate campaigns: " . $af_response->message);
        }

		unset($input['priceplow_affiliate_networks']);
		return $input;
	}
	
	
	/*
	 * Plugin Options page rendering goes here, checks
	 * for active tab and replaces key with the related
	 * settings key. Uses the plugin_options_tabs method
	 * to render the tabs.
	 */
	function plugin_options_page() {
		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->priceplow_general_settings_key;
		?>
		<div class="wrap">
			<?php $this->plugin_options_tabs(); ?>
			<form method="post" action="options.php">
				<?php wp_nonce_field( 'update-options' ); ?>
				<?php settings_fields( $tab ); ?>
				<?php do_settings_sections( $tab ); ?>
				
				<?php
				if($tab != 'priceplow_buyitnow_settings'){
					submit_button();	
				}								
				?>
			</form>
		</div>
		<?php
	}
	
	/*
	 * Renders our tabs in the plugin options page,
	 * walks through the object's tabs array and prints
	 * them one by one. Provides the heading for the
	 * plugin_options_page method.
	 */
	function plugin_options_tabs() {
		$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->priceplow_general_settings_key;

		screen_icon();
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
		}
		echo '</h2>';
	}
};

// Initialize the plugin
add_action( 'plugins_loaded', create_function( '', '$settings_api_tabs_priceplow_plugin = new Settings_API_Tabs_PricePlow_Plugin;' ) );
