<?php
//Load template from specific page
add_filter( 'page_template', 'wpa3396_page_template' );
function wpa3396_page_template( $page_template ){

	if ( get_page_template_slug() == 'template-brand.php' ) {
		remove_theme_support('editor-styles');
		$page_template =dirname( __FILE__ )  . '/template-brand.php';
		
	}
	return $page_template;
}

/**
 * Add "Custom" template to page attirbute template section.
 */
add_filter( 'theme_page_templates', 'brand_add_template_to_select', 10, 4 );
function brand_add_template_to_select( $post_templates, $wp_theme, $post, $post_type ) {

	// Add custom template named template-custom.php to select dropdown 
	$post_templates['template-brand.php'] = __('Brand App');

	return $post_templates;
}

function brand_dequeue_theme_assets() {

	if ( get_page_template_slug() != 'template-brand.php' ) return;
    $wp_scripts = wp_scripts();
    $wp_styles  = wp_styles();
    $themes_uri = get_theme_root_uri();

    // foreach ( $wp_scripts->registered as $wp_script ) {
    //     if ( strpos( $wp_script->src, $themes_uri ) !== false ) {
    //         wp_deregister_script( $wp_script->handle );
    //     }
    // }

    foreach ( $wp_styles->registered as $wp_style ) {
        if ( strpos( $wp_style->src, $themes_uri ) !== false ) {
            wp_deregister_style( $wp_style->handle );
        }
    }
}
//add_action( 'wp_enqueue_scripts', 'brand_dequeue_theme_assets', 999 );
