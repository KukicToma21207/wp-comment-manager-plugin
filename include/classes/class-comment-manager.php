<?php

require_once CM_ROOT_PATH . "include/helpers/helpers.php";


/**
 * class CommentsManagee
 * 
 * Description:
 * Allows in dept management of the comments for various post types
 */
class Comment_Manager
{

	// commentCategories are used as comment_type and allow filtering in comments section in ap admin area
	protected $commentCategories = [];


	// those are users that are put to the ban list and it is forbiden for them to leave comments, like comments etc.
	// everything that they commented and it is approved it will be visible
	protected $bannedUsers = [];


	// Hold any necessary options for this plugin
	protected $options = [];


	// Hold any addons loaded with autoloader
	protected $addons = [];


	//Singleton instance
	protected static $instance = null;

	/**
	 * Default constructor
	 */
	protected function __construct()
	{

		//===== Define ajax calls =====
		add_action("wp_ajax_cm_get_comments", [$this, "cmGetComments"]);
		add_action("wp_ajax_nopriv_cm_get_comments", [$this, "cmGetComments"]);

		add_action("wp_ajax_cm_save_comment", [$this, "cmSaveComment"]);
		add_action("wp_ajax_nopriv_cm_save_comment", [$this, "cmSaveComment"]);

		add_action("wp_ajax_cm_comment_update", [$this, "cmCommentsUpdate"]);
		add_action("wp_ajax_nopriv_cm_comment_update", [$this, "cmCommentsUpdate"]);

		add_action("wp_ajax_cm_comment_update_subcategory", [$this, "cmCommentsUpdateSubcategory"]);
		add_action("wp_ajax_nopriv_cm_comment_update_subcategory", [$this, "cmCommentsUpdateSubcategory"]);
		//===== END Ajax calls =====

		add_action('wp_enqueue_scripts', function () {

			//Add necessary scripts and styles
			wp_enqueue_style('cm_style', CM_ASSETS_URL . 'css/cm-style.css', [], "v_" . strtotime('now'));
			wp_enqueue_script('cm_script', CM_ASSETS_URL . 'js/cm-script.js', ['jquery'], "v_" . strtotime('now'));
			wp_localize_script('cm_script', 'cmAjax', ['ajaxurl' => admin_url('admin-ajax.php')]);
		});

		$this->init();
	}


	/**
	 * Returns singleton instance
	 */
	public static function instance()
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Main init function for the class
	 */
	function init()
	{
		//Load all options
		$this->options = get_option('cm_plugin_options');

		//Init all addons
		$this->loadAddons();
	}


	/**
	 * Load all addons in the folder
	 */
	function loadAddons()
	{
		$allFolders = glob(CM_ADDONS_PATH . "*", GLOB_ONLYDIR);

		foreach ($allFolders as $folder) {
			$fileName = basename($folder);
			$file = $folder . "/" . $fileName . ".php";

			if (file_exists($file)) {
				$this->addons[] = $fileName;
				$commentManager = $this;
				include $file;
			}
		}
	}


	/**
	 * @return array List of addons for the comment plugin
	 */
	function getAddonsList()
	{
		return $this->addons;
	}


	/**
	 * Customized options for comment manager plugin
	 */
	function getOptions()
	{
		return $this->options;
	}


	/**
	 * We must integrate in comments templare scheme
	 */
	function checkCommentTemplate()
	{
		if (! empty($this->options) && is_array($this->options) && $this->options['cm_option_use_custom_template'] == '1') {

			//New version of WP (With blocks)
			add_filter('pre_render_block', [$this, 'overrideCommentsBlock'], 10, 2);
			add_filter('comments_template', [$this, 'renderCustomTemplate'], 20);
		}
	}


	/**
	 * We need to override comment blcok render for new WP versions (after 2023)
	 */
	function overrideCommentsBlock($pre_render, $parsed_block)
	{
		if ($parsed_block['blockName'] === 'core/comments') {
			ob_start();
			comments_template();
			return ob_get_clean();
		}
	}


	/**
	 * Render our custom template for comments instead of default WP and active theme
	 */
	function renderCustomTemplate()
	{
		return CM_TEMPLATES_PATH . 'comments.php';
	}


