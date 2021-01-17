<?php

// ============================ //
/* Plugin Loader Class Template */
// ============================ //


// Loader Usage:
// =============
// 1. replace all occurrences of NAMESPACE in this file with the plugin namespace
// 2. define plugin options, default settings, and setup arguments in the plugin file
// 3. after definitions, require this file in the main plugin file (example below)


// ---------------------------
// Plugin Options and Defaults
// ---------------------------
// array of plugin option keys, with input types and defaults
// $options = array(
// 	'optionkey1'	=>	array(
//							'type' 		=> 'checkbox',
//							'default'	=> '1',
//						),
//	'optionkey2'	=>	array(
//							'type' 		=> 'radio',
//							'default'	=> 'on',
//							'options'	=> 'on/off',
//						),
//	'optionkey3'	=> array(
//							'type'		=> 'special',
//						),
// );

// ---------------
// Plugin Settings
// ---------------
// $slug = 'plugin-name';				// plugin slug (usually same as filename)
// $args = array(
//	// --- Plugin Info ---
//	'slug'			=> $slug,			// (uses slug above)
//	'file'			=> __FILE__,		// path to main plugin file (important!)
//	'version'		=> '0.0.1', 		// * rechecked later (from plugin header) *
//
//	// --- Menus and Links ---
//	'title'			=> 'Plugin Name',	// plugin title
//	'parentmenu'	=> 'wordquest',		// parent menu slug
//	'home'			=> 'http://mysite/plugins/plugin/',
//	'support'		=> 'http://mysite/plugins/plugin/support/',
//
//	// --- Options ---
//	'namespace'		=> 'plugin_name',	// plugin namespace (function prefix)
//	'settings'		=> 'pn',			// sidebar settings prefix
//	'option'		=> 'plugin_key',	// plugin option key
//	'options'		=> $options,		// plugin options array set above
//
//	// --- WordPress.Org ---
//	'wporgslug'		=> 'wp-automedic',	// WordPress.org plugin slug
//	'wporg'			=> false, 			// * rechecked later (via presence of updatechecker.php) *
//	'textdomain'	=> 'wp-automedic',	// translation text domain (usually same as slug)
//
//	// --- Freemius ---
//	'freemius_id'	=> '',				// Freemius plugin ID
//	'freemius_key'	=> '',				// Freemius public key
//	'hasplans'		=> false,			// has paid plans?
//	'hasaddons'		=> false,			// has add ons?
//	'plan'			=> 'free',	 		// * rechecked later (if premium version found) *
// );

// ----------------------------
// Start Plugin Loader Instance
// ----------------------------
// require(dirname(__FILE__).'/loader.php');	// requires this file!
// new NAMESPACE_Loader($args);					// instantiates loader class



// ==============================
// --- NAMESPACE Loader Class ---
// ==============================
// usage: simply change NAMESPACE to the plugin function prefix
class NAMESPACE_loader {

	public $args = null;
	public $namespace = null;
	public $options = null;
	public $defaults = null;
	public $data = null;

	// -----------------
	// Initialize Loader
	// -----------------
	function init($args) {

		// set plugin namespace
		$this->namespace = $args['namespace'];

		// set options
		$this->options = $args['options']; unset($args['options']);

		// setup values
		$this->setup_plugin();

		// maybe transfer settings
		$this->maybe_transfer_settings();

		// load settings
		$this->load_settings();

		// load actions
		$this->add_actions();

		// load helper libraries
		$this->load_helpers();

		// set this class instance to global for accessibility
		$GLOBALS[$args['namespace'].'_instance'] = $this;
	}

	// ------------
	// Setup Plugin
	// ------------
	function setup_plugin() {
		$args = $this->args; $namespace = $this->namespace;

		// --- Read Plugin Header ---
		if (!isset($args['dir'])) {$args['dir'] = dirname(__FILE__);}
		$fh = fopen($args['file'], 'r'); $data = fread($fh, 2048);
		$this->data = str_replace("\r", "\n", $data); fclose($fh);

		// --- Title ---
		if (!isset($args['title'])) {$args['title'] = $this->plugin_data('Plugin Name:');}

		// --- Plugin Home ---
		if (!isset($args['home'])) {$args['home'] = $this->plugin_data('Plugin URI:');}

		// --- Version ---
		if (!isset($args['version'])) {$args['version'] = $this->plugin_data('Version:');}

		// --- Author ---
		if (!isset($args['author'])) {$args['author'] = $this->plugin_data('Author:');}

		// --- Author URL ---
		if (!isset($args['author_url'])) {$args['author_url'] = $this->plugin_data('Author URI:');}

		// --- Pro Functions ---
		if (!isset($args['proslug'])) {
			$proslug = $this->plugin_data('@fs_premium_only');
			$args['proslug'] = substr($proslug, 0, -4);		// strips .php extension
		}

		// update the loader args
		$this->args = $args;
	}

