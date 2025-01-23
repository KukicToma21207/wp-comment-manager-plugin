<?php

/**
 * Render per page settings
 */
add_settings_field('cm_option_page_settings', 'Single Page', 'cm_option_page_settings', 'plugin', 'plugin_page_settings', ["commentManager" => Comment_Manager::instance()]);
function cm_option_page_settings($args)
{
    $commentManager = $args['commentManager'];
    $addonsList = $commentManager->getAddonsList();
    $pages = get_posts([
        'post_type' => 'page',
        'post_status' => 'publish',
        'comment_status' => 'open'
    ]);
    $postTypes = get_post_types_by_support('comments');    
    $options = Comment_Manager::instance()->get_options();
    $option = ((empty($options['cm_option_page_settings']) || $options['cm_option_page_settings'] == '') ? "" : $options['cm_option_page_settings']);
    $commentTypes = $options['cm_comment_types_option'] ? json_decode($options['cm_comment_types_option']) : "";
    $commentCategories = $options['cm_comment_category_option'] ? json_decode($options['cm_comment_category_option']) : "";
    $perPageSettings = (empty($option) ? [] : json_decode($option));
    $pageIDs = [];

    foreach ($perPageSettings as $pageSettings) {
        $pageIDs[] = $pageSettings->id;
    }

?>
    <input id='cm_option_page_settings' name='cm_plugin_options[cm_option_page_settings]' value='<?php echo $option; ?>' type="text" hidden>

    <h3 class="cm-options-title">Pages</h3>
    <ul class="cm-page-list">
        <?php $addedPages = 0; ?>
        <?php foreach ($pages as $page): ?>
            <?php if (in_array($page->ID, $pageIDs)) {
                continue;
            } else {
                $addedPages++;
            } ?>

            <li class="cm-page-list-item" data-post_id="<?php echo $page->ID; ?>" data-post_title="<?php echo $page->post_title; ?>">
                <?php echo $page->post_title; ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <section class="cm-per-page-settings-section">
        <?php echo (empty($pages) ? "<p class='cm-no-pages-message'>There is no pages with comments enabled.</p>" : (!$addedPages ? "<p class='cm-no-pages-message'>There is no more pages with comments enabled.</p>" : "")); ?>
        
        <div class="cm-per-page-settings-wrapper">
            <?php foreach ($perPageSettings as $page): ?>
                <?php
                ob_start();
                include "single-page-settings.php";
                echo ob_get_clean();
                ?>
            <?php endforeach; ?>
        </div>
    </section>
<?php
}



/**
 * Render post type settings
 */
add_settings_field('cm_option_post_type_settings', 'Single Post-Type', 'cm_option_post_type_settings', 'plugin', 'plugin_page_settings', ["commentManager" => Comment_Manager::instance()]);
function cm_option_post_type_settings($args)
{
    $commentManager = $args['commentManager'];
    $addonsList = $commentManager->getAddonsList();
    $postTypes = get_post_types_by_support('comments');    
    $options = get_option('cm_plugin_options');
    $option = ((empty($options['cm_option_post_type_settings']) || $options['cm_option_post_type_settings'] == '') ? "" : $options['cm_option_post_type_settings']);
    $commentTypes = $options['cm_comment_types_option'] ? json_decode($options['cm_comment_types_option']) : "";
    $commentCategories = $options['cm_comment_category_option'] ? json_decode($options['cm_comment_category_option']) : "";
    $postTypeSettings = (empty($option) ? [] : json_decode($option));
    $typeNames = [];

    foreach ($postTypeSettings as $pt) {
        if($pt->title == "page" || $pt->title == "attachment") continue;
        $typeNames[] = $pt->title;
    }

?>
    <input id='cm_option_post_type_settings' name='cm_plugin_options[cm_option_post_type_settings]' value='<?php echo $option; ?>' type="text" hidden>

    <h3 class="cm-options-title">Post types</h3>
    <ul class="cm-post-list">
        <?php $addedPosts = 0; ?>
        <?php foreach ($postTypes as $postType): ?>
            <?php if (in_array($postType, $typeNames) || $postType == "page" || $postType == "attachment") {
                continue;
            } else {
                $addedPosts++;
            } ?>

            <li class="cm-post-list-item" data-post_id="<?php echo $postType; ?>" data-post_title="<?php echo $postType; ?>">
                <?php echo $postType; ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <section class="cm-per-post-settings-section">
        <?php echo (empty($postTypes) ? "<p class='cm-no-posts-message'>There is no post types with comments enabled.</p>" : (!$addedPosts ? "<p class='cm-no-posts-message'>There is no more post types with comments enabled.</p>" : "")); ?>
        
        <div class="cm-per-post-settings-wrapper">
            <?php foreach ($postTypeSettings as $postType): ?>
                <?php 
                ob_start();
                include "single-post-type-settings.php";
                echo ob_get_clean();
                ?>
            <?php endforeach; ?>
        </div>
    </section>
<?php
}
