<?php

/*
Plugin Name: Plugin Loader Test
Plugin URI: https://wpmedic.tech/plugin-loader/
Description: Tester for Plugin Loader Class with Freemius Integration
Version: 1.0.9
Author: Tony Hayes
Author URI: https://wpmedic.tech
*/

// --------------------------------
// Test Plugin Options and Defaults
// --------------------------------
$options = array(

	// === General Tab ===

	// --- Checkbox Section ---
	'test_toggle'	=> array(
							'type' 		=> 'toggle',
							'label'		=> __('Toggle?'),
							'value'		=> 'yes',
							'default'	=> 'yes',
							'helper'	=> __('Toggle Input Type Help Text'),
							'tab'		=> 'general',
							'section'	=> 'checkboxes',
						),
 	'test_checkbox'	=>	array(
							'type' 		=> 'checkbox',
							'label'		=> __('Checkbox?'),
							'value'		=> 'yes',
							'default'	=> 'yes',
							'helper'	=> __('Checkbox Input Type Help Text'),
							'tab'		=> 'general',
							'section'	=> 'checkboxes',
						),
	'test_multicheck' => array(
							'type'		=> 'multicheck',
							'label'		=> __('MultiCheck?'),
							'value'		=> 'yes',
							'options'	=> array(
								'option1' => __('Option 1'),
								'option2' => __('Option 2'),
								'option3' => __('Option 3'),
							),
							'helper'	=> __('Multicheck Input Type Help Text'),
							'tab'		=> 'general',
							'section'	=> 'checkboxes',
						),

	// --- Selections Section ---
	'test_radio'	=> array(
							'type' 		=> 'radio',
							'label'		=> __('Radio?'),
							'default'	=> 'on',
							'options'	=> array(
								''			=> __('None'),
								'option1'	=> __('Option 1'),
								'option2'	=> __('Option 2'),
							),
							'helper'	=> __('Radio Input Type Help Text'),
							'tab'		=> 'general',
							'section'	=> 'selections',
						),
	'test_select'	=> 	array(
							'type'		=> 'select',
							'label'		=> __('Select?'),
							'options'	=> array(
								''			=> '',
								'value1'	=> __('Value 1'),
								'value2'	=> __('Value 2'),
								'value3'	=> __('Value 3'),
							),
							'helper'	=> __('Select Dropdown Input Type Help Text'),
							'tab'		=> 'general',
							'section'	=> 'selections',
						),
	'test_multiselect'	=> 	array(
							'type'		=> 'multiselect',
							'label'		=> __('MultiSelect?'),
							'options'	=> array(
								'value1'	=> __('Value 1'),
								'value2'	=> __('Value 2'),
								'value3'	=> __('Value 3'),
								'value4'	=> __('Value 4'),
								'value5'	=> __('Value 5'),
								'value6'	=> __('Value 6'),
							),
							'helper'	=> __('MultiSelect Dropdown Input Type Help Text'),
							'tab'		=> 'general',
							'section'	=> 'selections',
						),


	// === Text Tab ===

	// --- Basic Text Section ---
	'test_text'		=> array(
							'type'		=> 'text',
							'label'		=> __('Text?'),
							'placeholder'	=> "eg. Any random string.",
							'helper'	=> __('Text Input Type Help Text'),
							'tab'		=> 'text',
							'section'	=> 'basic',
						),
	'test_textarea'	=> array(
							'type'		=> 'textarea',
							'label'		=> __('Textarea'),
							'rows'		=> 5,
							'placeholder'	=> "eg. Some text.\nOn multiple lines.\nIs fine.",
							'helper'	=> __('Textarea Input Type Help Text'),
							'tab'		=> 'text',
							'section'	=> 'basic',
						),

	// --- Specific Text Section ---
	'test_email'		=> array(
							'type'		=> 'email',
							'label'		=> __('Email?'),
							'placeholder'	=> __('eg. youremail@example.com'),
							'helper'	=> __('Email Text Input Type Help Text'),
							'tab'		=> 'text',
							'section'	=> 'specific',
						),
	'test_numeric'		=> array(
							'type'		=> 'numeric',
							'label'		=> __('Number?'),
							'min'		=> 0.1,
							'max'		=> 3,
							'step'		=> 0.1,
							'suffix'	=> 'em',
							'placeholder'	=> 'eg. 0.5',
							'helper'	=> __('Numeric Text Input Type Help Text'),
							'tab'		=> 'text',
							'section'	=> 'specific',
						),
	'test_alphanumeric'	=> array(
							'type'		=> 'alphanumeric',
							'label'		=> __('Alphanumeric?'),
							'placeholder' => 'eg. abcde12345',
							'helper'	=> __('Alphanumeric Text Input Type Help Text'),
							'tab'		=> 'text',
							'section'	=> 'specific',
						),
	'test_url'			=> array(
							'type'		=> 'url',
							'label'		=> __('URL?'),
							'placeholder'	=> 'eg. https://wpmedic.tech',
							'helper'	=> __('URL Text Input Type Help Text'),
							'tab'		=> 'text',
							'section'	=> 'specific',
						),

	// --- CSV Text Section ---
	'test_csv'			=> array(
							'type'		=> 'csv',
							'label'		=> __('Values?'),
							'placeholder'	=> 'eg. Value1, Value2, Value3 etc.',
							'helper'	=> __('Comma Separated Values Input Type Help Text'),
							'tab'		=> 'text',
							'section'	=> 'csv',
						),
	'test_csvslug'		=> array(
							'type'		=> 'csvslugs',
							'label'		=> __('Slugs'),
							'placeholder'	=> 'eg. value-1, value-2, value-3 etc.',
							'helper'	=> __('Comma Separated Slugs Input Type Help Text'),
							'tab'		=> 'text',
							'section'	=> 'csv',
						),
	'test_emails'		=> array(
							'type'		=> 'emails',
							'label'		=> __('Emails?'),
							'placeholder'	=> 'eg. email1@example.com, email2@example.com etc.',
							'helper'	=> __('Comma Separated Emails Input Type Help Text'),
							'tab'		=> 'text',
							'section'	=> 'csv',
						),
	'test_usernames'	=> array(
							'type'		=> 'usernames',
							'label'		=> __('Usernames?'),
							'placeholder'	=> 'eg. username1, username2, username3 etc.',
							'helper'	=> __('Comma Separated Usernames Input Type Help Text'),
							'tab'		=> 'text',
							'section'	=> 'csv',
						),


	// === Special Tab ===
	'test_publictypes' => array(
							'type'		=> 'multicheck',
							'label'		=> __('Public Post Types?'),
							'options'	=> 'PUBLICTYPES',
							'helper'	=> __('Public Post Type Multicheck Help Text'),
							'tab'		=> 'special',
							'section'	=> 'multicheck',
						),
	'test_posttypes' => array(
							'type'		=> 'multicheck',
							'label'		=> __('Post Types?'),
							'options'	=> 'POSTTYPES',
							'helper'	=> __('Post Type Multicheck Help Text'),
							'tab'		=> 'special',
							'section'	=> 'multicheck',
						),
	'test_allposttypes' => array(
							'type'		=> 'multicheck',
							'label'		=> __('All Post Types?'),
							'options'	=> 'ALLTYPES',
							'helper'	=> __('All Post Type Multicheck Help Text'),
							'tab'		=> 'special',
							'section'	=> 'multicheck',
						),
	'test_pageid'	=> array(
							'type'		=> 'select',
							'label'		=> __('Page?'),
							'options'	=> 'PAGEID',
							'helper'	=> __('Page ID Selection Help Text'),
							'tab'		=> 'special',
							'section'	=> 'selections',
						),
	'test_postid'	=> array(
							'type'		=> 'select',
							'label'		=> __('Post?'),
							'options'	=> 'POSTID',
							'helper'	=> __('Post ID Selection Help Text'),
							'tab'		=> 'special',
							'section'	=> 'selections',
						),
	'test_userid'	=> array(
							'type'		=> 'select',
							'label'		=> __('User ID?'),
							'options'	=> 'USERID',
							'helper'	=> __('User ID Selection Help Text'),
							'tab'		=> 'special',
							'section'	=> 'selections'
						),
	'test_userid'	=> array(
							'type'		=> 'select',
							'label'		=> __('Username?'),
							'options'	=> 'USERNAME',
							'helper'	=> __('Username Selection Help Text'),
							'tab'		=> 'special',
							'section'	=> 'selections'
						),


	// --- Tabs and Sections ---
	'tabs'			=> array(
							'general'	=> __('General'),
							'text'		=> __('Text'),
							'special'	=> __('Special'),
						),
	'sections'		=> array(
							'checkboxes' => __('Checkboxes'),
							'selections' => __('Selections'),
							'basic'		 => __('Basic'),
							'specific'	 => __('Specific'),
							'csv'		 => __('Comma Separated Values'),
							'multicheck' => __('MultiCheck'),
						),
);

