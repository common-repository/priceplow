<?php
class PricePlowCore extends PricePlowAPI{
    
    public $num_featured_products = array(2,3,4,5,6,7,8);
    public $num_featured_products_per_row = array(2,3);
    public $priceplow_options = array('category'=>"A category",
                                          'brand'=>'A brand',
                                          'product'=>'A product',
                                          'top_products'=>'Top Products',
                                          'new_products'=>'New products',
                                          'hot_deals'=>'Current Hot Deals'
                                          );
    public function __construct(){
        parent::__construct();    
    }
    
    public function getGeneralOptions(){
        
        return get_option( 'priceplow_general_settings' );
    }
    
    public function getAdvancedOptions(){
        
        return get_option( 'priceplow_advanced_settings' );
    }
    
    
    
    
    public function getPricePlowCategory($priceplow_category="",$widget_field_attr=array()){
    	
    	$html = "";
    	
    	if(count($widget_field_attr)){
    		$field_name = $widget_field_attr['widget_field_name'];
    		$field_id = $widget_field_attr['widget_field_id'];
    	}
    	else{
    		$field_name = 'priceplow_category';
    		$field_id = 'priceplow_category';
    	}
    	
    	$nested_categories = $this->getAllCategories();
    	$flat_categories = $this->flatten_categories($nested_categories);

    	$html .=' <h4>'.__( 'Select a Category:', 'priceplow' ).'</h4>
    	<label for "'.$field_id.'">'.__( 'Category:', 'priceplow' ).'</label>
    	<select name="'.$field_name.'" id="'.$field_id.'">';
    	foreach($flat_categories as $category_id => $this_category) {
    		// NOTE: Bootstrap-Select would be nice here
    		$html .= '<option value="'.$category_id.'" '.selected($category_id,$priceplow_category,false).'>';
    		for($i=0; $i<$this_category['depth']; $i++) {
    			// Add spaces for depth
    			$html .= "&nbsp;&nbsp;";
    		}
    		$html .= $this_category['name'];
    		$html .= '</option>';
    	}
    	$html .= '</select>';
    	
    	return  $html;
    	
    }
    
    
    public function getPricePlowBrands($priceplow_brand_id = '',$widget_field_attr=array()) {
    	
    	$html = '';
    	$brands = $this->getAllBrands();
    	
    	if(count($widget_field_attr)){
    		$field_name = $widget_field_attr['widget_field_name'];
    		$field_id = $widget_field_attr['widget_field_id'];
    	}
    	else{
    		$field_name = 'priceplow_brand_id';
    		$field_id = 'priceplow_brand_id';
    	}
    	
    	
        if(!isset($brands->error)) {
        
            $html .='<h4>'.__("Select a Brand:").'</h4>
            <label for "'.$field_id.'">'.__("Brand:").'</label>';
            if(count($brands)) {
                $html .= '<select name="'.$field_name.'" id="'.$field_id.'"  class="priceplow_brand" >';
                foreach($brands as $this_brand) {
                    $html .= '<option value="'.$this_brand['id'].'" '.selected($this_brand['id'],$priceplow_brand_id,false).'>'.$this_brand['name'].'</option>';
                }
                $html .= '</select>';
            }
            else {
                $html .= 'try again! api error';
            }
        }
        return $html;
    }
    
   public function getPricePlowDeals($priceplow_deal_id = '',$widget_field_attr=array()) {
    	
    	$html = '';
    	$deals = $this->getDeals();
    	
    	if(count($widget_field_attr)){
    		$field_name = $widget_field_attr['widget_field_name'];
    		$field_id = $widget_field_attr['widget_field_id'];
    	}
    	else{
    		$field_name = 'priceplow_deal';
    		$field_id = 'priceplow_deal';
    	}
    	
    	
        if(!isset($deals->error)) {
        
            $html .='<h4>'.__("Select a Deal:").'</h4>
            <label for "'.$field_id.'">'.__("Deals:").'</label>';
            if(count($deals)) {
                $html .= '<select name="'.$field_name.'" id="'.$field_id.'" >';
                foreach($deals as $deal) {
                    $html .= '<option value="'.$deal->product->id.'" '.selected($deal->product->id,$priceplow_deal_id,false).'>'.$deal->product->name.'</option>';
                }
                $html .= '</select>';
            }
            
        }
        return $html;
    }    
    
