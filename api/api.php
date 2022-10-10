<?php

include ( __DIR__ . '/helpers.php');
include ( __DIR__ . '/BrandProductController.php');
include ( __DIR__ . '/BrandHomeController.php');
include ( __DIR__ . '/BrandCategoriesController.php');
include ( __DIR__ . '/BrandFilterController.php');
include ( __DIR__ . '/BrandUserController.php');
include ( __DIR__ . '/BrandWooController.php');
include ( __DIR__ . '/BrandPostsController.php');


include_once WC_ABSPATH. '/packages/woocommerce-blocks/src/StoreApi/Utilities/CartController.php';

//require_once ABSPATH . '/wp-content/plugins/simple-jwt-login/src/modules/SimpleJWTLoginService.php';





  


function brand_api_get_home($request){
    $response = new BrandHomeController();
    return  $response->get();
}
function brand_api_get_categories($post_type){
    $response = new BrandCategoriesController();
    return  $response->get();
}
function brand_api_get_filters($post_type){
    $response = new BrandFilterController();
    return  $response->get();
}
function brand_api_get_posts() {
	$ctrl = new BrandPostsController();
	$posts = brand_api_get('post'); //helper method
  return  $ctrl->data($posts);
}



add_action('rest_api_init', function() {

	$routes = ['home','posts','categories','filters'];
	foreach($routes as $route){
		register_rest_route('brand', $route, [
			'methods' => 'GET',
			'callback' => 'brand_api_get_'.$route,
      'permission_callback' => '__return_true',
		]);
	}


//Routes

  register_rest_route( 'brand', 'page', array(
    'methods' => 'GET',
    'permission_callback' => '__return_true',
    'callback' => function ( $request ) use ( $route  ) {
      $response = new BrandHomeController();
      return  $response->getBLocksByPostId();
    },
  ) );




  









	register_rest_route( 'brand', 'block-products', array(
		'methods' => 'GET',
    'permission_callback' => '__return_true',
		'callback' => function ( $request ) use ( $route  ) {

      $ctrl = new BrandProductController();
			return $ctrl->getBlockProducts($request );
    },
   // 'permission_callback' => true
		// 'permission_callback' => function($request){	  
		// 	return is_user_logged_in();
		//   }
		
  ) );




	register_rest_route( 'brand', 'user/update', array(
		'methods' => 'POST',
    'permission_callback' => '__return_true',
		'callback' => function ( $request ) use ( $route  ) {


      // var_dump($sm->getUserIdFromJWT('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2MDkzNDk2MTQsImV4cCI6MTYyNzM0OTYxNCwiZW1haWwiOiJuYXVtYW5haG1lZDE5QGdtYWlsLmNvbSIsImlkIjoxLCJzaXRlIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDAiLCJ1c2VybmFtZSI6ImFkbWluIiwidXNlciI6eyJpZCI6MSwiZW1haWwiOiJuYXVtYW5haG1lZDE5QGdtYWlsLmNvbSIsImZpcnN0TmFtZSI6Ik5hdWFtbiIsImxhc3ROYW1lIjoiQWhtYWQiLCJhdmF0YXIiOiJodHRwOlwvXC8yLmdyYXZhdGFyLmNvbVwvYXZhdGFyXC8_cz05NiZkPW1tJnI9ZyJ9fQ.srwf7hZSCkJPxn46AELRbLdXG-t40gQN0uo22MzZeO0'));

      

      // global $current_user;
      // return $current_user;
			$userController = new BrandUserController();
			return $userController->update($_REQUEST);
    },
   // 'permission_callback' => true
		// 'permission_callback' => function($request){	  
		// 	return is_user_logged_in();
		//   }
		
  ) );

  /**
   * 
   * Brand Route to caclculate price on backend 
   */    
  
  register_rest_route( 'brand', 'user/avatar', array(
		'methods' => 'POST',
    'permission_callback' => '__return_true',
    'callback' => function ( $request ) use ( $route  ) {
			//$woo = new brand_user_avatar_upload();
			return brand_user_avatar_upload($request);
    }
	) );


}, 100, 2);


 // $response = new WP_REST_Response($data, 200);
 add_action( 'simple_jwt_login_login_hook', function($user){



	return $user;

}, 10, 2);