	// -----------------
	// Set Pro Namespace
	// -----------------
	function pro_namespace($pronamespace) {
		$this->args['pronamspace'] = $pronamespace;
	}

	// ---------------
	// Get Plugin Data
	// ---------------
	function plugin_data($key) {
		$data = $this->data; $value = null;
		$pos = strpos($data, $key);
		if ($pos !== false) {
			$pos = $pos + strlen($key) + 1;
			$tmp = substr($data, $pos);
			$pos = strpos($tmp, "\n");
			$value = trim(substr($tmp, 0, $pos));
		}
		return $value;
	}

	// --------------------
	// Get Default Settings
	// --------------------
	function default_settings($dkey=false) {

		// return defaults if already set
		$defaults = $this->defaults;
		if (!is_null($defaults)) {
			if ($dkey && isset($defaults[$dkey])) {return $defaults[$dkey];}
			return $defaults;
		}

		// filter and store the plugin default settings
		$options = $args->options; $defaults = array();
		foreach ($options as $key => $values) {$defaults[$key] = $values['default'];}
		$namespace = $this->namespace;
		$defaults = apply_filters($namespace.'_default_settings', $defaults);
		$this->defaults = $defaults;
		if ($dkey && isset($defaults[$dkey])) {return $defaults[$dkey];}
		return $defaults;
	}

	// ------------
	// Add Settings
	// ------------
	function add_settings() {
		// add the default plugin settings
		$args = $this->args; $defaults = $this->default_settings();
		$added = add_option($args['option'], $defaults);

		// if added, make the defaults current settings
		if ($added) {
			$namespace = $this->namespace;
			foreach ($defaults as $key => $value) {$GLOBALS[$namespace][$key] = $value;}
		}

		// add sidebar settings
		if (file_exists($args['dir'].'/updatechecker.php')) {$adsboxoff = '';} else {$adsboxoff = 'checked';}
		$sidebaroptions = array('adsboxoff' => $adsboxoff, 'donationboxoff' => '', 'reportboxoff' => '', 'installdate' => date('Y-m-d'));
		add_option($args['settings'].'_sidebar_options', $sidebaroptions);
	}

	// -----------------------
	// Maybe Transfer Settings
	// -----------------------
	function maybe_transfer_settings() {
		$namespace = $this->namespace; $funcname = $namespace.'_transfer_settings';
		// check for either function prefixed or class extended method
		if (method_exists($this, 'transfer_settings')) {$settings = $this->transfer_settings();}
		elseif (function_exists($funcname)) {$settings = call_user_func($funcname);}
		$GLOBALS[$namespace] = $settings;
	}

	// ----------------
	// Get All Settings
	// ----------------
	function get_settings() {
		$namespace = $this->namespace;
		$settings = $GLOBALS[$namespace];
		$settings = apply_filters($namespace.'_settings', $settings);
		return $settings;
	}

	// ------------------
	// Get Plugin Setting
	// ------------------
	function get_setting($key, $filter=true) {
		$namepace = $this->namespace; $settings = $GLOBALS[$namespace];
		$settings = apply_filters($namespace.'_settings', $settings);

		if (isset($settings[$key])) {$value = $settings[$key];}
		else {
			$defaults = $this->default_settings();
			if (isset($defaults[$key])) {$value = $defaults[$key];}
			else {$value = null;}
		}
		if ($filter) {$value = apply_filters($namespace.'_'.$key, $value);}
		return $value;
	}

