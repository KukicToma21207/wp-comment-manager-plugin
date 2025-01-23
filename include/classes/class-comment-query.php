<?php
/**
 * This will build query args for comments search
 */
class Comment_Query {

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