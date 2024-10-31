<?php 
if (!class_exists('PricePlowAPI')) {

    class PricePlowAPI {
        
        private $_ApiUrl = '';
	    public $_ApiBaseUrl = '';
        private $_Apikey = '';
	    private $_ApiSecret = '';
        private $_options = '';
        private $_results = '';
	
	
        
        public function __construct() {
            $this->_ApiBaseUrl = "https://api.priceplow.com/".PRICEPLOW_PLUGIN_API_VERSION."/";

	    $advanced_settings = get_option('priceplow_advanced_settings');
	    $this->_Apikey = $advanced_settings['priceplow_api_key'];
	    $this->_ApiSecret = $advanced_settings['priceplow_apiSecret_key'];
	    
        }

        /*
         * Make a request to the API.  The method defaults to GET, but could be "PUT"
         *  This will always return the data in an object.
         */
        private function request($params,$method="GET", $data=null, $headers=array(),$response=false) {
            // 20140512 - We've updated the API so that the api_key is nearly always needed.
            //  So instead of updating each funciton, let's send the key if we have it.
            if(!array_key_exists('api_key', $params) && !empty($this->_Apikey)) {
                $params['api_key'] = $this->_Apikey;
            }
            $qstring =  http_build_query($params, '', '&');
            
                  $ApiUrl = $this->_ApiBaseUrl.$this->_ApiUrl.'?'.$qstring;


            $args = array(
                 'method'      =>    $method,
                 'timeout'     =>    50,
                 'redirection' =>    5,
                 'httpversion' =>    '1.0',
                 'blocking'    =>    true,
		         'sslverify'   =>    false,
                 'headers'     =>    $headers,
                 'body'        =>    json_encode($data),
                 'cookies'     =>    array()
                 );

            $result =  wp_remote_get($ApiUrl,$args);
                   
     
            
            if(!is_wp_error($result)){
            	$resultResponse = $result['response'];

            	if($resultResponse['code'] != 404){
	                $resultBody = $result['body'];
			

                    if($response==true){

                        $resultResponse = json_decode($resultBody,true); // Returns an Array
                    }
                    else{

                        $resultResponse = json_decode($resultBody); // Returns an object
                    }

                    $this->_results = $resultResponse;
            	}
            	else{
            		$resultResponse =   array('error'=>$resultResponse['code'],'message'=>$resultResponse['message']);
                    //error_log(implode(' : ',$resultResponse));
                    $this->_results = (object) $resultResponse;
		        }
            }
            else {
		
                $apiresponseStr = array();
                foreach($result->errors as $code=>$error):
                    $apiresponseStr[] = implode(' : ',array('code'=>$code,'message'=>$error[0]));
		        endforeach;

            	$resultResponse =  array('error'=>'1','response'=>implode(', ',$apiresponseStr));

                $errStr = implode(' : ',$resultResponse);
                error_log($errStr);
                $this->_results =  (object) $resultResponse;
		
            }
       
        }

        /*
         *@method checkApiStatus will check the api status
         */
        public function checkApiStatus(){
            $this->_ApiUrl ="status";
            $params =array();
            return $this->_results;
        }

        /*
        *  @method RegisterPricePlow method will register the plugin to PricePlow and give the API credentials
        *  @return object
        *  POST method
        */
        public function RegisterPricePlow(){
            $this->_ApiUrl ="client/register/wordpress";
            $params =array('domain'=>$_SERVER['HTTP_HOST']);
            $this->request($params,'POST');
            return $this->_results;
        }


        /*
         *  @method getAllBrands method will pull all the brands from the API
         *   This will manually sort the brands by name since the API does not
         *  @return object
         */

        public function getAllBrands(){

            $this->_ApiUrl ="brands";

            $params =array();
                $this->request($params,'GET',null,array(),true);

            if(!isset($this->_results->error)) {
                usort($this->_results, array($this,'name_compare'));
            }

            return $this->_results;
        }

        /*
         *  @method getAllProducts method will pull products based on the brand
         *   This will manually sort the products by name since the API does not
         *  @param int $brand_id (required)
         *  @return object
         */
        public function getAllProducts($brand_id){

            $this->_ApiUrl ="brands/$brand_id/products";

            $params =array();
            $this->request($params,'GET',null,array(),true);

            if(!isset($this->_results->error)) {
                usort($this->_results, array($this,'name_compare'));
            }
            return $this->_results;

        }


        /*
         * @method getPopularProducts will bring list a random sample of popular products for a brand
         * @param int $brand_id (required)
         * @param int $count (optional)
         * @return object
         */
        public function getPopularProducts($brand_id,$count=''){

            $this->_ApiUrl ="brands/$brand_id/products/popular";

            $params =array();
                $this->request($params);
            return $this->_results;

        }

        /*
         * @method getMostPopularProducts will bring list a random sample of the most popular products.
         * @param int $count (optional)
         * @return object
         */
        public function getMostPopularProducts($count=''){

            $this->_ApiUrl ="products/popular";

            $params =array();
                $this->request($params);
            return $this->_results;

        }

        /*
         * @method getNewestProducts will bring list the newest N available products.
         * @param int $count (optional)
         * @return object
         */
        public function getNewestProducts($count=''){

            $this->_ApiUrl ="products/newest";

            $params =array();
                $this->request($params);
            return $this->_results;

        }

        /*
         * @method getProductStorePrice will bring list all store prices for a product.
         * @param int $product_id (required)
         * @return object
         */
        public function getProductStorePrice($product_id){

            $this->_ApiUrl ="products/$product_id";

            $params =array();
                $this->request($params);
            return $this->_results;
        }

        // ############# Category based api calls ###############
            /*
             *  @method getAllCategories method will pull list of categories.
             *  @return object
             */
        public function getAllCategories(){
            $this->_ApiUrl ="categories";

            $params =array();
            $this->request($params,'GET',null,array(),true);

            if(!isset($this->_results->error)) {
          	  return $this->_results;
            }
            else{
            	return array();
            }

        }

        /*
         * Flatten category list
         *  The categories returned each have its own tree of children
         *  This will flatten them and return a single associative array
         *  with the id as the key, and name, url, depth as items
         */
        public function flatten_categories($nested_categories, $depth=0) {
            $flattened_array = array();
            foreach($nested_categories as $this_category) {
                $flattened_array[$this_category['id']]['name'] = $this_category['name'];
                $flattened_array[$this_category['id']]['depth'] = $depth;
                $flattened_array[$this_category['id']]['url'] = $this_category['url'];
                if(!empty($this_category['children']) && count($this_category['children']) > 0) {
                    // This category has children
                    $recursive_flattened_array = $this->flatten_categories($this_category['children'], $depth+1);
                    // Merge the children in, but do NOT use array_merge as that destroys indexes.
                    //  Simply do an array 'concatenation'
                    $flattened_array = $flattened_array + $recursive_flattened_array;
                }
            }
            return $flattened_array;
        }

            /*
             *  @method getCategoryPopularProducts method will pull list a random sample of popular products for a category.
             *  @param int $category_id (required)
             *  @return object
             *
             */
        public function getCategoryPopularProducts($category_id){
            $this->_ApiUrl ="categories/$category_id/products/popular";

            $params =array();
                $this->request($params);
            return $this->_results;
        }


        //################## Campaign Affiliate Api calls ##################################

        /*
         * @method getAffiliateCampaigns will list an API client's affiliate campaign codes
         * @return object
         */
        public function getAffiliateCampaigns() {

            $this->_ApiUrl ="client/affiliate_campaigns";

            $params =array('api_key'=>$this->_Apikey,'api_secret'=>$this->_ApiSecret);
            $this->request($params);
            
            if(!isset($this->_results->error)) {
          	  return $this->_results;
            }
            else{
            	return array();
            }
        }

        /*
         * @method saveAffiliateCampaigns will save an API client's affiliate campaign codes
         * @return object
         */
        public function saveAffiliateCampaigns($affiliate_campaigns) {
            $this->_ApiUrl ="client/affiliate_campaigns";

            $params =array('api_key'=>$this->_Apikey,'api_secret'=>$this->_ApiSecret);
            $data = array('affiliate_campaigns'=>($affiliate_campaigns));

            $this->request($params, "PUT", $data, $headers=array("Content-Type"=> "application/json"));
            return $this->_results;
        }

        /*
         * @method getDeals will give the priceplow hot deals
         * @return object
         */
	public function getDeals(){
            $this->_ApiUrl ="deals";

            $params =array('api_key'=>$this->_Apikey,'api_secret'=>$this->_ApiSecret);
            $this->request($params);
            return $this->_results;	    
	}
	
    /*
    * an alphabetical name comparison for associative arrays with 'name' key inside
    *  USAGE: usort($array, array('ClassName', 'name_compare'));
    */
    public static function name_compare($a, $b) {
        return strcmp($a['name'], $b['name']);
    }

    } // end of PricePlow API class

} // end of if !if class exists