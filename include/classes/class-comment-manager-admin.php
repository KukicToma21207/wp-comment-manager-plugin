<?php

class Comment_Manager_Admin {
    protected static $instance = null;

    protected function __construct() {
        
        if (! is_admin() || (is_admin() && wp_doing_ajax())) {
            $CM = Comment_Manager::instance();

			add_action('template_redirect', [$CM, 'check_comment_template']);
			add_action('cm_custom_comment_form', [$CM, 'render_comment_form']);

			add_filter('cm_comments_main_title', [$CM, 'main_title_filter']);
			add_filter('cm_comments_main_form_title', [$CM, 'main_form_title_filter'], 10, 2);
			add_filter('cm_comments_reply_form_title', [$CM, 'reply_form_title_filter'], 10, 2);
			add_filter('cm_comments_main_placeholder', [$CM, 'main_form_placeholder_filter'], 10, 2);
			add_filter('cm_comments_reply_placeholder', [$CM, 'reply_form_placeholder_filter'], 10, 2);
		}

		add_action('admin_enqueue_scripts', function () {

			//Add necessary scripts and styles
			wp_enqueue_style('cm_admin_style', CM_ASSETS_URL . 'css/cm-admin-style.css', [], "v_" . strtotime('now'));
			wp_enqueue_script('cm_admin_script', CM_ASSETS_URL . 'js/cm-admin-script.js', [], "v_" . strtotime('now'));
			wp_localize_script('cm_admin_script', 'cmAjax', ['ajaxurl' => admin_url('admin-ajax.php')]);
		});

        $this->init();
    }

    
	/**
	 * Returns singleton instance
	 */
	public static function instance(): Comment_Manager_Admin
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}


    /**
     * Init
     *
     */     
     public function init() {
        
		// Define admin area page where we can adjust settings
		if (is_admin() && ! wp_doing_ajax()) {

			//Define menu
			add_action('admin_menu', [$this, 'defineWPAdminPage']);
			add_action('admin_init', [$this, 'registerPluginSetting']);
		}
    }
    

    
	/**
	 * Define admin menu
	 */
	function defineWPAdminPage()
	{
		add_menu_page(
			'Comment Manager',
			'Comments Area',
			'manage_options',
			'comment_manager_admin',
			[$this, 'renderWPAdminPage'],
			'dashicons-feedback',
			99
		);
	}

    

	/**
	 * This will register all necesary setting for the plugin
	 */
	function registerPluginSetting()
	{
		register_setting('cm_plugin_options', 'cm_plugin_options', ["autoload" => false]);
		add_settings_section('plugin_main', 'Main settings', [$this, 'renderSettingSection'], 'plugin');
		add_settings_section('plugin_comments_settings', 'Comments settings', [$this, 'renderCommentSettingSection'], 'plugin');
		add_settings_section('plugin_page_settings', 'Page settings', [$this, 'renderPageSettingSection'], 'plugin');
	}


    
	/**
	 * Rendering main admin area content page
	 */
	function renderWPAdminPage()
	{
		include_once(CM_TEMPLATES_PATH . "admin/main-admin-page.php");
	}



	/**
	 * One section in admin settings
	 */
	function renderSettingSection()
	{
		include_once(CM_TEMPLATES_PATH . "admin/main-settings-section.php");
	}



	/**
	 * One section in admin settings
	 */
	function renderCommentSettingSection()
	{
		include_once(CM_TEMPLATES_PATH . "admin/comment-settings-section.php");
	}



	/**
	 * One section in admin settings
	 */
	function renderPageSettingSection()
	{
		include_once(CM_TEMPLATES_PATH . "admin/page-settings-section.php");
	}

}

//Start admin class
Comment_Manager_Admin::instance();