<?php

add_filter('admin_comment_types_dropdown', function ($param) {

    //Read options that we use in plugin
    $options = Comment_Manager::instance()->get_options();
    $allTypes = json_decode($options['cm_comment_types_option']);

    //Apply comment types that we use so we can filter by them
    $cmTypes = [];
    foreach ($allTypes as $type) {
        $typeValue = str_replace(" ", "_", strtolower($type));
        $cmTypes[$typeValue] = $type;
    }

    $param += $cmTypes;

    return $param;
});



add_action("restrict_manage_comments", function () {
    $selectedCategory = $_REQUEST['category'];

    //Read options that we use in plugin
    $options = Comment_Manager::instance()->get_options();
    $allCategories = json_decode($options['cm_comment_category_option']);

?>
    <select name="category" id="">
        <option value="">All comment categories</option>

        <?php foreach ($allCategories as $category): ?>
            <?php $categoryValue = str_replace(" ", "_", strtolower($category)); ?>
            <option value="<?php echo $categoryValue; ?>" <?php echo ($selectedCategory == $categoryValue ? "selected='selected'" : ""); ?>><?php echo $category; ?></option>
        <?php endforeach; ?>
    </select>
<?php
});



add_filter("comments_list_table_query_args", function ($args) {
    if (empty($_REQUEST['category'])) {
        return $args;
    }

    $tmpCategory = sanitize_text_field($_REQUEST['category']);
    $args['meta_query'] = [
        [
            'key' => 'cm_category',
            'value' => $tmpCategory
        ]
    ];

    return $args;
});
