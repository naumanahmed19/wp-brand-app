<?php


add_action('rest_api_init', function() {



    function isUserLoggedIn(){
        $user = wp_get_current_user();
        return $user->exists();
    }


    function getLineItems($order){
        $items=[];
        foreach ($order->get_items() as $item_id => $item ) {
            $lineItem =  $item->get_data();
            $lineItem['image']=   wp_get_attachment_url(get_post_thumbnail_id( $lineItem['product_id']));
            $items[] =  $lineItem;

        }
        return $items;
    }

    // function getOrderProducts($items){
    //     $products = [];
    //     $ctrl = new BrandProductController();
    //     foreach ( $items as $key => $item) {
    //       $product = wc_get_product($item['product_id']);
    //       $products[] = $ctrl->get($product,$product); 
    //     }
    //     return  $products;
    // }

    function getCustomerOrder($orderId){
        $data = [];
        $userid = get_current_user_id();
        $order = wc_get_order($orderId);
            if(!$order || $order->user_id  !== $userid  )  return new WP_Error(404, 'resource not found', '');
        
    
        $data =  $order->get_data();

        $lineItems = getLineItems($order);
        $data['line_items'] = getLineItems($order);
        // $data['line_items']['image'] = 'xxx';
        // wp_get_attachment_url(get_post_thumbnail_id( $data['line_items']['product_id'])); 
        //$data['products'] = getOrderProducts($data['line_items'])
        $data['products'] = [];

        return $data;
    }

  register_rest_route('woofee', 'orders', [
    'methods' => WP_REST_Server::READABLE,
    'permission_callback' => "isUserLoggedIn",
    'callback' => function ( $request ) use ( $route  ) {
         $orders = wc_get_orders(array(
        'customer_id' => get_current_user_id(),
        )); 

        $response = [];
        if(!$orders)  return new WP_Error(500, 'request cannot be completed', '');
        foreach($orders as $order) :
            $order =  $order->get_data();
            $response[] =  getCustomerOrder($order['id']);
        endforeach;
      return $response;
    }
  ]);

  register_rest_route('woofee', 'orders/(?P<id>\d+)', [
    'methods' => WP_REST_Server::READABLE,
    'permission_callback' => "isUserLoggedIn",
    'callback' => function ( $request ) use ( $route  ) {
      return getCustomerOrder($request['id']);
    }
  ]);

  register_rest_route('woofee', 'checkout-customer', [
    'methods' => 'POST',
    'permission_callback' => "isUserLoggedIn",
    'callback' => function ( $request ) use ( $route  ) {
      //Get user
      $userid = get_current_user_id();
      //check if order exist & not already assinged to a user
      $order = wc_get_order($request['orderId'] );
      if(!$order || !empty( $order->user_id))  return new WP_Error(500, 'request cannot be completed', '');

      //update customer order
      update_post_meta($request['orderId'], '_customer_user', $userid);
      $order_data = $order->get_data();
      return  $order_data;
    }
  ]);



  
}, 100, 2);