// --------------------
// Test Plugin Settings
// --------------------
$slug = 'loader-test';
$args = array(
	// --- Plugin Info ---
	'slug'			=> $slug,
	'file'			=> __FILE__,
	'version'		=> '0.0.1',

	// --- Menus and Links ---
	'title'			=> 'Plugin Loader Test',
	// 'parentmenu'		=> 'wordquest',
	'home'			=> 'https://wpmedic.tech/plugin-loader/',
	'support'		=> 'https://wordquest.org/solutions/',
	'ratetext'		=> __('Rate on WordPress.org'),
	'share'			=> 'https://wpmedic.tech/#share',
	'sharetext'		=> __('Share the Plugin Love'),
	'donate'		=> 'https://patreon.com/wpmedic',
	'donatetext'		=> __('Support this Plugin'),
	// 'readme'			=> false,
	// 'settingsmenu'	=> false,

	// --- Options ---
	'namespace'		=> 'PREFIX',
	'settings'		=> 'lt',
	'option'		=> 'loader_test',
	'options'		=> $options,

	// --- WordPress.Org ---
	// 'wporgslug'		=> 'loader-test',
	'wporg'			=> false,
	'textdomain'		=> 'loader-test',

	// --- Freemius ---
	// 'freemius_id'	=> '',
	// 'freemius_key'	=> '',
	// 'hasplans'		=> false,
	// 'hasaddons'		=> false,
	// 'plan'		=> 'free',
);

// ----------------------------
// Start Plugin Loader Instance
// ----------------------------
$loader = dirname( __FILE__ ) . '/loader.php';
if ( file_exists( $loader ) ) {
	require $loader;
	new NAMESPACE_loader( $args );
}


// --------------------
// Output Posted Values
// --------------------
add_action( 'NAMESPACE_admin_page_top', 'NAMESPACE_output_posted' );
function NAMESPACE_output_posted() {

	echo "<br><b>Current Settings:</b><br>";
	var_dump( NAMESPACE_get_settings() );
	echo "<br><br>";

	echo "<br><b>Plugin Options:</b><br>";
	$instance = $GLOBALS['NAMESPACE_instance'];
	var_dump( $instance->options );
	echo "<br><br>";

	echo "<br><b>Posted Values:</b><br>";
	foreach ( $_POST as $key => $value ) {
		echo $key . ': ' . var_dump( $value ).'<br>';
	}
}