   public function getPricePlowBrandProduct($brand_id, $product_id='',$widget_field_attr=array()) {
        $html = '';

        if(count($widget_field_attr)){
            $field_name = $widget_field_attr['widget_field_name_1'];
            $field_id = $widget_field_attr['widget_field_id_1'];
        }
        else{
            $field_name = 'priceplow_product_id';
            $field_id = 'priceplow_product_id';
        }

        $html .='<div class="priceplow_brandproduct_wrap">';
        $html .='<h4>'.__("Select a Product:").'</h4>
        <label for="'.$field_id.'">'.__("Product:").'</label>
        <select name="'.$field_name.'" id="'.$field_id.'" class="priceplow_product_id">';
        $html .= '<option value="">'.__('Choose','priceplow').'</option>';
        $html .=  $this->getPricePlowProductOptions($brand_id, $product_id);


        $html .= '</select>';
        $html .='</div>';

        return  $html;
   }
   
   
   public function getPricePlowProductAdvancedOptions($field_values=array(),$widget_field_attr=array()){

        if(count($widget_field_attr)){
		
		$field_name_1 = $widget_field_attr['widget_field_name_1'];
		$field_id_1   = $widget_field_attr['widget_field_id_1'];
            
	   	$field_name_2 = $widget_field_attr['widget_field_name_2'];
	   	$field_id_2   = $widget_field_attr['widget_field_id_2'];
	   	 	
	    	$field_name_3 = $widget_field_attr['widget_field_name_3'];
	    	$field_id_3   = $widget_field_attr['widget_field_id_3'];
	    	
	    	$field_name_4 = $widget_field_attr['widget_field_name_4'];
	    	$field_id_4   = $widget_field_attr['widget_field_id_4'];
	    	
	    	$field_name_5 = $widget_field_attr['widget_field_name_5'];
	    	$field_id_5   = $widget_field_attr['widget_field_id_5'];
	    	
	    	$field_name_6 = $widget_field_attr['widget_field_name_6'];
	    	$field_id_6   = $widget_field_attr['widget_field_id_6'];
        }
        else{
          	$field_id_1 = $field_name_1 = 'priceplow_display_image';
	   	$field_id_2 = $field_name_2 = 'priceplow_disable_product_name';
	    	$field_id_3 = $field_name_3 = 'priceplow_disable_product_meta';
	    	$field_id_4 = $field_name_4 = 'priceplow_max_stores_display';
	    	$field_id_5 = $field_name_5 = 'priceplow_link_header';
	    	$field_id_6 = $field_name_6 = 'priceplow_additional_div_class';
	    	
        }
        
	
	$priceplow_display_image = (!empty($field_values['priceplow_display_image'])?$field_values['priceplow_display_image']:'on');
	$priceplow_disable_product_name = (!empty($field_values['priceplow_disable_product_name'])?$field_values['priceplow_disable_product_name']:'on');
	$priceplow_disable_product_meta = (!empty($field_values['priceplow_disable_product_meta'])?$field_values['priceplow_disable_product_meta']:'');
	$priceplow_max_stores_display =  (!empty($field_values['priceplow_max_stores_display'])?$field_values['priceplow_max_stores_display']:'');
	$priceplow_link_header =  (!empty($field_values['priceplow_link_header'])?$field_values['priceplow_link_header']:'on');
	$priceplow_additional_div_class =  (!empty($field_values['priceplow_additional_div_class'])?$field_values['priceplow_additional_div_class']:'');
	
	
	$html = '';
	$html .='<ul class="priceplow-advanced-options-wrap" style="display:none;">';
		$html .='<li>';
		$html .='<label for="'.$field_id_1.'">'.__('Display image ','priceplow').'</label>&nbsp;';
		$html .='<input type="checkbox" name="'.$field_name_1.'" id="'.$field_id_1.'" '.checked($priceplow_display_image,'on',false).' class="widefat">';
		$html .='</li>';

		$html .='<li>';
		$html .='<label for="'.$field_id_2.'">'.__('Display Product Name within widget ','priceplow').'</label>&nbsp;';
		$html .='<input type="checkbox" name="'.$field_name_2.'" id="'.$field_id_2.'" '.checked($priceplow_disable_product_name,'on',false).' class="widefat">';
		$html .='</li>';

		$html .='<li>';
		$html .='<label for="'.$field_id_3.'">'.__('Disable Meta Info (brand and category) ','priceplow').'</label>&nbsp;';
		$html .='<input type="checkbox" name="'.$field_name_3.'" id="'.$field_id_3.'" '.checked($priceplow_disable_product_meta,'on',false).' class="widefat">';
		$html .='</li>';
		
		$html .='<li>';
		$html .='<label for="'.$field_id_4.'">'.__('Max # stores to display ','priceplow').'</label>&nbsp;';
		$html .='<input type="number" name="'.$field_name_4.'" id="'.$field_id_4.'" value="'.$priceplow_max_stores_display.'" class="widefat" style="width: 50px;">';
		$html .='</li>';

		$html .='<li>';
		$html .='<label for="'.$field_id_5.'">'.__('Link Header to Product Page? ','priceplow').'</label>&nbsp;';
		$html .='<input type="checkbox" name="'.$field_name_5.'" id="'.$field_id_5.'" '.checked($priceplow_link_header,'on',false).' class="widefat">';
		$html .='</li>';
				
		$html .='<li>';
		$html .='<label for="'.$field_id_6.'">'.__(' Place inside of "textwidget" div <em>(this may look better on some sites)</em> ','priceplow').'</label>&nbsp;';
		$html .='<input type="checkbox" name="'.$field_name_6.'" id="'.$field_id_6.'" '.checked($priceplow_additional_div_class,'on',false).' class="widefat">';
		$html .='</li>';
		
		
	$html .='</ul>';
		
	return  $html;
   }
    
