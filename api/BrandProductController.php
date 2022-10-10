<?php

// include_once WP_PLUGIN_DIR  . '/woocommerce/packages/woocommerce-blocks/src/StoreApi/Schemas/V1/ProductSchema.php';


//ude_once WP_PLUGIN_DIR  . 'woocommerce/packages/woocommerce-blocks/src/StoreApi/Schemas/V1/ProductSchema.php';

class BrandProductController{

 /**
   * @deprecated
   */
  public function getProducts($widget){
    $category =  get_field('category', 'widget_' .$widget) ;
    $posts_per_page =  get_field('posts_per_page', 'widget_' .$widget) ;
    $args = array(
      'posts_per_page'  => $posts_per_page,
    );
    if(!empty($category)) {
     $args['category'] =   $category->slug;
    }
    return $this->getPosts($args);
  }

 
  public function getBlockProducts($attrs){

    // $category =  get_field('category') ;
    // $posts_per_page =  get_field('posts_per_page') ;
    $args = array(
      'posts_per_page'  => $attrs['numberOfPosts'],
      'tax_query' => array(
          array(
              'taxonomy' => 'product_cat',
              'terms' => $attrs['categories'],
              'operator' => 'IN',
          )
      )
    );
    // if(!empty($category)) {
    //  $args['category'] =   $category->slug;
    // }

 

    return $this->getPosts($args);
  }


  public function getPosts($args){
  



    $products_query = wc_get_products($args);
   // return $products_query[0]->get_data();


   $formatters =  new Automattic\WooCommerce\StoreApi\Formatters();
   $ext= new Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema( $formatters );
   $controller = new Automattic\WooCommerce\StoreApi\SchemaController($ext);
   $schema  = new Automattic\WooCommerce\StoreApi\Schemas\V1\ProductSchema( $ext,$controller);

 
    $products = array();
    foreach ( $products_query as $product ) {
      
   

      $products[] = $schema->get_item_response($product);

     // $products[] = $this->get($product,$product);
    }
    return $products;
  }

  public function get( $response, $product) {

        $data = $response->get_data();  

        $formatters =  new Automattic\WooCommerce\StoreApi\Formatters();
        $ext= new Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema( $formatters );
        $controller = new Automattic\WooCommerce\StoreApi\SchemaController($ext);
        $schema  = new Automattic\WooCommerce\StoreApi\Schemas\V1\ProductSchema( $ext,$controller);

       return $schema->get_item_response( $product ) ;








        $data['currency'] = get_woocommerce_currency_symbol();
        $data['permalink'] = $product->get_permalink();
      
     
        $terms = get_the_terms( $data['id'],'product_cat');
        $cats = [];
        foreach ($terms as $term) {
            $cats[] =$term;
        }
        $data['categories'] = $cats;




        $data['price_html'] = $product->get_price_html(); 

          /**
           * Add Colors
           */
        if(taxonomy_exists( 'pa_color' )){
      
          $terms = get_terms( 'pa_color' );
          $colors=[];
          foreach($terms as $term){
            $colors[] = get_term_meta( $term->term_id,'color', true );
          }
        
        }
      
          /**
           * Add Patterns
           */
        
         
        if(taxonomy_exists( 'pa_pattern' )){
          $terms = get_terms( 'pa_pattern' );
          $patterns=[];
          foreach($terms as $term){
            $patterns[] =wp_get_attachment_url( get_term_meta( $term->term_id,'image', true ));
          }
        }
    


       $attrs= [];
          foreach($product->get_attributes() as $key => $attr){     
            $attrs[]  = $attr->get_data();
           
            $data['test2'] = $attr;
        }
        $data['attributes'] =$attrs;

        
        foreach($data['attributes'] as $key => $attr){

       
          $labels =  explode(",",  $product->get_attribute($attr['name']));
        
          //Removing "pa_" from attribute slug and adding a cap to first letter
           $attr['name']=  ucfirst( str_replace('pa_', '',$attr['name']) );
           $data['attributes'][$key]['name'] = $attr['name'];
            foreach($data['attributes'][$key]['options'] as $k => $option){
     
            
              if($attr['name'] === 'Color'){
                  $data['attributes'][$key]['options'][$k] = ['option'=>$labels[$k], 'value'=> $colors[$k],'disable'=>false];
              }elseif($attr['name'] === 'Pattern'){
                  // $data['attributes'][$key]['name'] = 'Color';
                  // $data['attributes'][$key]['type'] = 'pattern';	
                  $data['attributes'][$key]['options'][$k] = ['option'=>$labels[$k], 'value'=> $patterns[$k],'disable'=>false];	
              }else{
                  $data['attributes'][$key]['options'][$k] = ['option'=> $labels[$k], 'value'=> $labels[$k],'disable'=>false];
              } 
            }
      
          }


     
          if( $product->is_type( 'variable' ) ) {
               $variations = $product->get_available_variations();
                 $variations_id = wp_list_pluck( $variations, 'variation_id' );
                $data['variations'] = $variations_id;
          }
 
        
       //	$response =  custom_change_product_response($response)
        if(!empty( $data['variations'])){
          $variations = $data['variations'];
          $variations_res = array();
          $variations_array = array();
          if (!empty($variations) && is_array($variations)) {
            foreach ($variations as $variation) {
              $variation_id = $variation;
              $variation = new WC_Product_Variation($variation_id);

              

              $variations_res['id'] = $variation_id;
              $variations_res['on_sale'] = $variation->is_on_sale();
              $variations_res['regular_price'] =  (string) $variation->get_regular_price();
              $variations_res['sale_price'] = $variation->get_sale_price();
              $variations_res['sku'] = $variation->get_sku();
              $variations_res['quantity'] = $variation->get_stock_quantity();
              if ($variations_res['quantity'] == null) {
                $variations_res['quantity'] = '';
              }
              $variations_res['stock'] = $variation->get_stock_quantity();
      
              $attributes = array();
              // variation attributes
              foreach ( $variation->get_variation_attributes() as $attribute_name => $attribute ) {
                // taxonomy-based attributes are prefixed with `pa_`, otherwise simply `attribute_`
                $attributes[] = array(
                  'name'   => wc_attribute_label( str_replace( 'attribute_', '', $attribute_name ), $variation ),
                  'slug'   => str_replace( 'attribute_', '', wc_attribute_taxonomy_slug( $attribute_name ) ),
                  'option' => $attribute,
                );
      
                $variations_res['variation_attributes'][strtolower( wc_attribute_label( str_replace( 'attribute_', '', $attribute_name ), $variation ))] =  $attribute;
              }
      
              $variations_res['attributes'] = $attributes;
              $variations_array[] = $variations_res;
            }
          }
          $data['variations'] = $variations_array;
        }



        $data['images'] =woo_get_images($product);
        return  $data;
      }
}