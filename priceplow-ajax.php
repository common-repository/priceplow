<?php

function priceplow_ajax_suport(){

    add_action( 'wp_ajax_priceplow_getproduct_action', 'priceplow_getproduct_action_handle' );
    add_action( 'wp_ajax_priceplow_getfeaturetype_action', 'priceplow_getfeaturetype_action_handle' );
    
    add_action( 'wp_ajax_priceplow_getbuyitnow_action', 'priceplow_getbuyitnow_action_handle' );
    
}
add_action( 'init', 'priceplow_ajax_suport' );

$_priceplowapi = new PricePlowAPI();
$_priceplowcore = new PricePlowCore();

function priceplow_getproduct_action_handle(){
	global $_priceplowapi;
	global $_priceplowcore;
	    
	$brand_id = $_GET['brand'];
	$html = '';  // Data will go here, initialize it
    
	$html .= $_priceplowcore->getPricePlowProductOptions($brand_id);
	
	echo $html;
	
  die();
}


function priceplow_getfeaturetype_action_handle(){
    global $_priceplowapi;
    global $_priceplowcore;
    $html = '';
    
    try {
	    $feature_type = $_GET['feature_type'];
	    
   
	    $widget_field_attr = array_filter(array('widget_field_name'=>(isset($_GET['widget_field_name'])?$_GET['widget_field_name']:''),
						    'widget_field_id'=>(isset($_GET['widget_field_id'])?$_GET['widget_field_id']:''),
						    'widget_field_name_1'=>(isset($_GET['widget_field_name_1'])?$_GET['widget_field_name_1']:''),
						    'widget_field_id_1'=>(isset($_GET['widget_field_id_1'])?$_GET['widget_field_id_1']:'')
						));
	    
	    if($feature_type =="category") {
		
			$html = $_priceplowcore->getPricePlowCategory('',$widget_field_attr);
			
	    }
	    elseif($feature_type =="brand") {
	    
	    	$html = $_priceplowcore->getPricePlowBrands('',$widget_field_attr);
	    	
	    	
	    	/*$html .='<div class="priceplow_brandproduct_wrap" style="display:none;">';
	    	$html .='<h4>'.__("Select a Product:").'</h4>
	    	<label for "priceplow_brandproduct">'.__("Product:").'</label>
	    	<select name="priceplow_brandproduct" id="priceplow_brandproduct" class="priceplow_brandproduct">';*/
	    	
	    	$html .= '</select>';
	    	$html .='</div>';
	    }
        elseif($feature_type == "product") {
	    
	    if(count($widget_field_attr)){
		    $field_name = $widget_field_attr['widget_field_name_1'];
		    $field_id = $widget_field_attr['widget_field_id_1'];
	    }
	    else{
		    $field_name = 'priceplow_product_id';
		    $field_id = 'priceplow_product_id';
	    }
	
            $html = $_priceplowcore->getPricePlowBrands('',$widget_field_attr);
            $html .='<div class="priceplow_brandproduct_wrap" style="display:none;">';
            $html .='<h4>'.__("Select a Product:").'</h4>
	    	<label for="'.$field_id.'">'.__("Product:").'</label>
	    	<select name="'.$field_name.'" id="'.$field_id.'" class="priceplow_product_id">';
	    $html .= '</select>';
        }
	    elseif($feature_type =="top_products") {
	    	
	    	
	    
	    }
	    elseif($feature_type =="new_products") {
	    	
		/*
	    	$newProducts = $_priceplowapi->getNewestProducts();

	    	$html .='<h4>'.__("Select a Product:").'</h4>
	    	<label for "priceplow_newproduct_select">'.__("Product:").'</label>
	    	<select name="priceplow_newproduct_select" id="priceplow_newproduct_select">';
	    	
	    	foreach($newProducts as $product) {
	    		$html .= '<option value="'.$product->id.'">'.$product->name.'</option>';
	    	}
	    	
	    	$html .= '</select>';
		*/
		
		$html .= 'Newest products will always be featured!';
	    	
	    
	    }
	    elseif($feature_type =="hot_deals") {
		
	     $html ="The newest hot deals will be shown!";	
		//$html = $_priceplowcore->getPricePlowDeals('',$widget_field_attr);
	    
	    }
    
    } catch (Exception $e) {
    	echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
    
    echo $html;
    die();
}


function priceplow_getbuyitnow_action_handle(){
    global $_priceplowcore;   
    $priceplow_buyitnow_product = $_GET['buyitnow_product'];
    
    $_priceplowcore->getBuyItNowURLS($priceplow_buyitnow_product); 
    
    die();
}