    public function getPricePlowProductOptions($brand_id,$product_id=''){
        
        $products =  $this->getAllProducts($brand_id);
	    $html = '';
        if(count($products)){
            
	    	foreach ($products as $product){
	    		$html .= '<option value="'.$product['id'].'" '.selected($product['id'],$product_id,false).'>'.$product['name'].'</option>';
	    	}
        }
        
        return $html;        
        
    }

    
    public function getPricePlowWidgetEmbedded(){

            $widgets = get_option('widget_priceplow-widget');
            $sidebar_widgets = get_option('sidebars_widgets');
            $advanced_settings = $this->getAdvancedOptions();
            
            $wp_inactive_widgets = $sidebar_widgets['wp_inactive_widgets'];
        
            
            $PricePlowWidget = array();
            if(!empty($widgets)) {
                $j=1;
                foreach($widgets as $key=>$widget){
                	
                	$thisWidgetID = 'priceplow-widget-'.$key;
                	
                	
                	if(!in_array($thisWidgetID, $wp_inactive_widgets)){
                	
       
                	
                	
                    $priceplow_feature_type =  $widget['priceplow_feature_type'];
                    if(!empty($priceplow_feature_type)){

                    	//Advanced settings
                    	
                    	$priceplow_display_image = (isset($widget['priceplow_display_image'])?$widget['priceplow_display_image']:'');
                    	$priceplow_disable_product_name = (isset($widget['priceplow_disable_product_name'])?$widget['priceplow_disable_product_name']:'');
                    	$priceplow_disable_product_meta = (isset($widget['priceplow_disable_product_meta'])?$widget['priceplow_disable_product_meta']:'');
                    	$priceplow_max_stores_display = (isset($widget['priceplow_max_stores_display'])?$widget['priceplow_max_stores_display']:'');
                    	$priceplow_link_header = (isset($widget['priceplow_link_header'])?$widget['priceplow_link_header']:'');
                    	$priceplow_additional_div_class = (isset($widget['priceplow_additional_div_class'])?$widget['priceplow_additional_div_class']:'');
                    	
						$widget_title = (isset($widget['title'])?$widget['title']:'');
						$priceplow_num_products = (isset($widget['priceplow_num_products'])?$widget['priceplow_num_products']:'');
						$priceplow_num_products_per_row = (isset($widget['priceplow_num_products_per_row'])?$widget['priceplow_num_products_per_row']:'');

						
						$priceplow_default_stores_show = (!empty($advanced_settings['priceplow_default_stores_show'])?$advanced_settings['priceplow_default_stores_show']:'');
                    	
                        $PricePlowWidget[$j]['widget_title']= $widget_title;
                      
                        $PricePlowWidget[$j]['items_per_row']= $priceplow_num_products_per_row;
                        $PricePlowWidget[$j]['feature_type']= $priceplow_feature_type;
                        $PricePlowWidget[$j]['div_identifier']= $thisWidgetID;
                        $PricePlowWidget[$j]['disable_title'] = 'true'; // Widgets provide their own titles

                        if($priceplow_feature_type != 'product') {
                        	
                        	$PricePlowWidget[$j]['items_to_show']= $priceplow_num_products;
                        }
                        
                        if($priceplow_feature_type=='category'){
                            $PricePlowWidget[$j]['category_id']= $widget['priceplow_category'];
                        } elseif($priceplow_feature_type=='brand') {
                            $PricePlowWidget[$j]['brand_id']= $widget['priceplow_brand'];
                        } elseif($priceplow_feature_type=='product') {
                            $PricePlowWidget[$j]['product_id']= $widget['priceplow_product_id'];

                            //Advanced settings
                            if($priceplow_display_image !='on'){
                            	$PricePlowWidget[$j]['disable_image']= true;
                            }
                            if($priceplow_disable_product_name !='on'){
                            	$PricePlowWidget[$j]['disable_product_name']= true;
                            }
                            if($priceplow_disable_product_meta =='on'){
                            	$PricePlowWidget[$j]['disable_product_meta']= true;
                            }
                   
                            
                            if(!empty($priceplow_max_stores_display)){
                            	$PricePlowWidget[$j]['items_to_show']= $priceplow_max_stores_display;
                            }
                            elseif(!empty($priceplow_default_stores_show)){
                            	$PricePlowWidget[$j]['items_to_show']= $priceplow_default_stores_show;
                            }

                            
                            if($priceplow_link_header =='on'){
                            	$PricePlowWidget[$j]['link_header']= true;
                            }
                            if($priceplow_link_header =='on'){
                            	$PricePlowWidget[$j]['additional_div_class']= "textwidget";
                            }
                                                        
                        } elseif($priceplow_feature_type=='top_products'){

                        } elseif($priceplow_feature_type=='new_products'){

                        } elseif($priceplow_feature_type=='hot_deals') {

                        }
                        $j++;
                    
                    }

                	}
                } // end of loop

                return ($PricePlowWidget);
            }
            else{
                return false;
            }
    
    }
    
