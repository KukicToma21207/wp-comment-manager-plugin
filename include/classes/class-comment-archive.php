<?php

require_once "class-comment-query.php";

class Comment_Archive {
    
    protected static Comment_Archive|null $instance = null;


    protected function __construct() {}

    public static function instance(): Comment_Archive {
        if ( self::$instance === null ) {

            self::$instance = new self();
        }

        return self::$instance;
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
	public function get_comments($commentID)
	{
		$postID = intval($_REQUEST['post_id']);
		$parentID = empty($_REQUEST['parent_id']) ? 0 : intval($_REQUEST['parent_id']);
		$filter = (empty($_REQUEST['filter']) || trim($_REQUEST['filter'])) == "" ? NULL : trim($_REQUEST['filter']);
        $CM = Comment_Manager::instance();
        $CQ = Comment_Query::instance();

		$customSettings = $CM->getCustomOptionsForPost($postID);
		$maxComments = intval($customSettings['cm_option_comments_per_page'] ?? 6);

		$commentArgs = $CQ->get_query_args($postID, $customSettings['settings']->type, $customSettings['settings']->category, $filter, true);

		$total = get_comments($commentArgs);

		$lastPage = ($total % $maxComments) > 0 ? intval(round($total / $maxComments, PHP_ROUND_HALF_DOWN) + 1) : intval($total / $maxComments);
		$page = empty($_REQUEST['page']) || intval($_REQUEST['page']) == -1 ? $lastPage : intval($_REQUEST['page']);

		$queryArgs = $CQ->get_query_args($postID, $customSettings['settings']->type, $customSettings['settings']->category, $filter, false, $parentID, $maxComments, $page);

		if (empty($commentID)) {
			$comments = get_comments($queryArgs);
			$comments = ($parentID === 0 ? array_reverse($comments) : $comments);
		} else {
			$comments = [get_comment($commentID)];
		}

		$result = "";

		foreach ($comments as $single) {
			$args = [
				'comment' => $single,
				'parent_id' => $parentID,
			];

			$cmComment = $CM->render_single_comment($args);

			if ($parentID == 0) {
				$title = $customSettings['settings']->titles->reply_form ?? ""; // 'Напиши одговор за <a class="cm-form-title-comment-link" href="cm-comment_' . $single->comment_ID . '">' . get_comment_author() . '</a>';
				$form = $CM->render_comment_form(['comment_id' => $single->comment_ID, 'post_id' => $postID, 'parent_id' => $parentID, 'title' => $title], true);

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
}