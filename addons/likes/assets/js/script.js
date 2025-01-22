/**
 * Ajax call for the Like/Dislike functionality
 * 
 * @param {jQuery} $ 
 * @param {array} args 
 * @param {function} callback 
 */
function cmLike($, args, callback) {

    let data = { action: "cm_like" };

    submitData = { ...data, ...args };

    $.ajax({
        type: "post",
        dataType: "json",
        url: cmAjax.ajaxurl,
        data: submitData,
        success: function (response) {
            if (response.type != "success") {
                console.error(response.message);
                return;
            }

            if (callback) {
                callback(response.data);
                return;
            }
        },
        error: function (response) {
            console.error("Error: " + response.message);
        },
    });
}


function updateLikedDisliked($, data, likeLink, dislikeLink) {
    let likesContainer = $(likeLink.find("span")[0]);
    let dislikesContainer = $(dislikeLink.find("span")[0]);
    let likes = Number(likesContainer.text());
    let dislikes = Number(dislikesContainer.text());

    data.actions.forEach(element => {

        if (element == 'like+') {
            likes++;
            likesContainer.text(likes);
            likeLink.addClass(function(i, current){
                return "liked doAnimate";
            });
        }

        if (element == 'like-') {
            likes--;
            likes = (likes < 0 ? 0 : likes);
            likesContainer.text(likes);
            likeLink.removeClass('liked');
        }

        if (element == 'dislike+') {
            dislikes++;
            dislikesContainer.text(dislikes);
            dislikeLink.addClass(function(i, current){
                return "disliked doAnimate";
            });
        }

        if (element == 'dislike-') {
            dislikes--;
            dislikes = (dislikes < 0 ? 0 : dislikes);
            dislikesContainer.text(dislikes);
            dislikeLink.removeClass('disliked');
        }
    });
}



jQuery(document).ready(function ($) {
    $(document).on("click", "a.cm-comment-likes-count, a.cm-comment-dislikes-count", function (ev) {
        ev.preventDefault();

        let commentID = $(this).data("comment_id");
        let action = $(this).data("action");
        let likeLink;
        let dislikeLink;

        if(action == "like") {                    
            likeLink = $(this);
            dislikeLink = $(likeLink.siblings("a.cm-comment-dislikes-count")[0]);

        } else if(action == "dislike") {
            dislikeLink = $(this);
            likeLink = $(dislikeLink.siblings("a.cm-comment-likes-count")[0]);            
        }

        args = {
            comment_id: commentID,
            type: action,
        };

        cmLike($, args, function (data) {
            updateLikedDisliked($, data, likeLink, dislikeLink);
        });
    });
});