	/**
	 * Modifies title based on the custom options for active page/post
	 */
	function cmMainTitleFilter($title)
	{
		$options = $this->getCustomOptionsForPost(get_the_ID());

		if (! empty($options['settings']) && $options['settings']->titles) {
			if ($options['settings']->titles->main != "") {
				return $options['settings']->titles->main;
			}
		}

		return $title;
	}


	/**
	 * Modifies title based on the custom options for active page/post
	 */
	function cmMainFormTitleFilter($title, $args)
	{
		$options = $this->getCustomOptionsForPost($args['post_id']);

		if (! empty($options['settings']) && $options['settings']->titles) {
			return $options['settings']->titles->main_form ?? "";
		}

		return $title;
	}


	/**
	 * Modifies title based on the custom options for active page/post
	 */
	function cmReplyFormTitleFilter($title, $args)
	{
		$options = $this->getCustomOptionsForPost($args['post_id']);

		if (! empty($options['settings']) && $options['settings']->titles) {
			return $options['settings']->titles->reply_form ?? "";
		}

		return $title;
	}


	/**
	 * Modifies title based on the custom options for active page/post
	 */
	function cmMainFormPlaceholderFilter($title, $args)
	{
		$options = $this->getCustomOptionsForPost($args['post_id']);

		if (! empty($options['settings']) && $options['settings']->titles) {
			return $options['settings']->titles->main_placeholder ?? "";
		}

		return $title;
	}


	/**
	 * Modifies title based on the custom options for active page/post
	 */
	function cmReplyFormPlaceholderFilter($title, $args)
	{
		$options = $this->getCustomOptionsForPost($args['post_id']);

		if (! empty($options['settings']) && $options['settings']->titles) {
			return $options['settings']->titles->reply_placeholder ?? "";
		}

		return $title;
	}



	/**
	 * Reads customization options based on the post type
	 * 
	 * @param integer|null $postID Post ID for which to read options for, if null all options will return
	 * 
	 * @return array Set of customization options for given post ID
	 */
	function getCustomOptionsForPost($postID = null)
	{
		$result = [];

		$options = get_option('cm_plugin_options');
		if (empty($postID)) {
			return $options;
		}

		$currentPostType = get_post_type($postID);
		$currentPostIsPage = ($currentPostType == "page");

		$result['cm_option_comments_per_page'] = $options['cm_option_comments_per_page'];

		if ($currentPostIsPage) {
			foreach (json_decode($options['cm_option_page_settings']) as $pageSettings) {
				if ($pageSettings->id == $postID) {
					$result['settings'] = $pageSettings;
					continue;
				}
			}
		} else {
			if (comments_open($postID)) {
				foreach (json_decode($options['cm_option_post_type_settings']) as $pageSettings) {

					if ($pageSettings->id == $currentPostType) {
						$result['settings'] = $pageSettings;
						continue;
					}
				}
			}
		}

		return $result;
	}


	/**
	 * Ajax call that will update category and type of existing comments
	 */
	function cmCommentsUpdate()
	{
		$type = $_REQUEST['type'];
		$postID = 0;
		$postType = "";

		echo "Started";
		ob_flush();
		flush();

		if ($type == "page") {
			$postID = $_REQUEST['post_id'];
			$type = strtolower($_REQUEST['comment_type'] ?? "comment");
			$category = $_REQUEST['comment_category'] ?? NULL;

			$comments = get_comments([
				'post_id' => $postID
			]);

			foreach ($comments as $index => $comment) {
				$args = [
					'comment_ID' => $comment->comment_ID,
					'comment_type' => $type,
				];

				wp_update_comment($args);

				if (! empty($category)) {
					update_comment_meta($comment->comment_ID, 'cm_category', $category);
				} else {
					delete_comment_meta($comment->comment_ID, 'cm_category');
				}

				echo "," . ($index + 1);
				ob_flush();
				flush();
			}
		} else {
			$postType = $_REQUEST['post_type'];
			$type = strtolower($_REQUEST['comment_type'] ?? "comment");
			$category = $_REQUEST['comment_category'] ?? NULL;

			$posts = get_posts([
				'post_type' => $postType,
			]);

			foreach ($posts as $post) {
				$postID = $post->ID;

				$comments = get_comments([
					'post_id' => $postID
				]);

				foreach ($comments as $index => $comment) {
					$args = [
						'comment_ID' => $comment->comment_ID,
						'comment_type' => $type,
					];

					wp_update_comment($args);

					if (! empty($category)) {
						update_comment_meta($comment->comment_ID, 'cm_category', $category);
					} else {
						delete_comment_meta($comment->comment_ID, 'cm_category');
					}

					echo "," . ($type);
					ob_flush();
					flush();
				}
			}
		}

		die();
	}


