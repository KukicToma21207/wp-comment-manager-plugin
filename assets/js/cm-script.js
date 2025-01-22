
function loadComments($, args, callback) {

    $.ajax({
        type: "post",
        dataType: "json",
        url: cmAjax.ajaxurl,
        data: {
            action: "cm_get_comments",
            post_id: args.post_id,
            parent_id: (args.parent_id ?? 0),
            page: (args.page ?? -1),
            filter: (args.filter ?? ""),
        },
        success: function (response) {
            if (response.type != "success") {
                console.error(response.message);
                return;
            }

            if (callback) {
                callback(response.data);
                return;
            }

            $(".cm-comments-section").removeClass("hidden");
            let commentsList = $("ul.cm-child-list");
            commentsList.html(response.data);

            updatePagination($, response.pagination, args.post_id);
        },
        error: function (response) {
            console.error("Error: " + response);
        },
        finish: function (response) {
            console.info("All done with Ajax!");
        }
    });
}


function saveComment($, args, callback) {
    let data = { action: "cm_save_comment" };

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
        finish: function (response) {
            console.info("All done!");
        }
    });
}


function updatePagination($, data, post_id) {

    let paginationHolder = $("#cm-pagination-container");

    //We dont need pagination for single page
    if (data.total <= 1 || data.current == 0) {

        //Make sure there is no left over pagination
        paginationHolder.html("");
        return;
    }

    let dataPostID = " data-post_id='" + post_id + "' ";

    //Do not need previous link if we're on the first page
    let usePrevious = data.current > 1;

    //Do not need next link if we're already on the last page
    let userNext = data.current < data.total;

    let separateFirstAndCurrent = (data.current - 2 > 2);
    let separateLastAndCurrent = (data.current + 2 < data.total - 1);

    let previousLink = usePrevious ? "<a href='#' " + dataPostID + " class='cm-pagination-link' data-page='" + (data.current - 1) + "'>&lt;&nbsp;претходни</a>" : "";
    let nextLink = userNext ? "<a href='#' " + dataPostID + " class='cm-pagination-link' data-page='" + (data.current + 1) + "'>следећи&nbsp;&gt;</a>" : "";

    let firstSeparator = separateFirstAndCurrent ? "<span>...</span>" : "";
    let nextSeparator = separateLastAndCurrent ? "<span>...</span>" : "";
    let firstPage = data.current == 1 ? "" : "<a href='#' " + dataPostID + " class='cm-pagination-link' data-page='1'>1</a>";
    let currentPage = "<span>" + data.current + "</span>";
    let lastPage = data.current == data.total ? "" : "<a href='#' " + dataPostID + " class='cm-pagination-link' data-page='" + data.total + "'>" + data.total + "</a>";

    let allPrevious = "";
    if (data.current - 2 > 1) {
        allPrevious += "<a href='#' " + dataPostID + " class='cm-pagination-link' data-page='" + (data.current - 2) + "'>" + (data.current - 2) + "</a>";
    }
    if (data.current - 1 > 1) {
        allPrevious += "<a href='#' " + dataPostID + " class='cm-pagination-link' data-page='" + (data.current - 1) + "'>" + (data.current - 1) + "</a>";
    }

    let allNext = "";
    if (data.current + 1 < data.total) {
        allNext += "<a href='#' " + dataPostID + " class='cm-pagination-link' data-page='" + (data.current + 1) + "'>" + (data.current + 1) + "</a>";
    }
    if (data.current + 2 < data.total) {
        allNext += "<a href='#' " + dataPostID + " class='cm-pagination-link' data-page='" + (data.current + 2) + "'>" + (data.current + 2) + "</a>";
    }

    paginationHolder.html(previousLink + firstPage + firstSeparator + allPrevious + currentPage + allNext + nextSeparator + lastPage + nextLink);
    paginationHolder.attr("data-current-page", data.current);
}


