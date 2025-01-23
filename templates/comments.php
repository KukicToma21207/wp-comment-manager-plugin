<?php

/**
 * Custom comments template
 */
if (!comments_open()):
    return;
endif;

/**
 * Lets call initial form for the comments
 */
?>
<div class="cm-main-form-wrapper">
    <?php do_action('cm_custom_comment_form', ['title' => 'Питање', 'comment_id' => 0, 'post_id' => $post->ID]); ?>
</div>

<div class="cm-comments-section hidden">
    <div class="cm-comments-title">
        <h2><?php echo apply_filters('cm_comments_main_title', "Komentari"); ?></h2>

        <?php if (current_user_can('moderate_comments')): ?>
            <div class="cm-filter-wrapper">
                <?php
                $options = Comment_Manager::instance()->get_options();
                $subCategories = (!empty($options['cm_comment_sub_category_option']) && Trim($options['cm_comment_sub_category_option']) != "" ? json_decode($options['cm_comment_sub_category_option']) : []);

                $meta = get_comment_meta(intval($comment->comment_ID), 'cm_subcategory', true);
                $subCategory = (empty($meta) || trim($meta) == "" ? "Без категорије" : $meta);
                ?>

                <div class="cm-filter-item">
                    <select id="select-subcategory-filter" class="selected-subcategory-filter">
                        <option value="">Без категорије</option>

                        <?php foreach ($subCategories as $category): ?>

                            <option value="<?php echo $category; ?>">
                                <?php echo $category; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="cm-filter-item">
                    <input id="cm-comments-filter-btn" class="cm-comment-filter" type="button" value="Filter" data-post_id="<?php echo $post->ID; ?>" />
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div id="cm-comment_0" class="cm-comments-main-container" data-post_id="<?php echo get_the_ID(); ?>">
        <ul class="cm-child-list">

        </ul>

        <div id="cm-pagination-container">

        </div>
    </div>
</div>