    public function getPricePlowMetaEmbedded() {
            
        $post_id = get_the_ID();
        $post_meta = get_post_meta($post_id);
        
        $general_settings = $this->getGeneralOptions();
        $advanced_settings = $this->getAdvancedOptions();
        
        $priceplow_feature_type = isset($post_meta['_priceplow_feature_type'][0])?$post_meta['_priceplow_feature_type'][0]:'';
        

        
        $priceplow_category = (isset($post_meta['_priceplow_category'][0])?$post_meta['_priceplow_category'][0]:'');
        $priceplow_brand_id = (isset($post_meta['_priceplow_brand_id'][0])?$post_meta['_priceplow_brand_id'][0]:'');
        $priceplow_product_id = (isset($post_meta['_priceplow_product_id'][0])?$post_meta['_priceplow_product_id'][0]:'');

        //$priceplow_brandproduct = (isset($post_meta['_priceplow_brandproduct'][0])?$post_meta['_priceplow_brandproduct'][0]:'');

        $priceplow_num_products = (isset($post_meta['_priceplow_num_products'][0])?$post_meta['_priceplow_num_products'][0]:$general_settings['priceplow_featured_product']);
        $priceplow_num_products_per_row = (isset($post_meta['_priceplow_num_products_per_row'][0])?$post_meta['_priceplow_num_products_per_row'][0]:$general_settings['priceplow_default_items_per_row']);
        $priceplow_disable = (isset($post_meta['_priceplow_disable'][0])?$post_meta['_priceplow_disable'][0]:'');
        
        
        //Advanced settings
        
        $priceplow_display_image 		= (isset($post_meta['_priceplow_display_image'][0])?$post_meta['_priceplow_display_image'][0]:'');
        $priceplow_disable_product_name = (isset($post_meta['_priceplow_disable_product_name'][0])?$post_meta['_priceplow_disable_product_name'][0]:'');
        $priceplow_disable_product_meta = (isset($post_meta['_priceplow_disable_product_meta'][0])?$post_meta['_priceplow_disable_product_meta'][0]:'');
        $priceplow_max_stores_display 	= (isset($post_meta['_priceplow_max_stores_display'][0])?$post_meta['_priceplow_max_stores_display'][0]:'');
        $priceplow_link_header 			= (isset($post_meta['_priceplow_link_header'][0])?$post_meta['_priceplow_link_header'][0]:'');
        $priceplow_additional_div_class = (isset($post_meta['_priceplow_additional_div_class'][0])?$post_meta['_priceplow_additional_div_class'][0]:'');
        

  
        /*
         * 
         * Default settings for empty priceplow settings
         * 
         */
        if(empty($priceplow_feature_type)){
        	 
        	if(!empty($advanced_settings['priceplow_default_featured_product'])){
        		$priceplow_feature_type = 'product';
        		$priceplow_product_id = $advanced_settings['priceplow_default_featured_product'];
        	}        	
        	else if(!empty($advanced_settings['priceplow_default_featured_brand'])){
        		$priceplow_feature_type = 'brand';
        		$priceplow_brand_id = $advanced_settings['priceplow_default_featured_brand'];
        		        		
        	}
        	else if(!empty($advanced_settings['priceplow_default_featured_category'])){
        		$priceplow_feature_type = 'category';
        		$priceplow_category = $advanced_settings['priceplow_default_featured_category'];
        	}
        	else{
        		$priceplow_feature_type = 'top_products';
        	}
        	 
        }
        
        
        $priceplow_default_stores_show = (!empty($advanced_settings['priceplow_default_stores_show'])?$advanced_settings['priceplow_default_stores_show']:'');
        
        
        // Get the post heading.  If they chose "Hot Deals", display that
        if($priceplow_feature_type == "hot_deals") {
            $priceplow_post_heading = "Current Hot Nutrition Deals";
        } else {
            // Otherwise show the default...
            $priceplow_post_heading = (!empty($general_settings['priceplow_post_heading'])?$general_settings['priceplow_post_heading']:'');
        }
        
        
        $PricePlowWidget = array();

        if($priceplow_disable !="on" && !empty($priceplow_feature_type)) {
        		
        		$PricePlowWidget['widget_title']= $priceplow_post_heading;
               
                $PricePlowWidget['items_per_row']= $priceplow_num_products_per_row;
                $PricePlowWidget['feature_type']= $priceplow_feature_type;
                $PricePlowWidget['div_identifier']= 'priceplow-widget-0';

                if($priceplow_feature_type!='product') {
                	$PricePlowWidget['items_to_show']= $priceplow_num_products;
                }
                
                if($priceplow_feature_type=='category'){
                    $PricePlowWidget['category_id']= $priceplow_category;
                } elseif($priceplow_feature_type=='brand') {
                    $PricePlowWidget['brand_id']= $priceplow_brand_id;
                } elseif($priceplow_feature_type=='product') {
                    $PricePlowWidget['product_id']= $priceplow_product_id;
                    
                    //Advanced settings
                    if($priceplow_display_image !='on'){
                    	$PricePlowWidget['disable_image']= true;
                    }
                    if($priceplow_disable_product_name !='on'){
                    	$PricePlowWidget['disable_product_name']= true;
                    }               
                    if($priceplow_disable_product_meta =='on'){
                    	$PricePlowWidget['disable_product_meta']= true;
                    }                         
                    
                    if(!empty($priceplow_max_stores_display)){                    	
                    	$PricePlowWidget['items_to_show']= $priceplow_max_stores_display;
                    }
                    elseif(!empty($priceplow_default_stores_show)){
                    	$PricePlowWidget['items_to_show']= $priceplow_default_stores_show;
                    }
                    
                    
                    if($priceplow_link_header =='on'){
                    	$PricePlowWidget['link_header']= true;
                    } 
                    if($priceplow_link_header =='on'){
                    	$PricePlowWidget['additional_div_class']= "textwidget";
                    }                                       
                    
                    
                    
                    
                } elseif($priceplow_feature_type=='top_products'){

                } elseif($priceplow_feature_type=='new_products'){

                } elseif($priceplow_feature_type=='hot_deals') {

                }
                return array($PricePlowWidget);

        } else {
            return FALSE;
        }

    }


