<?php
/*
Plugin Name: Comment Manager
Description: WordPress plugin for comment customization
Version: 1.0.0
Author: Tomislav Kukic
*/


if (! defined('ABSPATH'))
	exit; // Exit if accessed directly


define("CM_ROOT_PATH", plugin_dir_path(__FILE__));
define("CM_TEMPLATES_PATH", plugin_dir_path(__FILE__) . 'templates/');
define("CM_ASSETS_PATH", plugin_dir_path(__FILE__) . 'assets/');
define("CM_ASSETS_URL", plugin_dir_url(__FILE__) . 'assets/');
define("CM_ADDONS_PATH", plugin_dir_path(__FILE__) . 'addons/');
define("CM_ADDONS_URL", plugin_dir_url(__FILE__) . 'addons/');

require_once "include/classes/class-comment-manager.php";
require_once "include/classes/class-comment-manager-admin.php";

if (is_admin() && ! wp_doing_ajax()) {
	$cm = Comment_Manager::instance();
	$options = $cm->get_options();

	if (! empty($options['cm_option_use_custom_template']) && $options['cm_option_use_custom_template'] == '1') {

		//Adding additional filters for comments table in admin panel
		require_once "include/admin-comments-extended.php";
	}
}
