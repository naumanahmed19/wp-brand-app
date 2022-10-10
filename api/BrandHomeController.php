<?php
class BrandHomeController{


    private $tabs = [];

    public function get(){
    
      $blocks = [];
      $data = [];

      if(function_exists('get_xapp_blocks')){
        
        // get_xapp_blocks requires name of your app which is a post title
        $blocks = get_xapp_blocks();
        $data['data']=  $this->getInnerBlocks($blocks,null)[0];
        $data['settings'] =  $this->getSettings($blocks);
        
        $json = '{
          "type": "text",
          "args": {
            "text": "Bank Example"
          }
        }';

        $data['jsonData'] = json_decode($json);
      }
        return  $data;
    }


    public function getBLocksByPostId(){
      
        $blocks = get_xapp_blocks();
        return $this->getInnerBlocks($blocks,null);
        
    }

    /**
     * 
     * brand/root
     * ___
     * 
     */


    /**
     * Get categories:
     * Issue: Acf taxonomy does not return product category image so we are mapping it here
     */
    public function getCategories($selectedCats, $taxonomy='product_cat'){
        $categories = [];


        // $newArray = array_map( create_function('$value', 'return (int)$value;'),
        // $selectedCats);

        $cats = get_terms( array(
          'taxonomy' =>  $taxonomy,
          'hide_empty' => false,
          'include' =>   $selectedCats
        ));
        foreach ($cats as $key => $cat ) {
          $thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true ); 
          $cat_thumb_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
          $image = wp_get_attachment_url( $cat_thumb_id ); 
          $cat->image = $image ? $image : null;
          $categories[] = $cat;
        }

        

      return $categories;
    }
    

 

    public function getTabs($tabs){
      $sections = [];

      foreach ($tabs as $key => $tab ) {
        //Move all tab contents to one lever and filter with tabId on frontend
        $args = ['index'=> $tab['attrs']['tabScreenIndex']];
        $tabInnerBlocks = $this->getInnerBlocks($tab['innerBlocks'],$args);
        foreach ($tabInnerBlocks as $key => $section) {
              $sections[] = $section ;
        }  
      }
      return $sections;
    }


    public function getSlides($slides){
      $allSlides = [];
      foreach ($slides as $key => $slide ) {
        $allSlides[$key]['mediaUrl'] = $slide['mediaUrl'];
        if($slide['category']){
          $allSlides[$key]['category']  = 
          
          get_term( $slide['category'], 'product_cat');
        }
      }

      return $allSlides;
    }

    
    public function getExpandableBlock($attr){
      $response = [];
      foreach ($attr['categories'] as $key => $item ) {
         $item  [ 'categories'] =  $this->getCategories($item['categories']);
         $response[] = $item;
      }
      return $response;
    }


    public function getCategoriesRow($data){


      $list = [];
      for($i=0 ; $i<= $data['category_list']; $i++)
        if(isset($data["category_list_${i}_categories"])){
           $list[$i]['name'] =  $data["category_list_${i}_title"];
          $list[$i]['categories']  =  $this->getCategories( $data["category_list_${i}_categories"]);
        }

      return $list;
    }



    public function getSectionSettings($wId){
      $sectionSettings = [];
      $sectionSettings ['radius'] = get_field('radius', $wId);
      $sectionSettings ['padding'] = get_field('padding', $wId);
      $sectionSettings ['color'] = get_field('color', $wId);
      $sectionSettings ['margin'] = get_field('margin', $wId);
      return $sectionSettings;
    }
    



/**
 * Find tabs
 * issue: local varibale tabs return empty on recursion
 * fix: update global class variabl tabs
 * 
 * Take first tabs only: if tabs array is not empty
 */
public function findTabs($blocks){
  $tabs=[];
  foreach( $blocks as $key => $block){
    if(empty($this->tabs)){
      if($block['blockName'] == 'b/tabs') {
        $tabs = $block['innerBlocks'];
        $this->tabs = $block;
      }
      $this->findTabs($block['innerBlocks']);
    }
    break;
   }
  return $tabs;
}      

//TODO
//Add template to plugin
//add sidebar locations
public function getScreenBlocks($blocks ){

  $sections = [];
  $rootTabs =  $blocks;

  foreach( $rootTabs as $screen => $tab){
      $item['attrs'] = !empty( $tab['attrs']) ? $tab['attrs'] :  null;
      $item['innerBlocks'] =  $this->getInnerBlocks($tab['innerBlocks'],$args);
      $sections[] = $item;
}

  return $sections;
}