    public function getEmbedCode(){

        $general_settings = $this->getGeneralOptions();
        $advanced_settings = $this->getAdvancedOptions();

       // print_r($general_settings);
       // print_r($advanced_settings);

        $priceplow_api_key = $advanced_settings['priceplow_api_key'];

        $script ='';

        $script .='
                    var priceplow_settings = new Array();
                    // Set Global Settings
                    priceplow_settings["api_url_prefix"] = "'.$this->_ApiBaseUrl.'";'."\n";
        if(!empty($general_settings['priceplow_powered_by'])) {
            $script .= '
                    priceplow_settings["link_to_priceplow"] = true;';
        }
        if(!empty($general_settings['priceplow_new_tab_links'])) {
            $script .= '
                    priceplow_settings["new_tab_links"] = true;';
        } else {
            $script .= '
                    priceplow_settings["new_tab_links"] = false;';
        }

        if(!empty($priceplow_api_key)) {
            $script .= '
                    priceplow_settings["api_key"] = "'.$priceplow_api_key.'";';
        }
        if(!empty($advanced_settings["priceplow_incoming_campaign_id"]) && !empty($general_settings['priceplow_terms'])) {
            $script .= '
                    priceplow_settings["campaign_id"] = '.$advanced_settings['priceplow_incoming_campaign_id'].';';
        }
        if(!empty($advanced_settings["priceplow_default_featured_brand"])) {
            $script .= '
                    priceplow_settings["alert_brand_id"] = '.$advanced_settings['priceplow_default_featured_brand'].';';
        }

        // Individual section goes here.
        $priceplow_meta_embedded = $this->getPricePlowMetaEmbedded();
        $priceplow_widget_embedded = $this->getPricePlowWidgetEmbedded();

            $w=0;
            if(!empty($priceplow_meta_embedded)){
                foreach($priceplow_meta_embedded as $key=>$embed) {
                    $script .="
                    priceplow_settings[$w] = ".json_encode($embed).";\n";
                    $w++;
                }

            }

            if(!empty($priceplow_widget_embedded)){
                foreach($priceplow_widget_embedded as $key=>$embed) {
                    $script .="
                    priceplow_settings[$w] = ".json_encode($embed).";\n";
                    $w++;
                }
            }

        return $script;

    } // end of getEmbedCode
    public function getBuyItNowURLS($priceplow_buyitnow_product){
        $advanced_settings = $this->getAdvancedOptions();
        $priceplow_incoming_campaign_id = $advanced_settings['priceplow_incoming_campaign_id'];

        if($priceplow_buyitnow_product){

            $buyitnow_store = $this->getProductStorePrice($priceplow_buyitnow_product);

            $lowest_prices = $buyitnow_store->lowest_prices;
            ?>
            <table>

                <?php


                if(count($lowest_prices)):
                    foreach($lowest_prices as $lowest_price):


                        $url = $lowest_price->url.(!empty($priceplow_incoming_campaign_id)?'&ic='.$priceplow_incoming_campaign_id:'');
                        ?>
                        <tr>
                            <td><?php echo $lowest_price->size; ?></td>
                            <td><input type="text" name="priceplow_buyitnow_urls" value="<?php echo $url; ?>" readonly="readonly" style="width: 600px;"/></td>
                        </tr>
                    <?php
                    endforeach;
                endif;
                ?>
            </table>
        <?php
        }
    }
} // end of PricePlowCore class