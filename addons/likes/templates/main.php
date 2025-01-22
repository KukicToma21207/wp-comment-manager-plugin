
<div class="cm-likes-wrapper">
    <?php if (is_user_logged_in()): ?>
        <?php $likedClass = ($args['user_liked'] ? " liked" : ""); ?>
        <?php $dislikedClass = ($args['user_disliked'] ? " disliked" : ""); ?>

        <a href="#" class="cm-comment-likes-count<?php echo $likedClass; ?>" data-action="like" data-comment_id="<?php echo $args['comment_id']; ?>">
            <?php custom_svg('like', 'cm-comment-likes'); ?><span><?php echo $args['likes']; ?></span>
        </a>
        <a href="#" class="cm-comment-dislikes-count<?php echo $dislikedClass; ?>" data-action="dislike" data-comment_id="<?php echo $args['comment_id']; ?>">
            <?php custom_svg('dislike', 'cm-comment-dislikes'); ?><span><?php echo $args['dislikes']; ?></span>
        </a>
    <?php else: ?>
        <p class="cm-comment-likes-count"><?php custom_svg('like', 'cm-comment-likes'); ?>
        <span><?php echo $args['likes']; ?></span>
    </p>
        <p class="cm-comment-dislikes-count"><?php custom_svg('dislike', 'cm-comment-dislikes'); ?>
        <span><?php echo $args['dislikes']; ?></span>
    </p>
    <?php endif; ?>
</div>