	/**
	 * Update comment subcategory
	 */
	public function cmCommentsUpdateSubcategory()
	{
		$result = [
			"type" => "success",
			"data" => "",
			"message" => "success",
		];

		$commentID = (empty($_POST['comment_id']) ? NULL : intval($_POST['comment_id']));
		$subCategory = (empty($_POST['subcategory']) ? NULL : $_POST['subcategory']);

		if (! $commentID) {
			$result['type'] = "error";
			$result['message'] = "Missing data";

			echo json_encode($result);
			die();
		}

		if (! $subCategory || $subCategory == "delete") {
			$result['type'] = "delete";

			delete_comment_meta($commentID, 'cm_subcategory');
		} else {
			update_comment_meta($commentID, 'cm_subcategory', $subCategory);
		}

		echo json_encode($result);
		die();
	}


	/**
	 * Ajax function to retrive all comment
	 * 
	 * @param integer $comment_id ID of a comment to be returned
	 * 
	 * @return void Echo the result for ajax return
	 * 
	 * @see $_REQUEST If pagination is set it will retrive required page
	 */
	public function cmGetComments($comment_id = null)
	{
		$postID = intval($_REQUEST['post_id']);
		$parentID = (empty($_REQUEST['parent_id']) ? 0 : intval($_REQUEST['parent_id']));
		$filter = (empty($_REQUEST['filter']) || trim($_REQUEST['filter']) == "" ? NULL : trim($_REQUEST['filter']));

		$customSettings = $this->getCustomOptionsForPost($postID);
		$maxComments = intval($customSettings['cm_option_comments_per_page'] ?? 6);

		$commentArgs = [
			'post_id' => $postID,
			'hierarchical' => 'threaded',
			'count' => true
		];

		$customizedOptions = [];

		if ($customSettings['settings']->type) {
			$customizedOptions += [
				'type' => $customSettings['settings']->type,
			];
		}

		if (! empty($customSettings['settings']->category)) {
			$customizedOptions += [
				'meta_query' => [
					[
						'key' => 'cm_category',
						'value' => $customSettings['settings']->category,
					]
				]
			];

			if ($filter) {
				$customizedOptions['meta_query'][] =					
				[
					'key' => 'cm_subcategory',
					'value' => $filter,
				];
			}
		} else {
			if ($filter) {
				$customizedOptions += [
					'meta_query' => [
						[
							'key' => 'cm_subcategory',
							'value' => $filter,
						]
					]
				];
			}
		}

		$commentArgs += $customizedOptions;

		$total = get_comments($commentArgs);

		$lastPage = (($total % $maxComments) > 0 ? intval(round($total / $maxComments, PHP_ROUND_HALF_DOWN) + 1) : intval($total / $maxComments));
		$page = (empty($_REQUEST['page']) || intval($_REQUEST['page']) == -1 ? $lastPage : intval($_REQUEST['page']));

		$queryArgs = [
			'post_id' => $postID,
			'hierarchical' => 'threaded',
			'order' => 'ASC',
		];

		if ($filter) {
			$queryArgs += [
				'meta_query' => [
					[
						'key' => 'cm_subcategory',
						'value' => $filter,
					]
				]
			];
		}

		if ($parentID) {
			$queryArgs +=
				[
					"parent" => $parentID
				];
		} else {
			$queryArgs +=
				[
					'number' => $maxComments,
					'paged' => $page,
				];
		}

		$queryArgs += $customizedOptions;

		if (empty($comment_id)) {
			$comments = get_comments($queryArgs);
			$comments = ($parentID === 0 ? array_reverse($comments) : $comments);
		} else {
			$comments = [get_comment($comment_id)];
		}

		$result = "";

		foreach ($comments as $single) {
			$args = [
				'comment' => $single,
				'parent_id' => $parentID,
			];

			$cmComment = $this->getSingleComment($args);

			if ($parentID == 0) {
				$newTitle = $customSettings['settings']->titles->reply_form ?? ""; // 'Напиши одговор за <a class="cm-form-title-comment-link" href="cm-comment_' . $single->comment_ID . '">' . get_comment_author() . '</a>';
				$form = $this->getCustomCommentForm(['comment_id' => $single->comment_ID, 'post_id' => $postID, 'parent_id' => $parentID, 'title' => $newTitle]);

				$final = str_replace("{{comment_form}}", $form, $cmComment);
			} else {
				$final = $cmComment;
			}

			$result .= $final;
		}

		$pagination = ["current" => $page, "total" => $lastPage];

		echo json_encode(["type" => "success", "data" => $result, "pagination" => $pagination]);
		die();
	}


