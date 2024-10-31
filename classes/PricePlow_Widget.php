<?php
/**
 * Adds PricePlow_Widget widget.
 */
class PricePlow_Widget extends WP_Widget {
        
        private $_priceplowapi;
        private $_priceplowcore;
        
	
        /**
	 * Register widget with WordPress.
	 */
	function __construct() {
            
		parent::__construct(
			'priceplow-widget', // Base ID
			__('PricePlow Product Widget', 'priceplow'), // Name
			array( 'description' => __( 'Add a PricePlow featured product/brand/category widget to your sidebar', 'priceplow' ), ) // Args
		);
                
                $this->_priceplowapi = new PricePlowAPI();
                $this->_priceplowcore = new PricePlowCore();
        }

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		?>
		
		
		<?php
				
		
                echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Featured Products', 'priceplow' );
		}
		
		 $general_setting = $this->_priceplowcore->getGeneralOptions();

		$priceplow_num_products = (isset($instance['priceplow_num_products'])?$instance['priceplow_num_products']:$general_setting['priceplow_featured_product']);
		$priceplow_num_products_per_row = (isset($instance['priceplow_num_products_per_row'])?$instance['priceplow_num_products_per_row']:$general_setting['priceplow_default_items_per_row']);
		$priceplow_feature_type = (isset($instance['priceplow_feature_type'])?$instance['priceplow_feature_type']:'');

		 $priceplow_category = (isset($instance['priceplow_category'])?$instance['priceplow_category']:'');
		 $priceplow_brand = (isset($instance['priceplow_brand'])?$instance['priceplow_brand']:'');
		 $priceplow_product_id = (isset($instance['priceplow_product_id'])?$instance['priceplow_product_id']:'');
		 
		 		
		 $priceplow_category_ajax_fields = array('widget_field_name'=>$this->get_field_name( 'priceplow_category') ,
		 				         				 'widget_field_id'=>$this->get_field_id( 'priceplow_category') 
											);
		 
		 
		 $priceplow_brand_ajax_fields = array('widget_field_name'=>$this->get_field_name( 'priceplow_brand') ,
						      				  'widget_field_id'=>$this->get_field_id( 'priceplow_brand')
						     				);
		 		 
	
		 $priceplow_product_ajax_fields = array('widget_field_name'=>$this->get_field_name( 'priceplow_brand') ,
							'widget_field_id'=>$this->get_field_id( 'priceplow_brand'),
							'widget_field_name_1'=>$this->get_field_name( 'priceplow_product_id') ,
							'widget_field_id_1'=>$this->get_field_id( 'priceplow_product_id'),
							);

		 $priceplow_top_products_ajax_fields = array();
		 
		 $priceplow_new_products_ajax_fields = array();
		 
		 $priceplow_hot_deals_ajax_fields = array('widget_field_name'=>$this->get_field_name( 'priceplow_deal') ,
		 				         				 'widget_field_id'=>$this->get_field_id( 'priceplow_deal') 
											);
		 
		 //	Advanced settings
		 
		 $priceplow_display_image 		 = (isset($instance['priceplow_display_image'])?$instance['priceplow_display_image']:'');
		 $priceplow_disable_product_name = (isset($instance['priceplow_disable_product_name'])?$instance['priceplow_disable_product_name']:'');
		 $priceplow_disable_product_meta = (isset($instance['priceplow_disable_product_meta'])?$instance['priceplow_disable_product_meta']:'');
		 $priceplow_max_stores_display 	 = (isset($instance['priceplow_max_stores_display'])?$instance['priceplow_max_stores_display']:'');
		 $priceplow_link_header 		 = (isset($instance['priceplow_link_header'])?$instance['priceplow_link_header']:'');
		 $priceplow_additional_div_class = (isset($instance['priceplow_additional_div_class'])?$instance['priceplow_additional_div_class']:'');
		 
		 
		
		 $priceplow_advanced_options = array(
		 		'priceplow_display_image'=>$priceplow_display_image,
		 		'priceplow_disable_product_name'=>$priceplow_disable_product_name,
		 		'priceplow_disable_product_meta'=>$priceplow_disable_product_meta,
		 		'priceplow_max_stores_display'=>$priceplow_max_stores_display,
		 		'priceplow_link_header'=>$priceplow_link_header,
		 		'priceplow_additional_div_class'=>$priceplow_additional_div_class,
		 );
		 

		 
		 $priceplow_advanced_options_ajax_fields = array(
		 				'widget_field_name_1'=>$this->get_field_name( 'priceplow_display_image') ,
		 				'widget_field_id_1'=>$this->get_field_id( 'priceplow_display_image'),
		 		
				 		'widget_field_name_2'=>$this->get_field_name( 'priceplow_disable_product_name') ,
				 		'widget_field_id_2'=>$this->get_field_id( 'priceplow_disable_product_name'),
		 		
				 		'widget_field_name_3'=>$this->get_field_name( 'priceplow_disable_product_meta') ,
				 		'widget_field_id_3'=>$this->get_field_id( 'priceplow_disable_product_meta'),
		 		
				 		'widget_field_name_4'=>$this->get_field_name( 'priceplow_max_stores_display') ,
				 		'widget_field_id_4'=>$this->get_field_id( 'priceplow_max_stores_display'),
		 		
				 		'widget_field_name_5'=>$this->get_field_name( 'priceplow_link_header') ,
				 		'widget_field_id_5'=>$this->get_field_id( 'priceplow_link_header'),
		 		
				 		'widget_field_name_6'=>$this->get_field_name( 'priceplow_additional_div_class') ,
				 		'widget_field_id_6'=>$this->get_field_id( 'priceplow_additional_div_class'),		 				 				 				 				 		
		 );
		 
		 
		 
		 ?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php

		echo '<div class="priceplow_meta_options">';
		echo '<p>';
		_e('What would you like to feature?', 'priceplow' );
		echo '</p>';
		
		echo '<ul>';
		
		$priceplow_feature_type_fieldName = $this->get_field_name( 'priceplow_feature_type' );
		
		foreach($this->_priceplowcore->priceplow_options as $option_id=>$option){
		
		
			echo '<li>';
			echo '<input type="radio" id="'.$option_id.'" name="'.$priceplow_feature_type_fieldName.'" '.checked($option_id,$priceplow_feature_type,false	).' class="widefat priceplow_feature_type" value="'.$option_id.'"';            
			echo '/> &nbsp;';
			echo '<label for="'.$option_id.'">';
			_e( $option, 'priceplow' );
			echo '</label> ';
		
		}
		echo '</li>';
		
		echo '</ul>';
		echo '</div>';
		
		echo '<div class="priceplow_load_container"><span class="spinner"></span>';
		
		if(!empty($priceplow_category) && $priceplow_feature_type == 'category'){
			
			echo   $this->_priceplowcore->getPricePlowCategory($priceplow_category,$priceplow_category_ajax_fields);
		}
		
		if(!empty($priceplow_brand) && $priceplow_feature_type == 'brand'){
				
			echo   $this->_priceplowcore->getPricePlowBrands($priceplow_brand,$priceplow_brand_ajax_fields);
		}
		
		if(!empty($priceplow_brand) && $priceplow_feature_type == 'product'){
				
			echo   $this->_priceplowcore->getPricePlowBrands($priceplow_brand,$priceplow_brand_ajax_fields);
			echo   $this->_priceplowcore->getPricePlowBrandProduct($priceplow_brand,$priceplow_product_id,$priceplow_product_ajax_fields);
		}		
		
		if($priceplow_feature_type == 'hot_deals'){
			
			//echo   $this->_priceplowcore->getPricePlowDeal($priceplow_deal,$priceplow_hot_deals_ajax_fields);
			 echo  "The newest hot deals will be shown!";
		}		
		
		
		
		echo '</div>';
		
		
		echo '<div>';
		echo '<ul '.($priceplow_feature_type == 'product'?'style="display:none;"':'').'>';
		
		echo '<li>';
		echo '<label for="'.$this->get_field_id( 'priceplow_num_products' ).'">'.__('Number of products to show: ','priceplow').'</label>&nbsp;';
		echo '<select class="priceplow_num_products widefat" id="'.$this->get_field_id( 'priceplow_num_products' ).'" name="'.$this->get_field_name( 'priceplow_num_products' ).'">';
			foreach($this->_priceplowcore->num_featured_products as $num_featured_product):
				echo '<option value="'.$num_featured_product.'" '.selected($priceplow_num_products,$num_featured_product,false).' >'.$num_featured_product.'</option>';
			endforeach;
		echo '</select>';
		echo '</li>';
	
		echo '<li>';
		echo '<label for="'.$this->get_field_id( 'priceplow_num_products_per_row' ).'">'.__('Number of products per row: ','priceplow').'</label>&nbsp;';
		echo '<select class="priceplow_num_products_per_row widefat" id="'.$this->get_field_id( 'priceplow_num_products_per_row' ).'" name="'.$this->get_field_name( 'priceplow_num_products_per_row' ).'">';
		foreach($this->_priceplowcore->num_featured_products_per_row as $num_featured_product_per_row):
		    echo '<option value="'.$num_featured_product_per_row.'" '.selected($priceplow_num_products_per_row,$num_featured_product_per_row,false).' >'.$num_featured_product_per_row.'</option>';
		endforeach;
		echo '</select>';
		echo '</li>';
		echo '</ul>';
		
		echo '<ul '.($priceplow_feature_type != 'product'?'style="display:none;"':'').'>';
		echo '<li>';
		echo '<a href="#" class="priceplow-advanced-options-toggle show">'.__('Show Advanced Options','priceplow').'</a>';
		echo $this->_priceplowcore->getPricePlowProductAdvancedOptions($priceplow_advanced_options,$priceplow_advanced_options_ajax_fields);
		echo '</li>';
		echo '</ul>';
			
		?>
		
		<input type="hidden" class="priceplow_category_ajax_fields" value="<?php echo implode('::::',$priceplow_category_ajax_fields); ?>">
		<input type="hidden" class="priceplow_brand_ajax_fields" value="<?php echo implode('::::',$priceplow_brand_ajax_fields); ?>">
		<input type="hidden" class="priceplow_product_ajax_fields" value="<?php echo implode('::::',$priceplow_product_ajax_fields); ?>">
		<input type="hidden" class="priceplow_top_products_ajax_fields" value="<?php echo implode('::::',$priceplow_top_products_ajax_fields); ?>">
		<input type="hidden" class="priceplow_new_products_ajax_fields" value="<?php echo implode('::::',$priceplow_new_products_ajax_fields); ?>">
		<input type="hidden" class="priceplow_hot_deals_ajax_fields" value="<?php echo implode('::::',$priceplow_hot_deals_ajax_fields); ?>">
		
		<?php 
		
		
		
		echo '</div>';
		
		
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
	
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['priceplow_num_products'] = ( ! empty( $new_instance['priceplow_num_products'] ) ) ? strip_tags( $new_instance['priceplow_num_products'] ) : '';
		$instance['priceplow_num_products_per_row'] = ( ! empty( $new_instance['priceplow_num_products_per_row'] ) ) ? strip_tags( $new_instance['priceplow_num_products_per_row'] ) : '';
		$instance['priceplow_feature_type'] = ( ! empty( $new_instance['priceplow_feature_type'] ) ) ? strip_tags( $new_instance['priceplow_feature_type'] ) : '';
		$instance['priceplow_category'] = ( ! empty( $new_instance['priceplow_category'] ) ) ? strip_tags( $new_instance['priceplow_category'] ) : strip_tags( $old_instance['priceplow_category'] );
		$instance['priceplow_brand'] = ( ! empty( $new_instance['priceplow_brand'] ) ) ? strip_tags( $new_instance['priceplow_brand'] ) : strip_tags( $old_instance['priceplow_brand'] );
		$instance['priceplow_product_id'] = ( ! empty( $new_instance['priceplow_product_id'] ) ) ? strip_tags( $new_instance['priceplow_product_id'] ) : strip_tags( $old_instance['priceplow_product_id'] );
		
		// Advanced settings
		

		$instance['priceplow_display_image'] 		= ( ! empty( $new_instance['priceplow_display_image'] )  ? strip_tags( $new_instance['priceplow_display_image'] ) : 'off' );
		$instance['priceplow_disable_product_name'] = ( ! empty( $new_instance['priceplow_disable_product_name'] )  ? strip_tags( $new_instance['priceplow_disable_product_name'] ) : 'off' );
		$instance['priceplow_disable_product_meta'] = ( ! empty( $new_instance['priceplow_disable_product_meta'] )  ? strip_tags( $new_instance['priceplow_disable_product_meta'] ) : 'off' );
		$instance['priceplow_max_stores_display'] 	= ( ! empty( $new_instance['priceplow_max_stores_display'] )  ? strip_tags( $new_instance['priceplow_max_stores_display'] ) : '' );
		$instance['priceplow_link_header'] 			= ( ! empty( $new_instance['priceplow_link_header'] )  ? strip_tags( $new_instance['priceplow_link_header'] ) : 'off' );
		$instance['priceplow_additional_div_class'] = ( ! empty( $new_instance['priceplow_additional_div_class'] )  ? strip_tags( $new_instance['priceplow_additional_div_class'] ) :'off' );
		
		
		
		
		return $instance;
	}

} // class PricePlow_Widget