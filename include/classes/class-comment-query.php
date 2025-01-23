<?php
/**
 * This will build query args for comments search
 */
class Comment_Query {

    protected static Comment_Query|null $instance = null;

    public static function instance(): Comment_Query|null {
        if (self::$instance === null) {
            self::$instance = new Comment_Query();
        }
        return self::$instance;
    }

    protected function __construct() {}


    public function get_query_args($postID,  $type, $category, $subCategory, $count = false, $parentID = null, $maxComments = -1, $page = -1) {
        $commentArgs = [
			'post_id' => $postID,
			'hierarchical' => 'threaded',
			'order' => 'ASC',
		];

        if ($count) {
            $commentArgs += ['count' => $count ];
        }

        if ($parentID) {
			$commentArgs +=
				[
					"parent" => $parentID
				];
		} else if(!$count) {
			$commentArgs +=
				[
					'number' => $maxComments,
					'paged' => $page,
				];
		}

		$customizedOptions = [];

		if ($type) {
			$customizedOptions += [
				'type' => $type,
			];
		}

		if (! empty($category)) {
			$customizedOptions += [
				'meta_query' => [
					[
						'key' => 'cm_category',
						'value' => $category,
					]
				]
			];

			if ($subCategory) {
				$customizedOptions['meta_query'][] =					
				[
					'key' => 'cm_subcategory',
					'value' => $subCategory,
				];
			}
		} else {
			if ($subCategory) {
				$customizedOptions += [
					'meta_query' => [
						[
							'key' => 'cm_subcategory',
							'value' => $subCategory,
						]
					]
				];
			}
		}

		$commentArgs += $customizedOptions;

        return $commentArgs;
    }
}