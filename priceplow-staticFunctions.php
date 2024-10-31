<?php
/*
 *
 */

function getPricePlowBuyItNow($product_id){
    	global $_priceplowapi;
	global $_priceplowcore;

        $buyitnow_store = $_priceplowapi->getProductStorePrice($product_id);
		
	$lowest_prices = $buyitnow_store->lowest_prices;
        
        
        $advanced_settings = $_priceplowcore->getAdvancedOptions();
        $priceplow_incoming_campaign_id = $advanced_settings["priceplow_incoming_campaign_id"];
        
        if(count($lowest_prices)){
            $urls = array();
            foreach($lowest_prices as $lowest_price){
                $url = $lowest_price->url.(!empty($priceplow_incoming_campaign_id)?'&ic='.$priceplow_incoming_campaign_id:'');
                $urls[] =   $url;  
            }
            
            return (object)$urls;
            
        }
        else{
            return false;
        }

}

