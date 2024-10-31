<?php
/** 
 * The PricePlow Meta Class for Posts/Pages
 */
class PricePlow_Meta_Box extends PricePlowCore {
        
	private $_priceplowapi;
	private $_priceplowcore;
	
	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
		add_filter('the_content',array( $this, 'priceplow_add_empty_content_div' ),9);
		
		$this->_priceplowapi = new PricePlowAPI();
		$this->_priceplowcore = new PricePlowCore();
	}

	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box($postType) {
		$types = array('post', 'page');
		if(in_array($postType, $types))
		{
			add_meta_box(
				 'priceplow_featured_meta_box'
				,__( 'PricePlow Featured Products', 'priceplow' )
				,array( $this, 'render_meta_box_content' )
				,$postType
				,'side'
			);
		}
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {
		
		
		// Check if our nonce is set.
		if ( ! isset( $_POST['priceplow_meta_nonce'] ) )
		  return $post_id;
	      
		$nonce = $_POST['priceplow_meta_nonce'];
	      
		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'priceplow_meta_nonce' ) )
		    return $post_id;		
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;
		 
		 
		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {
	      
		  if ( ! current_user_can( 'edit_page', $post_id ) )
		      return $post_id;
		
		} else {
	      
		  if ( ! current_user_can( 'edit_post', $post_id ) )
		      return $post_id;
		}
		
		
		// Sanitize and update the user input.
        if(!empty($_POST['priceplow_feature_type'])) {
            $priceplow_feature_type = sanitize_text_field( $_POST['priceplow_feature_type'] );
            update_post_meta( $post_id, '_priceplow_feature_type', $priceplow_feature_type );
        }

        if(!empty($_POST['priceplow_category'])) {
            $priceplow_category = sanitize_text_field( $_POST['priceplow_category'] );
            update_post_meta( $post_id, '_priceplow_category', $priceplow_category );
        }

	if(!empty($_POST['priceplow_brand_id'])) {
            $priceplow_brand_id = sanitize_text_field( $_POST['priceplow_brand_id'] );
            update_post_meta( $post_id, '_priceplow_brand_id', $priceplow_brand_id );
        }

        if(!empty($_POST['priceplow_product_id'])) {
            $priceplow_product_id = sanitize_text_field( $_POST['priceplow_product_id'] );
            update_post_meta( $post_id, '_priceplow_product_id', $priceplow_product_id );
        }
	
        if(!empty($_POST['priceplow_deal'])) {
            $priceplow_deal = sanitize_text_field( $_POST['priceplow_deal'] );
            update_post_meta( $post_id, '_priceplow_deal', $priceplow_deal );
        }
		
	if(!empty($_POST['priceplow_num_products'])) {
            $priceplow_num_products = sanitize_text_field( $_POST['priceplow_num_products'] );
            update_post_meta( $post_id, '_priceplow_num_products', $priceplow_num_products );
        }

        if(!empty($_POST['priceplow_num_products_per_row'])) {
            $priceplow_num_products_per_row = sanitize_text_field( $_POST['priceplow_num_products_per_row'] );
            update_post_meta( $post_id, '_priceplow_num_products_per_row', $priceplow_num_products_per_row );
        }

        
	
	$priceplow_disable = (!empty($_POST['priceplow_disable'])?sanitize_text_field( $_POST['priceplow_disable'] ):'off');	
	update_post_meta( $post_id, '_priceplow_disable', $priceplow_disable );
	
	//Advanced settings
	
	
	$priceplow_display_image = (!empty($_POST['priceplow_display_image'])?sanitize_text_field( $_POST['priceplow_display_image'] ):'off');
        update_post_meta( $post_id, '_priceplow_display_image', $priceplow_display_image );
	

	$priceplow_disable_product_name = (!empty($_POST['priceplow_disable_product_name'])?sanitize_text_field( $_POST['priceplow_disable_product_name'] ):'off');
        update_post_meta( $post_id, '_priceplow_disable_product_name', $priceplow_disable_product_name );

	$priceplow_disable_product_meta = (!empty($_POST['priceplow_disable_product_meta'])?sanitize_text_field( $_POST['priceplow_disable_product_meta'] ):'off');
        update_post_meta( $post_id, '_priceplow_disable_product_meta', $priceplow_disable_product_meta );

	
	$priceplow_max_stores_display = (!empty($_POST['priceplow_max_stores_display'])?sanitize_text_field( $_POST['priceplow_max_stores_display'] ):'');
        update_post_meta( $post_id, '_priceplow_max_stores_display', $priceplow_max_stores_display );

	$priceplow_link_header = (!empty($_POST['priceplow_link_header'])?sanitize_text_field( $_POST['priceplow_link_header'] ):'off');
        update_post_meta( $post_id, '_priceplow_link_header', $priceplow_link_header );

	$priceplow_additional_div_class = (!empty($_POST['priceplow_additional_div_class'])?sanitize_text_field( $_POST['priceplow_additional_div_class'] ):'off');
        update_post_meta( $post_id, '_priceplow_additional_div_class', $priceplow_additional_div_class );

	
		
	}


	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	
	public function render_meta_box_content( $post ) {

		// Use get_post_meta to retrieve an existing value from the database
		
		
		$priceplow_feature_type = get_post_meta( $post->ID, '_priceplow_feature_type', true );
		$priceplow_category = get_post_meta( $post->ID, '_priceplow_category', true );
		
		$priceplow_brand_id = get_post_meta( $post->ID, '_priceplow_brand_id', true );
		$priceplow_product = get_post_meta( $post->ID, '_priceplow_product_id', true );
		
		$priceplow_deal = get_post_meta( $post->ID, '_priceplow_deal', true );
	
		
		$priceplow_num_products = get_post_meta( $post->ID, '_priceplow_num_products', true );
		$priceplow_num_products_per_row = get_post_meta( $post->ID, '_priceplow_num_products_per_row', true );

		$priceplow_disable = get_post_meta( $post->ID, '_priceplow_disable', true );

                
		//Advanced settings
		$priceplow_display_image 	= get_post_meta( $post->ID, '_priceplow_display_image', true );		
		$priceplow_disable_product_name = get_post_meta( $post->ID, '_priceplow_disable_product_name', true );		
		$priceplow_disable_product_meta = get_post_meta( $post->ID, '_priceplow_disable_product_meta', true );
		$priceplow_max_stores_display 	= get_post_meta( $post->ID, '_priceplow_max_stores_display', true );
		$priceplow_link_header 		= get_post_meta( $post->ID, '_priceplow_link_header', true );
		$priceplow_additional_div_class = get_post_meta( $post->ID, '_priceplow_additional_div_class', true );
		
	    $priceplow_advanced_options = array(
	    					'priceplow_display_image'=>$priceplow_display_image,
				    		'priceplow_disable_product_name'=>$priceplow_disable_product_name,
				    		'priceplow_disable_product_meta'=>$priceplow_disable_product_meta,
				    		'priceplow_max_stores_display'=>$priceplow_max_stores_display,
				    		'priceplow_link_header'=>$priceplow_link_header,
				    		'priceplow_additional_div_class'=>$priceplow_additional_div_class,
	    					);
		
	    
	    
	    
		$general_setting = $this->_priceplowcore->getGeneralOptions();
		
		
		if(empty($priceplow_num_products)) {
			$priceplow_num_products = (isset($general_setting['priceplow_featured_product'])?$general_setting['priceplow_featured_product']:'');
			
		}

		if(empty($priceplow_num_products_per_row)) {
		    $priceplow_num_products_per_row = (isset($general_setting['priceplow_default_items_per_row'])?$general_setting['priceplow_default_items_per_row']:'');
		}
		
		 // Add an nonce field so we can check for it later.
		wp_nonce_field( 'priceplow_meta_nonce', 'priceplow_meta_nonce' );
        
        echo '<div class="priceplow_meta_options">';
        echo '<p>';
        _e('What would you like to feature?', 'priceplow' );
        echo '</p>';

        echo '<ul>';

        foreach($this->_priceplowcore->priceplow_options as $option_id=>$option){


        echo '<li>';
        echo '<input type="radio" id="'.$option_id.'" name="priceplow_feature_type" '.checked($option_id,$priceplow_feature_type,false	).' class="widefat priceplow_feature_type" value="'.$option_id.'"';
        echo '/> &nbsp;';

        echo '<label for="'.$option_id.'">';
        _e( $option, 'priceplow' );
        echo '</label> ';

        }
        echo '</li>';

        echo '</ul>';
        echo '</div>';

        echo '<div class="priceplow_load_container">';

		if($priceplow_feature_type =="category" && isset($priceplow_category)){
		    echo $this->_priceplowcore->getPricePlowCategory($priceplow_category);
		}

		if($priceplow_feature_type =="product" && isset($priceplow_brand_id)){
		
		echo $this->_priceplowcore->getPricePlowBrands($priceplow_brand_id);
		echo $this->_priceplowcore->getPricePlowBrandProduct($priceplow_brand_id, $priceplow_product);
		}
                if($priceplow_feature_type =="brand" && isset($priceplow_brand_id)){
                	echo $this->_priceplowcore->getPricePlowBrands($priceplow_brand_id);

                    // Comment from Mike - if we want to feature a *BRAND*, we do not need to worry
                    //  about specific products
                	/*if(isset($priceplow_brandproduct)){
                		$brand_id = $priceplow_brand_id;
                		
                		echo $this->_priceplowcore->getPricePlowBrandProduct($brand_id,$priceplow_brandproduct);
                	}*/
                	
                }

		if($priceplow_feature_type =="hot_deals" && isset($priceplow_deal)){
		    //echo $this->_priceplowcore->getPricePlowDeals($priceplow_deal);
		    echo  "The newest hot deals will be shown!";	
		}

                
        echo '</div>';


        echo '<div>';
        echo '<ul '.($priceplow_feature_type == 'product'?'style="display:none;"':'').'>';

        echo '<li>';
            echo '<label for="priceplow_num_products">'.__('Number of products to show: ','priceplow').'</label>&nbsp;';
            echo '<select class="widefat priceplow_num_products" id="priceplow_num_products" name="priceplow_num_products">';
             foreach($this->_priceplowcore->num_featured_products as $num_featured_product):
                    echo '<option value="'.$num_featured_product.'" '.selected($priceplow_num_products,$num_featured_product,false).' >'.$num_featured_product.'</option>';
                endforeach;
            echo '</select>';
        echo '</li>';

        echo '<li>';
        echo '<label for="priceplow_num_products_per_row">'.__('Number of products per row: ','priceplow').'</label>&nbsp;';
        echo '<select class="widefat priceplow_num_products" id="priceplow_num_products_per_row" name="priceplow_num_products_per_row">';
        foreach($this->_priceplowcore->num_featured_products_per_row as $num_featured_product_per_row):
            echo '<option value="'.$num_featured_product_per_row.'" '.selected($priceplow_num_products_per_row,$num_featured_product_per_row,false).' >'.$num_featured_product_per_row.'</option>';
        endforeach;
        echo '</select>';
        echo '</li>';
	
		echo '</ul>';
	
		echo '<ul '.($priceplow_feature_type != 'product'?'style="display:none;"':'').'>';
			echo '<li>';
			echo '<a href="#" class="priceplow-advanced-options-toggle show">'.__('Show Advanced Options','priceplow').'</a>';
			echo $this->_priceplowcore->getPricePlowProductAdvancedOptions($priceplow_advanced_options);
			echo '</li>';
        echo '</ul>';
        
        echo '</div>';
                
                
	echo '<div>';
		echo '<ul>';
		echo '<li>';               
		echo '<input type="checkbox" id="priceplow_disable" class="widefat"  name="priceplow_disable" '.checked($priceplow_disable,'on',false).' value="on" />&nbsp;';
		echo '<label for="priceplow_disable">'.__('Disable PricePlow on this Page','priceplow').'</label>';
		echo '</li>';                
		echo '</ul>';                
	echo '</div>';
                
	}

	
	/*
	 * add the empty div from posts/pages!
	 * This filter function will take the content and add an empty div at the end
	 *  if the post is configured to do so
	 */
	public function priceplow_add_empty_content_div($content) {
	    $this_post_id = get_the_ID();
	    $post_meta = get_post_meta($this_post_id);
	    $post_type = get_post_type();

	    $general_setting = $this->getGeneralOptions();
        $advanced_setting = $this->getAdvancedOptions();

        $priceplow_disable_on_homepage = (isset($general_setting['priceplow_disable_on_homepage'])?'true':'false');
        // Disable this if...
        // 1.  We're not on a post/page/category (TODO - custom post types), OR
        // 2.  We're on the home page and the user doesn't want that, OR
        // 3.  The user simply doesn't want it on this post/page, OR
        // 4.  The global options state they don't want it on this type of post AND they have no specific settings


        if(is_admin() ||
            ($post_type != "page" && $post_type != "post" && $post_type != "category") ||
            ($priceplow_disable_on_homepage && is_home() && is_front_page()) ||
            (!empty($advanced_setting['priceplow_disable_show_post_default']) && $post_type == "post" && empty($post_meta['_priceplow_feature_type'][0])) ||
            (!empty($advanced_setting['priceplow_disable_show_page_default']) && $post_type == "page" && empty($post_meta['_priceplow_feature_type'][0])) ||
            (!empty($post_meta['_priceplow_disable'][0]) && $post_meta['_priceplow_disable'][0] == "on")) {
                return $content;
        }

        
        // OK - If we're at this point, we want a widget.  It will be titled priceplow-widget-($priceplow-widget-usages)
        //$empty_div = '<div class="priceplow-container priceplow-divisible-by-3" id="priceplow-widget-0"></div>';
        $empty_div = '<div id="priceplow-widget-0"></div>';

        return $content . $empty_div;
	}
	
}

$priceplow_meta_box = new PricePlow_Meta_Box();