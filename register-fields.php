<?php

register_post_meta( 'xapp', 'apiKey', [
	'show_in_rest' => true,
	'single' => true,
	'type' => 'string',
] );

register_post_meta( 'brand_app', 'email', [
	'show_in_rest' => true,
	'single' => true,
	'type' => 'string',
] );


register_post_meta( 'brand_app', 'app_name', [
	'show_in_rest' => true,
	'single' => true,
	'type' => 'string',
] );

register_post_meta( 'brand_app', 'appIcon', [
	'show_in_rest' => true,
	'single' => true,
	'type' => 'integer',
] );

register_post_meta( 'brand_app', 'appSplashIcon', [
	'show_in_rest' => true,
	'single' => true,
	'type' => 'integer',
] );

register_post_meta( 'brand_app', 'appSplashColor', [
	'show_in_rest' => true,
	'single' => true,
	'type' => 'string',
] );