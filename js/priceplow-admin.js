jQuery(function($) {
    // toggle password jquery simple plugin
    $.toggleShowPassword = function (options) {
        var settings = $.extend({
            field: "#password",
            control: "#toggle_show_password"
        }, options);

        var control = $(settings.control);
        var field = $(settings.field)

        control.bind('click', function () {
            if (control.is(':checked')) {
                field.attr('type', 'text');
            } else {
                field.attr('type', 'password');
            }
        })
    };


    $.toggleShowPassword({
        field: '#priceplow_apiSecret_key',
        control: '#priceplow_show_apiSecret'
    });

    
    $(document).on('change','.priceplow_feature_type', function() {
        var $this = $(this);
        var feature_type = $this.val();
        var load_container;
        var widget_fields_attr;
        var widget_field_name;
        var widget_field_id;
        var widget_field_name_1;
        var widget_field_id_1;
        
        var num_products_ele;
        var product_advanced_options; 
    
        
        if($this.parents().hasClass('widget')){
              load_container = $this.closest('.widget').find(".priceplow_load_container");
              widget_fields_attr =  $this.closest('.widget').find('.priceplow_'+feature_type+'_ajax_fields').val();
              
              num_products_ele = $this.closest('.widget').find(".priceplow_num_products");
              product_advanced_options = $this.closest('.widget').find(".priceplow-advanced-options-toggle");
         }
         else{
              load_container = $(".priceplow_load_container");
              num_products_ele =$(".priceplow_num_products");
              product_advanced_options = $(".priceplow-advanced-options-toggle");
         }
         
        if(feature_type == "product"){
            num_products_ele.closest('ul').hide();
            product_advanced_options.closest('ul').show();
        }
        else{
            num_products_ele.closest('ul').show();
            product_advanced_options.closest('ul').hide();
        }
        
        if(typeof(widget_fields_attr) != "undefined"){
        	var widget_fields_attr_parts = widget_fields_attr.split('::::');
        	widget_field_name = widget_fields_attr_parts[0];
        	widget_field_id = widget_fields_attr_parts[1];
        	widget_field_name_1 = widget_fields_attr_parts[2];
        	widget_field_id_1 = widget_fields_attr_parts[3];
        }    
            
        load_container.html('<span class="spinner"></span>');
        $('.spinner',load_container).show();


        $.get(ajaxurl,{'action':'priceplow_getfeaturetype_action','feature_type':feature_type,
              'widget_field_name':widget_field_name,'widget_field_id':widget_field_id,
              'widget_field_name_1':widget_field_name_1,'widget_field_id_1':widget_field_id_1},function(response){
            load_container.html(response);
        });

    });
    
    $(document).on('click','.priceplow-advanced-options-toggle', function(e) {
    	e.preventDefault();
    	 var $this = $(this);
    	
    	 if($this.hasClass('show')){
    		 $this.removeClass('show').addClass('hide').text("Hide Advanced Options");
    		 $this.next(".priceplow-advanced-options-wrap").slideDown();
    	 }
    	 else{
    		 $this.removeClass('hide').addClass('show').text("Show Advanced Options");
    		 $this.next(".priceplow-advanced-options-wrap").slideUp();
    	 }
    	
    	
    });
    

      $(document).on('change','.priceplow_brand', function() {
          var $this = $(this);
          var brand = $this.val();
          var load_container;
            if($this.parents().hasClass('widget')){
                  load_container = $this.closest('.widget').find(".priceplow_load_container");
             }
             else{
                  load_container = $(".priceplow_load_container");
             }

          // TODO - Do not show spinner / do any work if we are in Featured BRAND mode.
          //  (todo cont) We only need to get product data if we are in Featured PRODUCT mode.
          //  (todo cont) Need a way to check which feature type is selected...
          load_container.append('<span class="spinner"></span>');
          $('.spinner',load_container).show();

          $.get(ajaxurl,{'action':'priceplow_getproduct_action','brand':brand},function(response){
                  $('.spinner',load_container).hide();
                  $('.priceplow_brandproduct_wrap').show();
                  $('.priceplow_product_id').html(response);

          });
      });


      $("#priceplow_default_featured_brand").change(function() {
          var $this = $(this);
          var brand = $this.val();
          var load_container = $('#priceplow_default_featured_product option:first-child');
          $.get(ajaxurl,{'action':'priceplow_getproduct_action','brand':brand},function(response){

                  load_container.siblings().remove();
                  load_container.after(response);

          });
      });
      
      $("#priceplow_buyitnow_brand").change(function() {
          var $this = $(this);
          var brand = $this.val();
          var load_container = $('#priceplow_buyitnow_product option:first-child');
          var product_dropdown = $('#priceplow_buyitnow_product');
          product_dropdown.after('<span class="spinner"></span>');
          
          product_dropdown.next('.spinner').show();
          
          $.get(ajaxurl,{'action':'priceplow_getproduct_action','brand':brand},function(response){

            product_dropdown.next('.spinner').remove();
                  load_container.siblings().remove();
                  load_container.after(response);

          });
      });
      
      $("#priceplow_buyitnow_product").change(function() {
        var $this = $(this);
        var product = $this.val();
        var load_container = $('#buyitnow-container');
        load_container.html('<span class="spinner"></span>');
        load_container.children('.spinner').show();
        
        $.get(ajaxurl,{'action':'priceplow_getbuyitnow_action','buyitnow_product':product},function(response){
                load_container.children('.spinner').remove();
                
                load_container.html(response);
                
        });
        
      });
});