public function getTabBlocks($blocks ){

  $sections = [];
  $rootTabs =  $blocks;

  foreach( $rootTabs as $screen => $tab){
   // if(!empty($tab['innerBlocks'])){ //if a root tab has blocks
  // $post = get_post($postId); 
      //$item['filter'] =$filter;
    //  $screen = $tab['attrs']['tabId'] ;
      // $args = ['screen'=>$screen];
      // $item['blockName']= $tab['blockName'];
      $item['attrs'] = !empty( $tab['attrs']) ? $tab['attrs'] :  null;
      $item['innerBlocks'] =  $this->getInnerBlocks($tab['innerBlocks'],null);

      $sections[] = $item;
      $filter = null;
 // }
}

  return $sections;

}

public function getBlocks($blocks ){
    // return return $this->getInnerBlocks($blocks,null);
   
   
    $sections = [];
    return $blocks;
    $rootTabs =  $blocks[0]['innerBlocks'];

    if(empty($rootTabs)) return $sections;
    
    $item['attrs'] = !empty( $block['attrs']) ? $block['attrs'] :  null;

    foreach( $rootTabs as $screen => $tab){
  //    if(!empty($tab['innerBlocks'])){ //if a root tab has blocks
    // $post = get_post($postId); 
        //$item['filter'] =$filter;
        $screen = $tab['attrs']['tabId'] ;
        $args = ['screen'=>$screen];
        $item['blockName']= $tab['blockName'];
        $item['attrs'] = !empty( $tab['attrs']) ? $tab['attrs'] :  null;
        $item['innerBlocks'] =  $this->getInnerBlocks($tab['innerBlocks'],$args);

        $sections[] = $item;
      $filter = null;
    //}
  }
  
    return $sections;

}


public function getInnerBlocks($blocks,$args){
  $sections = [];

  foreach( $blocks as $key => $block){

    $item = [];
    $name = $block['blockName'];

    if(isset($args['index'])) $item['index'] =   $args['index'];
    if(!empty($args['filter'])) $item['filter'] = $args['filter'];
    if(!empty($args['screen'])) $item['screen'] = $args['screen'];
    //  $item['settings'] = !empty($args['settings']) ? $args['settings']  : null;

    if(!empty($block['attrs']['title'])) $item['title'] = $block['attrs']['title'];
    $item['blockName']=$block['blockName'];

   /// $item['attrs']= $block['attrs'];
   
   
    //Important 
    $item['attrs'] = !empty( $block['attrs']) ? $block['attrs'] :  null;



    //dynamic

    if($name == 'container'){
      $item['type'] = $block['blockName'];
      $item['child'] = $this->getBlocks($block['innerBlocks']);
    }

    //if(!empty($block['attrs']['data'])){
      // $item['filter'] =$filter;
      // $item['screen'] = $screen ;
    //}
    //return $name;


    //  $this->findTabs($blocks); 
     // var_dump($this->tabs);
  //  if(!empty($this->tabs)){
  //   $tabsList =  $this->tabs['attrs']['tabs'];
  //     foreach($this->tabs['innerBlocks'] as $key => $block){
  //       if(!empty($block['innerBlocks'])){ //check if tabs content is not emtpy
  //         $filter =  $tabsList[$key]['title'];
  //         $allInnerBlocks = $this->getInnerBlocks($screen, $filter, $block['innerBlocks']);
  //         foreach($allInnerBlocks as $block ){
  //           $item['tabs'] = $block;
  //         }
  //       }
  //   }
  //  }
   // 

      if($name == 'brand/screens'){
        $item['innerBlocks'] = $this->getTabBlocks( $block['innerBlocks']);

      }
      if($name == 'brand/tab'){
  
        // $item['settings']=$block['attrs'];
        // $args = ['filter'=>$filter,'screen'=>$screen];
        $item['innerBlocks'] = $this->getBlocks($block['innerBlocks']);  
      }

      if($name == 'brand/tabs'){
       // $item['settings']= $block['attrs'];

       // $tabs = $this->getInnerBlocks($screen, $filter, $block['innerBlocks']);
      
      $item['innerBlocks'] = $this->getTabBlocks( $block['innerBlocks']);
  //  $item['innerBlocks'] =   $block['innerBlocks'];

         
      }

    if($name == 'brand/slider'){

      $item['attrs']['slides'] = $this->getSlides($block['attrs']['slides']) ;
    }

    if($name == 'core/columns'){
      $item['innerBlocks'] =  $block['innerBlocks']; 
    }

    if($name == 'core/column' || $name == 'core/group'){
      $item['innerBlocks'] = $this->getBlocks($block['innerBlocks']); 
    }


    if($name == 'core/paragraph'){
      $item = $block;
    }

    // if($name == 'brand/button'){
    //   $item = $block;
    // }



    if($name == 'brand/appbar'){
      $item['innerBlocks'] =  $this->getBlocks($block['innerBlocks']); ;
    
      
    }




    
    if($name == 'brand/categories'){
      $item['attrs']['categories']=  $this->getCategories($block['attrs']['categories']);

  
    //  $item['categories'] =  $block['attrs']['categories'];

      // $item['settings']['img_radius'] = intval($data['img_radius']) ;
      // $item['settings']['img_size'] =  intval($data['img_size']) ;
      // $item['settings']['layout'] = $data['cw_layout'];
      // if($data['cw_layout'] == 'grid'){
      //   $item['settings']['grid_items'] = intval($data['cw_grid_items']) ;
      // }
    }

    if($name == 'brand/banner-categories'){
      $item['image'] =    $block['attrs']['image'] ;
      $item['attrs']['categories'] =  $this->getCategories($block['attrs']['categories']);
    }

    if($name == 'brand/products'){
      $ctrl = new BrandProductController();
   
      $item['attrs']['products'] = $ctrl->getBlockProducts($block['attrs']['settings'],$item['attrs']['categories']);
      $item['attrs']['categories'] = []; //remove categories from payload 
    }

    if($name == 'brand/expcategories' ){

      $item['attrs']['categories']  = $this->getExpandableBlock($block['attrs']);
     
    
    }

    
    if($name == 'brand/tile' && !empty($block['attrs']['tile']['postId'])){
       // $blocks = get_xapp_blocks($block['attrs']['tile']['label']);
//        $item['innerBlocks'] = $this->getInnerBlocks($blocks,null);
        $postId = $block['attrs']['tile']['postId'];
        $item['attrs']['tile']['url'] = get_rest_url(null, "brand/page?postId={$postId}");
        
    }



    if(!empty($item)){
      $sections[] = $item;
    }
  }
  
  return $sections;

}