	// ---------------------
	// Reset Plugin Settings
	// ---------------------
	function reset_settings() {
		$args = $this->args; $namespace = $this->namespace;

		// check triggers and permissions
		if (!isset($_POST[$args['settings'].'_update_settings'])) {return;}
		if ($_POST[$settings['args'].'_update_settings'] != 'reset') {return;}
		$capability = apply_filters($args['namespace'].'_manage_options_capability', 'manage_options');
		if (!current_user_can($capability)) {return;}
		check_admin_referer($args['slug']);

		// reset plugin settings
		$defaults = $this->default_settings();
		$defaults['savetime'] = time();
		update_option($args['option'], $defaults);

		// loop to remerge with settings global
		foreach ($defaults as $key => $value) {$GLOBALS[$namespace][$key] = $value;}

		// set settings reset message flag
		$_GET['updated'] = 'reset';
	}

	// ----------------------
	// Update Plugin Settings
	// ----------------------
	function update_settings() {
		$args = $this->args; $namespace = $this->namespace;
		$settings = $GLOBALS[$namespace];

		// check triggers and permissions
		if (!isset($_POST[$settings['settings'].'_update_settings'])) {return;}
		if ($_POST[$settings['settings'].'_update_settings'] != 'yes') {return;}
		$capability = apply_filters($namespace.'_manage_options_capability', 'manage_options');
		if (!current_user_can($capability)) {return;}
		check_admin_referer($settings['slug']);

		// get plugin options and defaults
		$options = $this->options;
		$defaults = $this->default_settings();

		// maybe use custom function or method
		if (method_exists($this, 'process_settings')) {
			// check for an extended method to this class
			$settings = $this->process_settings();
		} elseif (function_exists($namespace.'_process_settings')) {
			// check for a namespace prefixed function
			$settings = call_user_func($namespace.'_process_settings', $options);
		} else {
			// loop plugin options
			foreach ($options as $key => $type) {
				// get posted value
				$postkey = $args['settings'].'_'.$key;
				if (isset($_POST[$postkey])) {$posted = $_POST[$postkey];}

				// sanitize value according to type
				// TODO: add to these sanitization types from/for plugins
				if (strstr($type, '/')) {
					$valid = explode('/', $type);
					if (in_array($posted, $valid)) {$settings[$key] = $posted;}
				} elseif ($type == 'checkbox') {
					if ( ($posted == '') || ($posted == 'yes') ) {$settings[$key] = $posted;}
				} elseif ($type == 'numeric') {
					$posted = absint($posted);
					if (is_numeric($posted)) {$settings[$key] = $posted;}
				} elseif ($type == 'alphanumeric') {
					// TODO: maybe improve on this?
					$checkposted = preg_match('/^[a-zA-Z0-9_]+$/', $posted);
					if ($checkposted) {$settings[$key] = $posted;}
				} elseif ($type == 'text') {
					$posted = sanitize_text_field($posted);
					$settings[$key] = $posted;
				} elseif ($type == 'textarea') {
					$posted = stripslashes($posted);
					$settings[$key] = $posted;
				}
			}
		}

		// loop default keys to remove others
		$settings_keys = array_keys($defaults);
		foreach ($settings as $key => $value) {
			if (!in_array($key, $settings_keys)) {unset($settings[$key]);}
		}

		// update the plugin settings
		$settings['savetime'] = time();
		update_option($args['option'], $settings);

		// merge with existing settings for pageload
		foreach ($settings as $key => $value) {$GLOBALS[$namespace][$key] = $value;}

		// set settings update message flag
		$_GET['updated'] = 'yes';

		// maybe update pro settings
		if (isset($args['pronamespace'])) {$funcname = $args['pronamespace'].'_update_settings';}
		else {$funcname = $args['namespace'].'_pro_update_settings';}
		if (function_exists($funcname)) {call_user_func($funcname);}

	}

	// ---------------
	// Delete Settings
	// ---------------
	function delete_settings() {
		// TODO: check for settings delete settings switch
		// $args = $this->args;
		// delete_option($args['option']);
	}


	// ===============
	// --- Loading ---
	// ===============

	// --------------------
	// Load Plugin Settings
	// --------------------
	function load_settings() {
		$args = $this->args; $namespace = $this->namespace;
		$GLOBALS[$namespace] = $args;
		$settings = get_option($args['option'], false);
		if ($settings && is_array($settings)) {
			foreach ($settings as $key => $value) {$GLOBALS[$namespace][$key] = $value;}
		} else {
			$defaults = $this->default_settings();
			foreach ($defaults as $key => $value) {$GLOBALS[$namespace][$key] = $value;}
		}
	}