	/**
	 * Render custom comment form
	 */
	function customCommentForm($args)
	{
		echo $this->getCustomCommentForm($args);
	}


	/**
	 * Custom comment form
	 * 
	 * @param array Parameters for single custom comment form
	 * 
	 * @return string Rendered custom comment form
	 */
	protected function getCustomCommentForm($args): string
	{
		$result = NULL;

		ob_start();
		include(CM_TEMPLATES_PATH . "comment-form.php");
		$result = ob_get_clean();

		return $result;
	}


	/**
	 * Single comment
	 * 
	 * @param array Parameters
	 * 
	 * @return string Rendered content for single comment
	 */
	protected function getSingleComment($args): string
	{
		$result = "";

		ob_start();
		include(CM_TEMPLATES_PATH . "single-comment.php");
		$result = ob_get_clean();

		return $result;
	}


	/**
	 * Custom save function to enable comment types and comment groups
	 */
	public function cmSaveComment()
	{
		$postID = intval($_REQUEST['post_id']);
		$parentID = (empty($_REQUEST['parent_id']) ? 0 : intval($_REQUEST['parent_id']));
		$message = (empty($_REQUEST['message']) ? "" : sanitize_text_field($_REQUEST['message']));
		$options = $this->getCustomOptionsForPost($postID);
		$result = [];

		if (is_user_logged_in()) {
			$user = get_user_by('id', get_current_user_id());
			$comment_author = $user->display_name;
			$comment_author_email = $user->user_email;
		} else {
			$result = ["type" => "failed", "data" => [], "message" => "You have to be logged in to post a comment."];
			return json_encode($result);
		}

		if (empty($message) || trim($message) === "") {
			$result = ["type" => "failed", "data" => [], "message" => "You can't post an empty comment."];
			return json_encode($result);
		}

		$comment_post_ID = $postID;
		$comment_author_url = '';
		$comment_content = $message;
		$comment_agent = 'NIS Portal';
		$comment_type = $options['settings']->type ?? "comment";
		$comment_parent = $parentID;
		$comment_approved = 1;
		$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_agent', 'comment_type', 'comment_parent', 'comment_approved');

		$commentdata = apply_filters("cm-bafore-comment-save", $commentdata);

		$comment_id = wp_insert_comment($commentdata);

		if (! $comment_id) {
			$result = ["type" => "failed", "data" => [], "message" => "Comment failed."];
			return json_encode($result);
		}

		//Add additional comment meta if needed
		if (! empty($options['settings']->category)) {
			update_comment_meta($comment_id, 'cm_category', $options['settings']->category);
		}

		$result = ["type" => "success", "data" => ["comment" => $this->cmGetComments($comment_id)], "message" => "Comment saved successfully."];
		return json_encode($result);
	}
}



/**
 * Let's initiate everything now!
 */
add_action('plugin_loaded', ['Comment_Manager', 'instance'], 30, 1);