function get_block_data($post, $block_name = 'core/heading', $field_name = "" ){
	$content = "";
	if ( has_blocks( $post->post_content ) && !empty($field_name )) {
	    $blocks = parse_blocks( $post->post_content );
	    foreach($blocks as $block){
		    if ( $block['blockName'] === $block_name ) {
		    	if(isset($block["attrs"]["data"][$field_name ])){
                   $content = $block["attrs"]["data"][$field_name ];
		    	}
		    }	    	
	    }  
	}
	return $content;
}

// function getWidgets($s ){
//   $sidebars_widgets = wp_get_sidebars_widgets();
//   $sidebars= ['home_screen','search_screen','settings_screen'];

//   $sections = [];
//   $i = 0;
//   foreach( $sidebars ?: [] as $sidebar){

//     $widgets = [];
//     if(isset( $sidebars_widgets[$sidebar])){
//       $widgets = $sidebars_widgets[$sidebar];
//     }

 
//   foreach( $widgets ?: [] as  $widget){
//     $arr = explode("-",$widget);
    
//     $name = $arr[0];
//     $widget_id = $arr[1];
//     // var_dump('widget_' . $name);
//     //$widget_instances = get_option('widget_' . $name);

//       $wId = 'widget_' .$widget;

//       //common fileds
//       $filter = get_field('filter', 'widget_' .$widget);
//       $sections[$i]['filter'] = !empty($filter) ? $filter : null;
//       $sections[$i]['screen'] = $sidebar;
//       $sections[$i]['settings'] = $this->getSectionSettings($wId);


//     if($name == 'brandslider_widget'){
//       $sections[$i]['type']='slider';
//       $slides = get_field('slides', 'widget_' .$widget);
//       $sections[$i]['slides'] = $this->getSlides($slides);
//     }
    
//     if($name == 'brand_categoriescarousel_widget'){
//       $sections[$i]['type']='cc';
//       $cats = get_field('categories', $wId);
//       $sections[$i]['categories'] = $this->getCategories($cats);
      
//       //extra item settings
//       $sections[$i]['settings']['img_radius'] = intval(get_field('img_radius', $wId));
//       $sections[$i]['settings']['img_size'] = intval(get_field('img_size', $wId));
//       $sections[$i]['settings']['layout'] = get_field('cw_layout', $wId);
//       if(get_field('cw_layout', $wId) == 'grid'){
//         $sections[$i]['settings']['grid_items'] = intval(get_field('cw_grid_items', $wId)) ;
//       }

      
    