add_action( 'simple_jwt_login_jwt_payload_auth', function($payload, $request){

	$userController = new BrandUserController();
  global $current_user;
  $payload['user'] =	$userController->get($payload['id']);
	return $payload;

}, 10, 2);



/**
 * V3 does not basc account details
 * so we are modify rest query
 *
 */
add_filter( 'woocommerce_rest_payment_gateway_object_query', 'brand_rest_prepare_order_object', 10, 3 );
function brand_rest_prepare_order_object( $response, $object, $request ) {
  // Get the value
  $bacs_info = get_option( 'woocommerce_bacs_accounts');

  $response->data['bacs_info'] = $bacs_info;

  return $response;
}


/**
 * V3 does not support muliple attributes
 * so we are modify rest products query
 *
 */
add_action( 'woocommerce_rest_product_object_query', 'brand_rest_product_object_query' );
function brand_rest_product_object_query($args){ 
    $filters  =!empty($_REQUEST['filter'])? $_REQUEST['filter'] :null;
    if ( ! empty( $filters ) ) {
    foreach ( $filters as $filter_key => $filter_value ) {
      if ( $filter_key === 'min_price' || $filter_key === 'max_price' ) {
        continue;
      }

      $args['tax_query'][] = [
        'taxonomy' => $filter_key,
        'field'    => 'term_id',
        'terms'    => \explode( ',', $filter_value ),
      ];
    }
  }
  return $args;
}

/**
 * Woo Change Rest Porduct Repsponse 
 * and variations 
 * v3 does not support variations directly in product
 * 
 */
