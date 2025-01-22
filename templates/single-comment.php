<?php

global $comment;
$comment = $args['comment'];

?>
<li class="cm-comment" id="cm-comment_<?php echo $comment->comment_ID; ?>">

    <div class="cm-comment-body">

        <div class="cm-comment-avatar">
            <?php echo get_avatar(get_current_user_id(), 69); ?>
        </div>

        <div class="cm-comment-main">
            <div class="cm-comment-meta">
                <cite>
                    <?php custom_svg('reply', 'cm-reply-icon'); ?>
                    <?php comment_author(); ?>
                </cite>
                <a class="cm-comment-date" href="#">
                    <?php
                    comment_date('d.m.Y');
                    echo "&nbsp;|&nbsp;";
                    comment_time('H:i');
                    ?>
                </a>
                <?php
                if (current_user_can('moderate_comments')) {
                ?>

                    <a class="cm-comment-edit-link" href="<?php echo get_edit_comment_link(); ?>">(Izmeni)</a>

                    <?php
                    if (empty($args['parent_id']) || $args['parent_id'] == 0) {

                        $options = Comment_Manager::instance()->getOptions();
                        $subCategories = (!empty($options['cm_comment_sub_category_option']) && Trim($options['cm_comment_sub_category_option']) != "" ? json_decode($options['cm_comment_sub_category_option']) : []);
                        $meta = get_comment_meta(intval($comment->comment_ID), 'cm_subcategory', true);
                        $subCategory = (empty($meta) || trim($meta) == "" ? "Без категорије" : $meta);
                    ?>
                        <span id="cm-comment-subcategory-<?php echo $comment->comment_ID; ?>"><?php echo $subCategory; ?></span>

                        <select class="select-subcategory" data-comment_id="<?php echo $comment->comment_ID; ?>" id="select-subcategory-<?php echo $comment->comment_ID; ?>">
                            <option value="">Измени категорију</option>
                            <option value="delete">Обриши категорију</option>

                            <?php foreach ($subCategories as $category): ?>
                                <option value="<?php echo $category; ?>">
                                    <?php echo $category; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                <?php
                    }
                }
                ?>
            </div>

            <div class="cm-comment-text">

                <?php
                comment_text();
                ?>
            </div>

            <div class="cm-comment-additional-info">
                <?php echo "<p id='cm-comment-responces-" . $comment->comment_ID . "' class='cm-comment-responces'>" . custom_svg('comment', 'cm-comment-responces', false) . " <span>" . get_comments(['parent' => $comment->comment_ID, 'count' => true]) . "</span></p>"; ?>

                <?php do_action("cm_comment_additional_info", ['comment_id' => $comment->comment_ID, 'post_id' => $comment->comment_post_ID]); ?>
            </div>
        </div>
    </div>

    <?php if (empty($args['parent_id']) || $args['parent_id'] == 0): ?>

        <div class="cm-comment-child-wrapper cm-hidden">
            <ul class="cm-child-list">

            </ul>
        </div>

        {{comment_form}}

    <?php endif; ?>



    <?php
    $userClass = "";
    $clickedTitle = "Затвори одговоре";

    if (is_user_logged_in()) {
        $showMoreTitle = "Напиши одговор";
    } else {
        $showMoreTitle = "Прикажи одговоре";
    }
    ?>

    <?php if (empty($args['parent_id']) || $args['parent_id'] == 0): ?>
        <div class="cm-reply-link-wrapper">
            <a href="#" class="cm-show-reply"
                data-original_title="<?php echo $showMoreTitle; ?>"
                data-clicked_title="<?php echo $clickedTitle; ?>"
                data-comment_id="<?php echo $comment->comment_ID; ?>">
                <?php echo $showMoreTitle; ?>
            </a>
            <?php custom_svg('arrow-d', 'cm-arrow-d'); ?>
        </div>
    <?php endif; ?>

</li>