	// -----------
	// Add Actions
	// -----------
	function add_actions() {
		$args = $this->args;
		// add settings on activation
		register_activation_hook($args['file'], array($this, 'add_settings'));

		// always check for update and reset of settings
		add_action('admin_init', array($this, 'update_settings'));
		add_action('admin_init', array($this, 'reset_settings'));

		// add plugin submenu
		add_action('admin_menu', array($this, 'settings_menu'), 1);

		// add plugin settings page link
		add_filter('plugin_action_links', array($this, 'settings_link'), 10, 2);

		// delete settings on deactivation
		// register_deactivation_hook($args['file'], array($this, 'delete_settings'));

		// maybe load thickbox
		add_action('admin_enqueue_scripts', array($this, 'maybe_load_thickbox'));

	}

	// ---------------------
	// Load Helper Libraries
	// ---------------------
	function load_helpers() {
		$args = $this->args; $file = $args['file']; $dir = $args['dir'];

		// --- Plugin Slug ---
		if (!isset($args['slug'])) {$args['slug'] = substr($file, 0, -4); $this->args = $args;}

		// --- Pro Functions ---
		$plan = 'free'; $profunctions = $dir.'/'.$proslug.'.php';
		if (file_exists($profunctions)) {$plan = 'premium'; include($profunctions);}
		$args['plan'] = $plan; $this->args = $args;

		// --- Plugin Update Checker ---
		// note: lack of updatechecker.php file indicates WordPress.Org SVN repo version
		// presence of updatechecker.php indicates direct site download or GitHub version
		$wporg = true; $updatechecker = $dir.'/updatechecker.php';
		if (file_exists($updatechecker)) {
			$wporg = false; $slug = $args['slug'];
			// note: requires $file and $slug
			include($updatechecker);
		}
		$args['wporg'] = $wporg; $this->args = $args;

		// --- WordQuest Admin ---
		if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
			global $wordquestplugins; $slug = $args['slug'];
			foreach ($args as $key => $value) {$wordquestplugins[$slug][$key] = $value;}
			$wordquest = $dir.'/wordquest.php';
			if (file_exists($wordquest) && is_admin()) {include($wordquest);}
		}