add_filter( 'woocommerce_rest_prepare_product_object', 'brand_add_custom_data_to_product', 10, 3 );
function brand_add_custom_data_to_product( $response, $post, $request ) {

  $data = $response->get_data();  

  
  $data['currency'] = get_woocommerce_currency_symbol();

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

    foreach($response->data['attributes'] as $key => $attr){

      foreach($data['attributes'][$key]['options'] as $k => $option){
        if($attr['name'] === 'Color'){
            $data['attributes'][$key]['options'][$k] = ['option'=>$option , 'value'=> $colors[$k],'disable'=>false];
        }elseif($attr['name'] === 'Pattern'){
            // $data['attributes'][$key]['name'] = 'Color';
            // $data['attributes'][$key]['type'] = 'pattern';	
            $data['attributes'][$key]['options'][$k] = ['option'=>$option , 'value'=> $patterns[$k],'disable'=>false];	
        }else{
            $data['attributes'][$key]['options'][$k] = ['option'=>$option , 'value'=> $option,'disable'=>false];
        } 
      }

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
  return  $data;
}






add_filter( 'woocommerce_rest_prepare_shop_coupon_object', 'brand_add_custom_data_to_coupon', 10, 3 );
function brand_add_custom_data_to_coupon( $response, $post, $request ) {
  $data = $response->get_data();  
  $coupon = new WC_Coupon(  $data['code'] );
  $data['valid'] = $coupon->is_valid();
  return $data;

}

// add_filter( 'woocommerce_rest_prepare_shop_order_object', 'brand_add_custom_data_to_order', 10, 3 );
// function brand_add_custom_data_to_order( $response, $post, $request ) {

  
// $data = $response->get_data();  

//  $products = [];
//  $ctrl = new BrandProductController();

//   foreach ( $response['line_items'] as $item) {
//     // Get the accessible array of product properties:
//     $product = wc_get_product($item['product_id']);
//     $products[] = $ctrl->get($product,$product); 
//   }

//   $response['products'] =  $products;

//   $bacs_info = get_option( 'woocommerce_bacs_accounts');

//   $response['bacs_info'] = $bacs_info;

//   return $response;

//}
/**
 * Sets the extension and mime type for .webp files.
 *
 * @param array  $wp_check_filetype_and_ext File data array containing 'ext', 'type', and
 *                                          'proper_filename' keys.
 * @param string $file                      Full path to the file.
 * @param string $filename                  The name of the file (may differ from $file due to
 *                                          $file being in a tmp directory).
 * @param array  $mimes                     Key is the file extension with value as the mime type.
 */
add_filter( 'wp_check_filetype_and_ext', 'wpse_file_and_ext_webp', 10, 4 );
function wpse_file_and_ext_webp( $types, $file, $filename, $mimes ) {
    if ( false !== strpos( $filename, '.webp' ) ) {
        $types['ext'] = 'webp';
        $types['type'] = 'image/webp';
    }

    return $types;
}

/**
 * Adds webp filetype to allowed mimes
 * 
 * @see https://codex.wordpress.org/Plugin_API/Filter_Reference/upload_mimes
 * 
 * @param array $mimes Mime types keyed by the file extension regex corresponding to
 *                     those types. 'swf' and 'exe' removed from full list. 'htm|html' also
 *                     removed depending on '$user' capabilities.
 *
 * @return array
 */
add_filter( 'upload_mimes', 'wpse_mime_types_webp' );
function wpse_mime_types_webp( $mimes ) {
    $mimes['webp'] = 'image/webp';

  return $mimes;
}



function woo_get_images( $product ) {
  $images         = array();
  $attachment_ids = array();

  // Add featured image.
  if ( $product->get_image_id() ) {
    $attachment_ids[] = $product->get_image_id();
  }

  // Add gallery images.
  $attachment_ids = array_merge( $attachment_ids, $product->get_gallery_image_ids() );

  // Build image data.
  foreach ( $attachment_ids as $attachment_id ) {
    $attachment_post = get_post( $attachment_id );
    if ( is_null( $attachment_post ) ) {
      continue;
    }

    $attachment = wp_get_attachment_image_src( $attachment_id, 'full' );
    if ( ! is_array( $attachment ) ) {
      continue;
    }

    $images[] = array(
      'id'                => (int) $attachment_id,
      'date_created'      => wc_rest_prepare_date_response( $attachment_post->post_date, false ),
      'date_created_gmt'  => wc_rest_prepare_date_response( strtotime( $attachment_post->post_date_gmt ) ),
      'date_modified'     => wc_rest_prepare_date_response( $attachment_post->post_modified, false ),
      'date_modified_gmt' => wc_rest_prepare_date_response( strtotime( $attachment_post->post_modified_gmt ) ),
      'src'               => current( $attachment ),
      'name'              => get_the_title( $attachment_id ),
      'alt'               => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
    );
  }

  return $images;
}



add_action( 'rest_insert_post', 'wpse220930_rest_insert_post', 1, 3 );
function wpse220930_rest_insert_post( $post, $request, $update = true )
{
    if ( ! empty( $request['tags'] ) )
        wp_set_object_terms( $post->ID, $request['tags'], 'post_tag', $update );

    if ( ! empty( $request['categories'] ) )
        wp_set_object_terms( $post->ID, $request['categories'], 'category', $update );
}



/**
 * Allow Comments
 */

add_filter( 'rest_allow_anonymous_comments', function ( $allow_anonymous, $request ) {
  // ... custom logic here ...
  return true; // or false to prohibit anonymous comments via post
}, 10, 2 ); 


add_filter(
  'jwt_auth_valid_credential_response',
  function ( $response, $user ) {

    $userController = new BrandUserController();
    $payload = array(
     'success' => true,
     "statusCode"=> 200,
     'token'    => $response['data']['token'],
     "message"=> "Credential is valid",
   );
   $payload['user'] =	$userController->get($user);
   return  $payload;

    $userController = new BrandUserController();
      // Modify the response here.
      return 	$userController->get($user);
  },
  10,
  2
);
/**
 * User login payload update
 * 
 */
function brand_user_response($response, $user, $token, $payload)
{

   	$userController = new BrandUserController();
     $payload = array(
      'success' => true,
      'token'    => $token,
    );
    $payload['user'] =	$userController->get($user);
    return  $payload;
}
add_filter('jwt_auth_valid_token_response ', 'brand_user_response',   10,4);



# JWT Authentication for WP REST API

function brand_jwt_auth_token_before_dispatch ($data, $user)
{
	$userController = new BrandUserController();
    $data['user'] =	$userController->get($user);


     return $data;
   	$userController = new BrandUserController();
     $payload = array(
      'success' => true,
      'token'    => $token,
    );
    $payload['user'] =	$userController->get($user);
    return  $payload;
}
add_filter('jwt_auth_token_before_dispatch', 'brand_jwt_auth_token_before_dispatch', 10, 2);
/**
 * User Profile update
 * 
 */
function brand_profile_response($data, $user)
{
    $user = get_user_by( 'id',  $data['id'] );
   	$userController = new BrandUserController();
    return $userController->get($user);
}
function brand_profile_response_fields() {
  $userController = new BrandUserController();
  register_rest_field(
      'user', 
      'profile',
      array(
          'get_callback'    => 	'brand_profile_response',
          'update_callback' => null,
          'schema'          => null,
      )
  );
}
add_action( 'rest_api_init', 'brand_profile_response_fields' );




/**
 *  Fix conflict jwt plugin with woo basic authorization
 * https://github.com/Tmeister/wp-api-jwt-auth/issues/48
 */
// add_filter('rest_pre_dispatch' , array( $this , 'rest_pre_dispatch_overwrite'), 11);
// function rest_pre_dispatch_overwrite($request) {
// 	if (is_wp_error($request)) {
// 		if($request->get_error_data('jwt_auth_bad_auth_header')) {
// 			return NULL;
// 		}
// 	}
//   return $request;
// }


/**
 *  change Avatar
 * 
 */
 function brand_user_avatar_upload( $request ){
	if ( ! function_exists( 'wp_handle_upload' ) ) {
	    require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}

  $user_id = $request['user'];
	$uploadedfile = $_FILES['avatar'];
	$movefile = wp_handle_upload( $uploadedfile, array('test_form' => FALSE) );
	if ( $movefile && !isset( $movefile['error'] ) ) {
	  update_user_meta($user_id, 'avatar', $movefile['url']) 
	  or add_user_meta($user_id, 'avatar', $movefile['url']);
	} else {
	    return  $movefile['error'];
	}
  return 'image uploaded';
}

// add_filter ('get_avatar', function($avatar_html, $id_or_email, $size, $default, $alt) {
// 	$avatar = get_user_meta($id_or_email,'avatar',true);
// 	if( $avatar ) {
// 		return '<img src="'.$avatar.'" width="96" height="96" alt="Avatar" class="avatar avatar-96 wp-user-avatar wp-user-avatar-96 photo avatar-default" />';
// 	} else {
// 		return $avatar_html;
// 	}
// }, 10, 5);





// /**
//  * Register Blocks
//  * @link https://www.billerickson.net/building-gutenberg-block-acf/#register-block
//  *
//  */
// function be_register_blocks() {
	
// 	if( ! function_exists( 'acf_register_block_type' ) )
// 		return;

// 	acf_register_block_type( array(
// 		'name'			=> 'team-member',
// 		'title'			=> __( 'Team Member', 'clientname' ),
// 		'render_template'	=> dirname( __file__ ) . '/partials/block-team-member.php',
// 		'category'		=> 'formatting',
// 		'icon'			=> 'admin-users',
// 		'mode'			=> 'auto',
// 		'keywords'		=> array( 'profile', 'user', 'author' ),
//     'example'  => array(
//       'attributes' => array(
//           'mode' => 'preview',
//           'data' => array(
//             'preview_image_help' =>'https://placeholder.com/.'
//           )
//       )
//   )
// 	));

// }
// add_action('acf/init', 'be_register_blocks' );