//     }
//     if($name == 'brand_bannerwithcategories_widget'){
//       $sections[$i]['type']='bc';
//       $cats = get_field('categories', 'widget_' .$widget);
//       $image = get_field('image', 'widget_' .$widget);
//       $sections[$i]['title'] = get_field('title', 'widget_' .$widget) ;
//       $sections[$i]['image'] = $image ;
//       $sections[$i]['categories'] = $this->getCategories($cats);
//     }
//     if($name == 'brand_productscarousel_widget'){
//       $sections[$i]['type']='product';
//       $sections[$i]['title'] = get_field('title', 'widget_' .$widget) ;
//       $ctrl = new BrandProductController();
//       $sections[$i]['products'] =  $ctrl->getBlockProducts($widget);
//       $sections[$i]['settings']['columns'] = intval(get_field('columns', $wId));
//     }
//     if($name == 'brandcategorylist_widget'){
//       $sections[$i]['type']= 'categorylist';
//       $sections[$i]['categories']  = $this->getCategoriesRow();
//     }
//     if($name == 'widget_link_tile'){
//       $sections[$i]['type']= $name ;
//       $sections[$i]['icon'] = get_field('leading_icon', 'widget_' .$widget) ;
     
//       $post =  get_field('page', 'widget_' .$widget) ;
//       $sections[$i]['title'] = $post->post_title;
//       $sections[$i]['content'] = $post->post_content ;
//     }

  
    
    
//     $i++;
//     }
//   }

//    return $sections;

// }

  public function getSettings($blocks)
  {
    $settings = [];


//$attrs =  $blocks[0]['attrs'];


    //general settings...
    $general = []; 
    $general['app_logo'] =  isset($attrs['app_logo'])?   $attrs['app_logo']['mediaUrl'] : null ;
    $general['app_logo_light'] =isset( $attrs['lightLogo']) ? $attrs['lightLogo']['mediaUrl'] : null ;
    if(isset($attrs['app_home_search'])){
      $general['app_home_search'] = $attrs['app_home_search']  ;
    }
    // woo options  
    $general['app_currency'] =  get_option('woocommerce_currency') ;
    $unicodeChar = get_woocommerce_currency_symbol(get_option('woocommerce_currency'));
    $general['app_currency_symbol'] =  html_entity_decode($unicodeChar , 0, 'UTF-8');
    $general['app_currency_pos'] =  get_option('woocommerce_currency_pos');

    $general['app_filter_min_price'] =  get_field('app_filter_min_price', 'option') ?  get_field('app_filter_min_price', 'option') : 10;
    $general['app_filter_max_price'] =  get_field('app_filter_max_price', 'option') ? get_field('app_filter_max_price', 'option') : 5000 ;
    $general['app_default_theme'] =  get_field('app_default_theme', 'option') ? get_field('app_default_theme', 'option') :false ;
    $general['app_theme_switcher'] =  json_decode(get_field('app_theme_switcher', 'option') ? get_field('app_theme_switcher', 'option') : false) ;
    
    $settings['general'] = $general;


    
    //intro settings...
    $intro = [];
    $intro['enable'] =  get_field('intro_enable', 'option') ? get_field('intro_enable', 'option') : false  ;
    $intro['title'] =  get_field('intro_title', 'option') ;
    $intro['content'] =  get_field('intro_subtitle', 'option') ;
    $intro['images'] =  get_field('intro_gallery', 'option') ;
    $settings['intro'] = $intro;


    //home settings...

    //if(isset($attrs['app_home_search'])){
     // $general['app_home_search'] =  $attrs['app_home_search'];
  // }


    $home = [];

   

 
    $settings['home'] = $home;


    return $settings;

  }
        
}




// class BlockHelper {

// 	var $block_id;
// 	var $post_id;

// 	function __construct( string $block_id, int $post_id ) {
// 		$this->block_id = $block_id;
// 		$this->post_id = $post_id;

// 	}
// 	public function getBlockFields() {
// 		$post = get_post( $this->post_id );

// 		if (! $post ) { return false; }

// 		$blocks = parse_blocks( $post->post_content );

// 		if ($blocks) {
// 			$iterator  = new RecursiveArrayIterator( $blocks );
// 			$recursive = new RecursiveIteratorIterator(
// 				$iterator,
// 				RecursiveIteratorIterator::SELF_FIRST
// 			);
// 			foreach ( $recursive as $key => $value ) {
// 				if ( isset($value['attrs']) && isset($value['attrs']['id']) ){
// 					if ( $value['attrs']['id'] === $this->block_id ) {
// 						acf_setup_meta( $value['attrs']['data'], $value['attrs']['id'], true );
// 						acf_reset_meta( $value['attrs']['id'] );
// 						return $value['attrs']['data'];
// 					}
// 				}
// 			}
// 		}
// 		return false;

// 	}

// }
