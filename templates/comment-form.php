<?php
if (!is_user_logged_in()):
?>
    <div class="cm-user-must-login">
        <p>Morate biti ulogovani da ostavite komentar.</p>
    </div>
<?php
    return;
endif;

global $post;
$hidden = $args['comment_id'] == 0 ? "" : "cm-hidden cm-deep";

?>
<form class="cm-comment-form <?php echo $hidden; ?>" id="cm-comment-form-<?php echo ($args["comment_id"] ?? 0); ?>">
    <?php if (intval($args['comment_id']) == 0): ?>

        <p class="medium"><?php echo apply_filters('cm_comments_main_form_title', $args['title'], ['post_id' => $args["post_id"]]); ?></p>
        <textarea name="comment" class="cm-comment-text" cols="45" rows="10" placeholder="<?php echo apply_filters('cm_comments_main_placeholder', 'Odgovori', ['post_id' => $args["post_id"]]); ?>"></textarea>
    <?php else: ?>

        <p class="medium"><?php echo apply_filters('cm_comments_reply_form_title', $args['title'], ['post_id' => $args["post_id"]]); ?></p>
        <textarea name="comment" class="cm-comment-text" cols="45" rows="10" placeholder="<?php echo apply_filters('cm_comments_reply_placeholder', 'Odgovori', ['post_id' => $args["post_id"]]); ?>"></textarea>
    <?php endif; ?>

    <div class="cm-additional-comment-fields">
        <?php do_action("cm_after_comment_form_fields", ['comment_id' => $args["comment_id"], 'post_id' => $args["post_id"]]); ?>
    </div>

    <p class="form-submit">
        <input type="submit" class="submit" data-parent_id="<?php echo $args["comment_id"]; ?>" data-post_id="<?php echo $args["post_id"]; ?>" value="Пошаљи">
        <input type="hidden" class="cm-comment_post_id" value="<?php echo $args["post_id"]; ?>">
        <input type="hidden" class="cm-comment_parent" value="<?php echo $args['comment_id']; ?>">
    </p>
</form>
<?php