		// --- Freemius ---
		if (version_compare(PHP_VERSION, '5.4.0') >= 0) {$this->load_freemius();}

	}

	// -------------------
	// Maybe Load Thickbox
	// -------------------
	function maybe_load_thickbox() {
		$args = $this->args;
		if (isset($_REQUEST['page']) && ($_REQUEST['page'] == $args['slug'])) {add_thickbox();}
	}


	// =======================
	// --- Freemius Loader ---
	// =======================
	//
	// required settings keys:
	// -----------------------
	// freemius_id	- plugin ID from Freemius plugin dashboard
	// freemius_key	- public key from Freemius plugin dashboard
	//
	// optional settings keys:
	// -----------------------
	// plan 		- (string) curent plugin plan (value of 'free' or 'premium')
	// hasplans		- (boolean) switch for whether plugin has premium plans
	// hasaddons	- (boolean) switch for whether plugin has premium addons
	// wporg		- (boolean) switch for whether free plugin is WordPress.org compliant
	// contact		- (boolean) submenu switch for plugin Contact (defaults to on for premium only)
	// support		- (boolean) submenu switch for plugin Support (default on)
	// account		- (boolean) submenu switch for plugin Account (default on)
	// parentmenu	- (string) optional slug for plugin parent menu
	//
	// okay lets do this...
	// ====================
	function load_freemius() {

		$args = $this->args; $namespace = $this->namespace;

		// check for required keys
		if (!isset($args['freemius_id']) || !isset($args['freemius_key'])) {return;}

		// check for free / premium plan
		// convert plan string value of 'free' or 'premium' to boolean premium switch
		$premium = false; if (isset($args['plan']) && ($args['plan'] == 'premium')) {$premium = true;}

		// maybe redirect link to plugin support forum
		if (isset($_REQUEST['page']) && ($_REQUEST['page'] == $args['slug'].'-wp-support-forum') && is_admin()) {
			if (!function_exists('wp_redirect')) {include(ABSPATH.WPINC.'/pluggable.php');}
			if (isset($args['support'])) {
				// changes the support forum slug for premium based on the pro plugin file slug
				if ($premium) {$support_url = str_replace($args['slug'], $args['proslug'], $args['support']);}
				$support_url = apply_filters('freemius_plugin_support_url_redirect', $support_url, $args['slug']);
				wp_redirect($support_url); exit;
			}
		}

		// do the Freemius Loading boogie
		if (!isset($args['freemius'])) {

			// start the Freemius SDK
			if (!class_exists('Freemius')) {
				$freemiuspath = dirname(__FILE__).'/freemius/start.php';
				if (file_exists($freemiuspath)) {require_once($freemiuspath);} else {return;}
			}

			// set defaults for optional key values
			if (!isset($args['hasaddons'])) {$args['hasaddons'] = false;}
			if (!isset($args['hasplans'])) {$args['hasplans'] = false;}
			if (!isset($args['wporg'])) {$args['wporg'] = false;}

			// set defaults for options submenu key values
			if (!isset($args['args'])) {$support = true;}
			if (!isset($args['args'])) {$account = true;}
			// by default, enable contact submenu item for premium plugins only
			if (!isset($args['args'])) {$args['contact'] = $premium;}

			// Freemius settings from plugin settings
			$settings = array(
				'type'				=> 'plugin',
				'slug'              => $args['slug'],
				'id'                => $args['freemius_id'],
				'public_key'        => $args['freemius_key'],
				'has_addons'        => $args['hasaddons'],
				'has_paid_plans'    => $args['hasplans'],
				'is_org_compliant'  => $args['wporg'],
				'is_premium'        => $premium,
				'menu'              => array(
					'slug'       	=> $args['slug'],
					'first-path' 	=> 'admin.php?page='.$settings['slug'].'&welcome=true',
					'contact'		=> $args['contact'],
					'support'		=> $args['support'],
					'account'		=> $args['account'],
			   )
			);

			// maybe add plugin submenu to parent menu
			if (isset($args['parentmenu'])) {
				$settings['menu']['parent'] = array('slug' => $args['parentmenu']);
			}

			// filter settings before initializing
			$settings = apply_filters('freemius_init_settings_'.$args['namespace'], $settings);
			if (!$settings || !is_array($settings)) {return;}

			// initialize Freemius now
			$GLOBALS[$namespace.'_freemius'] = fs_dynamic_init($settings);

			// add Freemius connect message filter
			$this->freemius_connect();
		}
	}

	// -----------------------
	// Filter Freemius Connect
	// -----------------------
	function freemius_connect() {
		$namespace = $this->args['namespace']; $freemius = $GLOBALS[$namespace.'_freemius'];
		if (isset($settings['freemius']) && is_object($freemius) && method_exists($freemius, 'add_filter') ) {
			$freemius->add_filter('connect_message', array($this, 'freemius_message'), WP_FS__DEFAULT_PRIORITY, 6);
		}
	}

	// ------------------------
	// Freemius Connect Message
	// ------------------------
	function freemius_message($message, $user_first_name, $plugin_title, $user_login, $site_link, $freemius_link) {
		$message = __fs('hey-x').'<br>';
		$message .= sprintf(
			__("If you want to more easily access support and feedback for this plugins features and functionality, %s can connect your user, %s at %s, to %s"),
			$user_first_name, '<b>'.$plugin_title.'</b>', '<b>'.$user_login.'</b>', $site_link, $freemius_link
		);
		return $message;
	}


	// =============
	// --- Admin ---
	// =============

	// -----------------
	// Add Settings Menu
	// -----------------
	function settings_menu() {
		$namespace = $this->namespace; $settings = $GLOBALS[$namespace];

		$args['capability'] = apply_filters($args['namespace'].'_manage_options_capability', 'manage_options');
		if (!isset($args['pagetitle'])) {$args['pagetitle'] = $args['title'];}
		if (!isset($args['menutitle'])) {$args['menutitle'] = $args['title'];}

		// check for WordQuest admin page function
		if (function_exists('wqhelper_admin_page')) {

			// filter menu capability early
			$capability = apply_filters('wordquest_menu_capability', 'manage_options');

			// maybe add Wordquest top level menu
			global $admin_page_hooks;
			if (empty($admin_page_hooks['wordquest'])) {
				$icon = plugins_url('images/wordquest-icon.png', $args['file']);
				$position = apply_filters('wordquest_menu_position', '3');
				add_menu_page('WordQuest Alliance', 'WordQuest', $capability, 'wordquest', 'wqhelper_admin_page', $icon, $position);
			}

			// check if using parent menu (and parent menu capability)
			if (isset($args['parentmenu']) && ($args['parentmenu'] == 'wordquest') && current_user_can($capability)) {

				// add WordQuest Plugin Submenu
				$menuadded = add_submenu_page('wordquest', $args['pagetitle'], $args['menutitle'], $args['capability'], $args['slug'], $args['namespace'].'_settings_page');

				// add icons and styling fix to the plugin submenu :-)
				add_action('admin_footer', array($this, 'submenu_fix'));
			}
		}

		if (!isset($menuadded) || !$menuadded) {
			// add a standalone settings page if WordQuest Admin not loaded
			add_options_page($args['pagetitle'], $args['menutitle'], $args['capability'], $args['slug'], $args['namespace'].'_settings_page');
		}
	}

	// ---------------------
	// WordQuest Submenu Fix
	// ---------------------
	function submenu_fix() {
		$args = $this->args; $slug = $args['slug']; $current = '0';
		$icon_url = plugins_url('images/icon.png', $args['file']);
		if (isset($_REQUEST['page']) && ($_REQUEST['page'] == $slug) ) {$current = '1';}
		echo "<script>jQuery(document).ready(function() {if (typeof wordquestsubmenufix == 'function') {
		wordquestsubmenufix('".$slug."','".$icon_url."','".$current."');} });</script>";
	}

	// -------------------------
	// Plugin Page Settings Link
	// -------------------------
	function settings_link($links, $file) {
		$args = $this->args;
		if ($file == plugin_basename($args['file'])) {
			$settingslink = "<a href='".admin_url('admin.php')."?page=".$args['slug']."'>".__('Settings')."</a>";
			array_unshift($links, $settingslink);
		}
		return $links;
	}

	// -----------
	// Message Box
	// -----------
	function message_box($message, $echo) {
		$box = "<table style='background-color: lightYellow; border-style:solid; border-width:1px; border-color: #E6DB55; text-align:center;'>";
		$box .= "<tr><td><div class='message' style='margin:0.25em;'><font style='font-weight:bold;'>";
		$box .= $message."</font></div></td></tr></table>";
		if ($echo) {echo $box;} else {return $box;}
	}

	// ------------------
	// Plugin Page Header
	// ------------------
	function settings_header() {
		$args = $this->args; $namespace = $this->namespace; $settings = $GLOBALS[$namespace];

		$icon_url = plugins_url('images/'.$args['slug'].'.png', $args['file']);
		$icon_url = apply_filters($namespace.'_plugin_icon_url', $icon_url);
		$wpmedic_icon_url = plugins_url('images/wpmedic.png', $args['file']);
		$wordquest_icon_url = plugins_url('images/wordquest.png', $args['file']);
		echo "<table><tr><td><img src='".$icon_url."'></td>";
		echo "<td width='20'></td><td>";
			echo "<table><tr><td>";
				echo "<h2 style='font-size:20px;'><a href='".$args['home']."' style='text-decoration:none;'>".$args['title']."</a></h2></a>";
			echo "</td><td width='20'></td>";
			echo "<td><h3>v".$args['version']."</h3></td></tr>";
			echo "<tr><td colspan='3' align='center'>";
				echo "<table><tr><td><font style='font-size:16px;'>".__('by')."</font></td>";
				echo "<td><a href='".$args['author_url']."' target=_blank style='text-decoration:none;font-size:16px;' target=_blank><b>".$args['author']."</b></a></td>";
				echo "<td><a href='".$args['author_url']."' target=_blank><img src='".$wpmedic_icon_url."' width='64' height='64' border='0'></td></tr></table>";
			echo "</td></tr></table>";
		echo "</td><td width='50'></td><td align='center' style='vertical-align:top;'>";

			// readme thickbox link
			// $readme_url = plugins_url('readme.txt', $args['file'];
			// echo "<br><a href='".$readme_url."' class='thickbox'><b>".__('Readme')."</b></a>";
			// if (isset($settings['docs'])) {echo " | <a href='".$settings['docs']."' target=_blank><b>".__('Docs')."</b></a>";}
			// if (isset($settings['home'])) {echo " | <a href='".$settings['home']."' target=_blank><b>".__('Home')."</b></a>";}
			// echo "<br><br>";

			// updated and reset messages
			if (isset($_GET['updated'])) {
				if ($_GET['updated'] == 'yes') {$message = $settings['title'].' '.__('Settings Updated.');}
				elseif ($_GET['updated'] == 'reset') {$message = $settings['title'].' '.__('Settings Reset!');}
				if (isset($message)) {$this->message_box($message, true);}
			}
		echo "</td></tr></table><br>";
	}

	// -------------
	// Settings Page
	// -------------
	function settings_page() {
		// TODO: could create and automatic settings page here
		// based on the passed plugin options and default settings...
	}

} // end plugin loader class