jQuery(document).ready(function ($) {

    let commentsConteainer = $("#cm-comment_0");
    if (!commentsConteainer || commentsConteainer.length <= 0) {
        return;
    }

    let post_id = commentsConteainer.data("post_id");

    loadComments($, { 'post_id': post_id });

    let loadMoreLinkActive = false;
    $(document).on("click", ".cm-show-reply, .cm-show-reply-user", function (ev) {
        ev.preventDefault();
        if (loadMoreLinkActive) {
            return;
        }
        loadMoreLinkActive = true;

        let commentID = $(this).data("comment_id");
        let comment = $("#cm-comment_" + commentID);
        let hiddenElements = comment.find(".cm-hidden");

        if (hiddenElements && hiddenElements.length > 0) {
            let self = $(this);

            let childWrapper = comment.find(".cm-comment-child-wrapper");
            let hasChildren = (childWrapper.find(".cm-child-list .cm-comment").length != 0);

            if (!hasChildren) {
                loadComments($, { 'post_id': post_id, 'parent_id': commentID }, function (data) {
                    $(childWrapper.find(".cm-child-list")[0]).html(data);

                    //Show children and reply form
                    hiddenElements.each(function (i, item) {
                        $(item).toggleClass("cm-hidden");
                    });

                    if (data != "") {
                        $(comment.find(".cm-comment-body")[0]).toggleClass("active");
                    }

                    loadMoreLinkActive = false;

                    self.html(self.data("clicked_title"));
                });
            } else {
                //Show children and reply form
                hiddenElements.each(function (i, item) {
                    $(item).toggleClass("cm-hidden");
                });
                $(comment.find(".cm-comment-body")[0]).toggleClass("active");

                loadMoreLinkActive = false;

                self.html(self.data("clicked_title"));
            }
        } else {
            $(this).html($(this).data("original_title"));

            let hideElements = comment.find(".cm-comment-child-wrapper, form");

            hideElements.each(function (i, item) {
                $(item).addClass("cm-hidden");
            });
            $(comment.find(".cm-comment-body")[0]).removeClass("active");

            loadMoreLinkActive = false;
        }


        $(document).on("click", "a.cm-form-title-comment-link", function (ev) {
            ev.preventDefault();

            let scrollID = $(this).attr('href');

            document.getElementById(scrollID).scrollIntoView();
        });
    });



    $(document).on("click", ".cm-pagination-link", function (ev) {
        ev.preventDefault();

        let page = $(this).data("page");
        let postID = $(this).data("post_id");
        let filter = $("#select-subcategory-filter").val();

        loadComments($, { page: page, post_id: postID, filter: filter });
    });



    $(document).on("click", "form.cm-comment-form input.submit", function (ev) {
        ev.preventDefault();

        let postID = $(this).data("post_id");
        let parentID = $(this).data("parent_id");
        let form = $("form#cm-comment-form-" + parentID);
        let textArea = $(form.find("textarea")[0]);
        let text = textArea.val().trim();
        let addonFields = form.find("input.cm-addon-field");
        let parentComment = $("#cm-comment_" + parentID);
        let commentList = $(parentComment.find("ul.cm-child-list")[0]);
        let commentResponces = $("#cm-comment-responces-" + parentID + " > span");
        let commentResponcesNum = parseInt(commentResponces.text());
        let commentFormData = { parent_id: parentID, post_id: postID, message: text };

        if (addonFields.length > 0) {
            addonFields.each(function (i, item) {
                let fieldName = $(item).attr('name');
                let fieldValue = $(item).val();

                commentFormData[fieldName] = fieldValue;
            });
        }

        if (text == "") {
            alert("Morate popuniti polje komentara.");
            return;
        }

        saveComment($, commentFormData, function (comment) {
            $(".cm-comments-section").removeClass("hidden");

            if (parentID == 0) {
                $(commentList).prepend(comment);
            } else {
                $(commentList).append(comment);
                $(parentComment.find(".cm-comment-body")[0]).addClass('active');
            }

            commentResponces.html(commentResponcesNum + 1);

            textArea.val("");

            if (addonFields.length > 0) {
                addonFields.each(function (i, item) {
                    $(item).val("");
                });
            }
        });
    });


    $(document).on("change", ".select-subcategory", function () {
        var commentID = Number($(this).data("comment_id"));
        var value = $(this).val();

        if(value.trim() == "") {
            return;
        }
        
        var submitData = {
            action: "cm_comment_update_subcategory",
            comment_id: commentID,
            subcategory: value,
        };

        $.ajax({
            type: "post",
            dataType: "json",
            url: cmAjax.ajaxurl,
            data: submitData,
            success: function (response) {
                
                if(response.type ==  "error") {
                    console.error(response.message);
                    return;
                } else if(response.type == "delete") {
                    $("span#cm-comment-subcategory-" + commentID).text("Без категорије");
                    return;
                }

                $("span#cm-comment-subcategory-" + commentID).text(value);
            },
            error: function (response) {
                console.error("Error: " + response.message);
            }
        });
    });


    $(document).on("click", "#cm-comments-filter-btn", function(){
        var value = $("#select-subcategory-filter").val();        
        let postID = $(this).data("post_id");

        loadComments($, {
            post_id: postID,
            parent_id: null,
            filter: value,
        });
    });
})
