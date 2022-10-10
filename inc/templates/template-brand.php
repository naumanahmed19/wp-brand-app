<?php
/**
 * Template Name: Brand App
 * Template Post Type: post, page
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since 1.0
 */

wp_head(); ?>

<div class="brand-woo-checkout">
  <?php echo do_shortcode( '[woocommerce_checkout]'); ?>
</div>

<script type="text/javascript">
		(function () {
			var c = document.body.className;
			c = c.replace(/woocommerce-no-js/, 'woocommerce-js');
			document.body.className = c;
		})();
	</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
	<style>.wp-container-1 > .alignleft { float: left; margin-inline-start: 0; margin-inline-end: 2em; }.wp-container-1 > .alignright { float: right; margin-inline-start: 2em; margin-inline-end: 0; }.wp-container-1 > .aligncenter { margin-left: auto !important; margin-right: auto !important; }</style>
<style>.wp-container-2 > .alignleft { float: left; margin-inline-start: 0; margin-inline-end: 2em; }.wp-container-2 > .alignright { float: right; margin-inline-start: 2em; margin-inline-end: 0; }.wp-container-2 > .aligncenter { margin-left: auto !important; margin-right: auto !important; }</style>
<style>.wp-container-3 > .alignleft { float: left; margin-inline-start: 0; margin-inline-end: 2em; }.wp-container-3 > .alignright { float: right; margin-inline-start: 2em; margin-inline-end: 0; }.wp-container-3 > .aligncenter { margin-left: auto !important; margin-right: auto !important; }</style>
<style>.wp-container-4 > .alignleft { float: left; margin-inline-start: 0; margin-inline-end: 2em; }.wp-container-4 > .alignright { float: right; margin-inline-start: 2em; margin-inline-end: 0; }.wp-container-4 > .aligncenter { margin-left: auto !important; margin-right: auto !important; }</style>
<style>


  body{
    background: #f7f8fa;
    font-family: 'Poppins', sans-serif !important;
    padding:5px;

  }
  h1,h2,h3,h4,h5,p{
    font-family: 'Poppins', sans-serif !important;
  }
  /* .woocommerce-error a:hover, .woocommerce-info a:hover, .woocommerce-message a:hover {
    color: #000;

  } */
  h3{
    font-size: 3rem;
  }
  label{
    font-size: 1.7rem;
    margin-top:10px;
  }
  .woocommerce-checkout-payment,input[type=checkbox] + label, input[type=radio] + label{
    font-size: 2rem;
    margin-top:10px;
  }
   .brand-woo-checkout{
    /* padding: 50px;
    margin: 0 auto; */
   }
  .woocommerce-form-coupon #coupon_code, .brand-woo-checkout .woocommerce .col2-set .col-1, .woocommerce .col2-set .col-2, .woocommerce-page .col2-set .col-1,
   .woocommerce-page .col2-set .col-2{
    float: none;
    width: 100%;
  }
  .woocommerce form .form-row .input-text, .woocommerce-page form .form-row .input-text, input[type=text], input[type=email], input[type=url], input[type=password], input[type=search], input[type=number], input[type=tel], input[type=date], input[type=month], input[type=week], input[type=time], input[type=datetime], input[type=datetime-local], input[type=color], .site textarea{
    font-size: 1.8em;
    background-color: #fff;
    padding: .75em 0;
    text-indent: 16px;
    border-radius: 2px;
    border: 1px solid #000;
    width: 100%;
    line-height: 1.375;
    font-family: inherit;
    margin: 0;
    box-sizing: border-box;
    margin-bottom:10px;
    height: 90px;
 
    min-height: 0;
    font-family: 'Poppins', sans-serif;
    color: #2b2d2f;
  }
   textarea{
   height: 6em !important;
  font-size: 1.5em !important;
  }

  .woocommerce-checkout .select2-container .select2-selection{
    border: 0.5px solid #000;
    border-radius: 2px;
    margin-bottom:10px;
    font-size: 1.8em;
    padding: .75em 0;
    height: 90px;
    line-height: 1.375;
    font-family: 'Poppins', sans-serif;
  }

  .woocommerce #payment #place_order , button {
    background: #000 !important;
    color:#fff !important;
    border: none;
    border-radius: 2px;
    margin-bottom:10px;
    font-size: 1.8em;
    padding: 0 1.75em !important;
    height: 90px;
    line-height: 1.375;
    font-family: 'Poppins', sans-serif;
  
  }
  .woocommerce-error a, .woocommerce-info a, .woocommerce-message a{
    color: #fff; ;
  }

  .woocommerce-info{
    border-top-color: #000;
    font-size: 2rem;

  }
  .woocommerce-error, .woocommerce-info, .woocommerce-message{
    background: #000;
    color: #fff;
  }
  .woocommerce-additional-fields {
    margin-top: 40px;
}

.woocommerce table{
  background: #fff;
    border: none;
    border-radius: 2px;
    margin-bottom:10px;
    font-family: 'Poppins', sans-serif !important;

}

h3#order_review_heading {
    padding: 30px 0;
}
.woocommerce table.shop_table td, .woocommerce table.shop_table th, .woocommerce-page table.shop_table td, .woocommerce-page table.shop_table th{
  font-family: 'Poppins', sans-serif !important;
  border: none;
  text-align:left;
  padding: 30px;
  font-size: 1.8em;
  
}
</style>
<?php
wp_footer();