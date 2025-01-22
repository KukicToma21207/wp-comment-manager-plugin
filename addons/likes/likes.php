<?php

define("CM_ADDON_LIKES_VER", "1.0.2");


add_action("wp_enqueue_scripts", "load_like_scripts");
function load_like_scripts() {
    wp_enqueue_script("cm-addon-likes-script", CM_ADDONS_URL . "likes/assets/js/script.js", [], CM_ADDON_LIKES_VER);
    wp_enqueue_style("cm-addon-likes-style", CM_ADDONS_URL . "likes/assets/css/style.css", [], CM_ADDON_LIKES_VER);
}



add_action("cm_comment_additional_info", "show_likes");
function show_likes($args) {
    //We need to determine if this page/post should display likes addon
    $options = Comment_Manager::instance()->getCustomOptionsForPost($args['post_id']);
    if(!empty($options['settings']) && !in_array("likes", $options['settings']->addons)){
        return;
    }

    $likes = intval(get_comment_meta($args['comment_id'], 'likes', true));
    $likeUsers = get_comment_meta($args['comment_id'], 'like_users', true);
    $parsedUsersL = (!empty($likeUsers) && trim($likeUsers) != "" ? explode(',', $likeUsers) : [] );
    $userLiked = (!is_user_logged_in() ? false : in_array(get_current_user_id(), $parsedUsersL));
    
    $dislikes = intval(get_comment_meta($args['comment_id'], 'dislikes', true));
    $dislikeUsers = get_comment_meta($args['comment_id'], 'dislike_users', true);
    $parsedUsersDL = (!empty($dislikeUsers) && trim($dislikeUsers) != "" ? explode(',', $dislikeUsers) : [] );
    $userDisliked = (!is_user_logged_in() ? false : in_array(get_current_user_id(), $parsedUsersDL));

    $args += [
        'likes' => $likes,
        'dislikes' => $dislikes,
        'user_liked' => $userLiked,
        'user_disliked' => $userDisliked,
    ];

    ob_start();
    include "templates/main.php";
    echo ob_get_clean();
}


add_action("wp_ajax_cm_like", "cmLikeComment");
add_action("wp_ajax_nopriv_cm_like", "cmLikeComment");
function cmLikeComment() {
    $commentID = intval($_REQUEST['comment_id']);
    $userID = get_current_user_id();
    $type = sanitize_text_field($_REQUEST['type']);

    $likeUsers = get_comment_meta($commentID, 'like_users', true);
    $parsedUsersL = (!empty($likeUsers) && trim($likeUsers) != "" ? explode(',', $likeUsers) : [] );
    $userLiked = (!is_user_logged_in() ? false : in_array($userID, $parsedUsersL));
    
    $dislikeUsers = get_comment_meta($commentID, 'dislike_users', true);
    $parsedUsersDL = (!empty($dislikeUsers) && trim($dislikeUsers) != "" ? explode(',', $dislikeUsers) : [] );
    $userDisliked = (!is_user_logged_in() ? false : in_array($userID, $parsedUsersDL));

    $actions = [];
    if($type == 'like') {
        if($userLiked) {
            removeCommentLike($commentID, $userID);
            $actions[] = "like-";
        } else if(!$userLiked && !$userDisliked) {
            addCommentLike($commentID, $userID);
            $actions[] = "like+";
        } else if(!$userLiked && $userDisliked) {
            removeCommentDislike($commentID, $userID);
            $actions[] = "dislike-";
            addCommentLike($commentID, $userID);
            $actions[] = "like+";
        }
    } else if($type == 'dislike') {
        if($userDisliked) {
            removeCommentDislike($commentID, $userID);
            $actions[] = "dislike-";
        } else if(!$userDisliked && !$userLiked) {
            addCommentDislike($commentID, $userID);
            $actions[] = "dislike+";
        } else if(!$userDisliked && $userLiked) {
            removeCommentLike($commentID, $userID);
            $actions[] = "like-";
            addCommentDislike($commentID, $userID);
            $actions[] = "dislike+";
        }
    }

    $result = json_encode(["type" => "success", "data" => ["actions" => $actions], "message" => ""]);
    echo $result;

    die();
}


function addCommentLike($commentID, $userID) {
    $likes = intval(get_comment_meta($commentID, 'likes', true)) + 1;
    $likeUsers = get_comment_meta($commentID, 'like_users', true);
    $parsedUsersL = (!empty($likeUsers) && trim($likeUsers) != "" ? explode(',', $likeUsers) : [] );
    $parsedUsersL[] = $userID;

    update_comment_meta($commentID, 'likes', $likes);
    update_comment_meta($commentID, 'like_users', implode(',', $parsedUsersL));
} 


function addCommentDislike($commentID, $userID) {
    $dislikes = intval(get_comment_meta($commentID, 'dislikes', true)) + 1;
    $dislikeUsers = get_comment_meta($commentID, 'dislike_users', true);
    $parsedUsersDL = (!empty($dislikeUsers) && trim($dislikeUsers) != "" ? explode(',', $dislikeUsers) : [] );
    $parsedUsersDL[] = $userID;

    update_comment_meta($commentID, 'dislikes', $dislikes);
    update_comment_meta($commentID, 'dislike_users', implode(',', $parsedUsersDL));
}


function removeCommentLike($commentID, $userID) {
    $likes = intval(get_comment_meta($commentID, 'likes', true)) - 1;
    $likes = ($likes < 0 ? 0 : $likes);
    
    $likeUsers = get_comment_meta($commentID, 'like_users', true);
    $parsedUsersL = (!empty($likeUsers) && trim($likeUsers) != "" ? explode(',', $likeUsers) : [] );    
    $key = array_search($userID, $parsedUsersL);
    if($key !== false) {
        unset($parsedUsersL[$key]);
    }

    update_comment_meta($commentID, 'likes', $likes);
    update_comment_meta($commentID, 'like_users', implode(',', $parsedUsersL));
    
}


function removeCommentDislike($commentID, $userID) {
    $dislikes = intval(get_comment_meta($commentID, 'dislikes', true)) - 1;
    $dislikeUsers = get_comment_meta($commentID, 'dislike_users', true);
    $parsedUsersDL = (!empty($dislikeUsers) && trim($dislikeUsers) != "" ? explode(',', $dislikeUsers) : [] );
    
    $key = array_search($userID, $parsedUsersDL);
    if($key !== false) {
        unset($parsedUsersDL[$key]);
    }

    update_comment_meta($commentID, 'dislikes', $dislikes);
    update_comment_meta($commentID, 'dislike_users', implode(',', $parsedUsersDL));
}