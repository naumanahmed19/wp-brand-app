<?php

class BrandWooController{

  public function calculate( $data ){
    $gateways = WC()->payment_gateways->get_available_payment_gateways();
    $order    = new WC_Order();
  
    // Set Billing and Shipping adresses
    foreach( array('billing_', 'shipping_') as $type ) {
        foreach ( $data['shipping'] as $key => $value ) {
            if( $type === 'shipping_' && in_array( $key, array( 'email', 'phone' ) ) )
                continue;
  
            $type_key = $type.$key;
  
            if ( is_callable( array( $order, "set_{$type_key}" ) ) ) {
                $order->{"set_{$type_key}"}( $value );
            }
        }
    }
  
    // Set other details
    $order->set_created_via( 'programatically' );
    $order->set_customer_id( $data['user_id'] );
    $order->set_currency( get_woocommerce_currency() );
    $order->set_prices_include_tax( 'yes' === get_option( 'woocommerce_prices_include_tax' ) );
    $order->set_customer_note( isset( $data['order_comments'] ) ? $data['order_comments'] : '' );
    $order->set_payment_method( isset( $gateways[ $data['payment_method'] ] ) ? $gateways[ $data['payment_method'] ] : $data['payment_method'] );
  
    // Line items
    foreach( $data['line_items'] as $line_item ) {
      //  $args = $line_item['args'];
        $product = wc_get_product( isset($line_item['variation_id']) && $line_item['variation_id'] > 0 ? $line_item['variation_id'] : $line_item['product_id'] );
        $order->add_product( $product, $line_item['quantity'], $line_item );
    }
  
    $calculate_taxes_for = array(
        'country'  => $data['shipping']['country'],
        'state'    => $data['shipping']['state'],
        'postcode' => $data['shipping']['postcode'],
        'city'     => $data['shipping']['city']
    );
  

    //https://stackoverflow.com/questions/48188567/applying-programmatically-a-coupon-to-an-order-in-woocommerce3
    // Coupon items
    if( isset($data['coupon_lines'])){
        foreach( $data['coupon_lines'] as $coupon_item ) {
            $order->apply_coupon(sanitize_title($coupon_item['code']));
        }
    }
  
    // Fee items
    if( isset($data['fee_items'])){
        foreach( $data['fee_items'] as $fee_item ) {
            $item = new WC_Order_Item_Fee();
  
            $item->set_name( $fee_item['name'] );
            $item->set_total( $fee_item['total'] );
            $tax_class = isset($fee_item['tax_class']) && $fee_item['tax_class'] != 0 ? $fee_item['tax_class'] : 0;
            $item->set_tax_class( $tax_class ); // O if not taxable
  
            $item->calculate_taxes($calculate_taxes_for);
  
           //$item->save();
            $order->add_item( $item );
        }
    }
  
    if( isset($data['shipping_lines'])){
      foreach( $data['shipping_lines'] as $s ) {
          $item = new WC_Order_Item_Shipping();
  
          $item->set_method_title( $s['method_title'] );
          $item->set_method_id( $s['method_id'] );
         $item->set_total( $s['total'] );
          $tax_class = isset($s['tax_class']) && $s['tax_class'] != 0 ? $s['tax_class'] : 0;
       //   $item->set_tax_class( $tax_class ); // O if not taxable
  
          // $item->calculate_taxes($calculate_taxes_for);
  
        //  $item->save();
          $order->add_item( $item );
      }
  }
  
    // Set calculated totals
    $order->calculate_totals();
   
  
    $order =  json_decode($order);
    $order->date_created = '';
    $order->line_items =[];

    wp_delete_post($order->id,true);
    return $order;
  }
}