// ----------------------------------
// Load Namespaced Prefixed Functions
// ----------------------------------
// [Optional] rename the NAMESPACE to your plugin namespace
// these functions will then be available within your plugin
// to more easily call the matching plugin loader class methods

add_action('plugins_loaded', 'NAMESPACE_namespaced_functions');
function NAMESPACE_load_prefixed_functions() {

	// auto-magic namespacing note
	// ---------------------------
	// all function names suffixes here must be two words for the magic namespace grabber to work
	// ie. _add_settings, because the namespace is taken from before the second-last underscore

	// ------------
	// Add Settings
	// ------------
	if (!function_exists('NAMESPACE_add_settings')) {
	 function NAMESPACE_add_settings() {
		$f = __FUNCTION__; $namespace = substr($f, 0, strrpos($f, '_', (strrpos($f, '_') - strlen($f) - 1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		$instance->add_settings();
	 }
	}

	// ------------
	// Get Defaults
	// ------------
	if (!function_exists('NAMESPACE_default_settings')) {
	 function NAMESPACE_default_settings($key=false) {
		$f = __FUNCTION__; $namespace = substr($f, 0, strrpos($f, '_', (strrpos($f, '_') - strlen($f) - 1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		return $instance->default_settings($key);
	 }
	}

	// -----------
	// Get Options
	// -----------
	if (!function_exists('NAMESPACE_get_options')) {
	 function NAMESPACE_get_options() {
		$f = __FUNCTION__; $namespace = substr($f, 0, strrpos($f, '_', (strrpos($f, '_') - strlen($f) - 1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		return $instance->options;
	 }
	}

	// -----------
	// Get Setting
	// -----------
	if (!function_exists('NAMESPACE_get_setting')) {
	 function NAMESPACE_get_setting($key, $filter=true) {
		$f = __FUNCTION__; $namespace = substr($f, 0, strrpos($f, '_', (strrpos($f, '_') - strlen($f) - 1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		return $instance->get_setting($key, $filter);
	 }
	}

	// --------------
	// Reset Settings
	// --------------
	if (!function_exists('NAMESPACE_reset_settings')) {
	 function NAMESPACE_reset_settings() {
		$f = __FUNCTION__; $namespace = substr($f, 0, strrpos($f, '_', (strrpos($f, '_') - strlen($f) - 1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		$instance->reset_settings();
	 }
	}

	// ---------------
	// Update Settings
	// ---------------
	if (!function_exists('NAMESPACE_update_settings')) {
	 function NAMESPACE_update_settings() {
		$f = __FUNCTION__; $namespace = substr($f, 0, strrpos($f, '_', (strrpos($f, '_') - strlen($f) - 1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		$instance->update_settings();
	 }
	}

	// ---------------
	// Delete Settings
	// ---------------
	if (!function_exists('NAMESPACE_delete_settings')) {
	 function NAMESPACE_delete_settings() {
		$f = __FUNCTION__; $namespace = substr($f, 0, strrpos($f, '_', (strrpos($f, '_') - strlen($f) - 1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		$instance->delete_settings();
	 }
	}

	// ---------------
	// Settings Header
	// ---------------
	if (!function_exists('NAMESPACE_settings_header')) {
	 function NAMESPACE_settings_header() {
		$f = __FUNCTION__; $namespace = substr($f, 0, strrpos($f, '_', (strrpos($f, '_') - strlen($f) - 1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		$instance->settings_header();
	 }
	}

	// -------------
	// Settings Page
	// -------------
	if (!function_exists('NAMESPACE_settings_page')) {
	 function NAMESPACE_settings_page() {
		$f = __FUNCTION__; $namespace = substr($f, 0, strrpos($f, '_', (strrpos($f, '_') - strlen($f) - 1)));
		$instance = $GLOBALS[$namespace.'_instance'];
		$instance->settings_page();
	 }
	}

}

// fully loaded
