
jQuery(document).ready(function ($) {

    /**
     * This will add comment type/category that is enetred in the text field
     */
    $(".add-button").click(function () {
        let type = $(this).data("type");
        type = (type == "comment-type" ? "type" : (type == "comment-category" ? "category" : "sub-category"));

        let newCommentType = $("#new-comment-" + type + "-text");
        if (newCommentType.val().trim() == "") {
            alert("Please enter comment " + type + " first");
            return;
        }

        let commentTypeList = $("#cm-comment-" + type + "-list");
        commentTypeList.append("<li>" + newCommentType.val() + "</li>");
        newCommentType.val("");

        updateCommentTypeOption($("#cm-comment-" + type + "-list"), $("#cm-comment-" + type + "-option"));
    });


    $(document).on("click", "#cm-comment-type-list li", function () {
        $("#cm-comment-type-list li").removeClass("active");
        $(this).addClass('active');
        $("#remove-a-type").removeAttr("disabled");
    });


    $(document).on("click", "#cm-comment-category-list li", function () {
        $("#cm-comment-category-list li").removeClass("active");
        $(this).addClass('active');
        $("#remove-a-category").removeAttr("disabled");
    });


    $(document).on("click", "#cm-comment-sub-category-list li", function () {
        $("#cm-comment-sub-category-list li").removeClass("active");
        $(this).addClass('active');
        $("#remove-a-sub-category").removeAttr("disabled");
    });


    /**
     * This will remove selected comment type/category
     */
    $(document).on("click", ".remove-button", function () {
        let type = $(this).data("type");
        let selectedType;
        let listObject;
        let optionObject;

        if (type == "comment-type") {
            selectedType = $("#cm-comment-type-list li.active");
            listObject = $("#cm-comment-type-list");
            optionObject = $("#cm-comment-type-option");

        } else if (type == "comment-category") {
            selectedType = $("#cm-comment-category-list li.active");
            listObject = $("#cm-comment-category-list");
            optionObject = $("#cm-comment-category-option");
        } else if (type == "comment-sub-category") {
            selectedType = $("#cm-comment-sub-category-list li.active");
            listObject = $("#cm-comment-sub-category-list");
            optionObject = $("#cm-comment-sub-category-option");
        }

        if (selectedType && selectedType.length != 0) {
            selectedType.remove();
            updateCommentTypeOption(listObject, optionObject);
        }

        $(this).attr("disabled", "disabled");
    });


    /**
     * This will add page from list to settings container
     */
    $(document).on("click", ".cm-page-list-item", function () {
        let settingsInput = $("#cm_option_page_settings");
        let initialSettings = settingsInput.val().trim();
        let postID = $(this).data("post_id");
        let postTitle = $(this).data("post_title");
        let saveData = initialSettings == "" ? [] : JSON.parse(initialSettings);
        let pageSettings = {
            id: postID,
            title: postTitle,
        };

        saveData.push(pageSettings);
        settingsInput.val(JSON.stringify(saveData));

        //Initiate save
        $("#cm-options-main-submit").click();
    });


    /**
     * This will add post type from list to settings container
     */
    $(document).on("click", ".cm-post-list-item", function () {
        let settingsInput = $("#cm_option_post_type_settings");
        let initialSettings = settingsInput.val().trim();
        let postID = $(this).data("post_id");
        let postTitle = $(this).data("post_title");
        let saveData = initialSettings == "" ? [] : JSON.parse(initialSettings);
        let postTypeSettings = {
            id: postID,
            title: postTitle,
        };

        saveData.push(postTypeSettings);
        settingsInput.val(JSON.stringify(saveData));

        //Initiate save
        $("#cm-options-main-submit").click();
    });


    /**
     * This will remove page from the settings container
     */
    $(document).on("click", "a.cm-remove-per-page-settings", function (ev) {
        ev.preventDefault();

        let settingsInput = $("#cm_option_page_settings");
        let initialSettings = settingsInput.val().trim();
        let postID = $(this).data("post_id");

        let saveData = JSON.parse(initialSettings);
        let foundID = -1;
        $(saveData).each(function (i, el) {
            if (el.id == postID) {
                foundID = i;
                return;
            }
        });

        if (foundID > -1) {
            saveData.splice(foundID, 1);
        }

        settingsInput.val(JSON.stringify(saveData));

        //Initiate save
        $("#cm-options-main-submit").click();
    });

    /**
     * This will remove post type from the settings container
     */
    $(document).on("click", "a.cm-remove-per-post-settings", function (ev) {
        ev.preventDefault();

        let settingsInput = $("#cm_option_post_type_settings");
        let initialSettings = settingsInput.val().trim();
        let postID = $(this).data("post_id");

        let saveData = JSON.parse(initialSettings);
        let foundID = -1;
        $(saveData).each(function (i, el) {
            if (el.id == postID) {
                foundID = i;
                return;
            }
        });

        if (foundID > -1) {
            saveData.splice(foundID, 1);
        }

        settingsInput.val(JSON.stringify(saveData));

        //Initiate save
        $("#cm-options-main-submit").click();
    });


    /**
     * This will update option that is actually saved in DB
     */
    updateCommentTypeOption = (listObject, optionObject) => {
        let allOptions = listObject.find("li");
        let finalOption = [];

        if (!allOptions || allOptions.length == 0) {
            optionObject.val("");
            return;
        }

        allOptions.each(function (index, item) {
            finalOption.push($(item).text());
        });

        optionObject.val(JSON.stringify(finalOption));
    }


    function savePerPageSettings() {
        let allCustomSettings = $(".cm-cutom-page-settings");
        let settingsInput = $("#cm_option_page_settings");
        let allPageOptions = JSON.parse(settingsInput.val() == "" ? "[]" : settingsInput.val());

        allCustomSettings.each(function (index, master) {
            let addons = [];
            let titles = {};
            let type = "Comment";
            let category = "";

            $($(master).find(".cm-page-use-addons-container ul li input:checked")).each(function (i, el) {
                addons.push($(el).val());
            });

            $($(master).find(".cm-page-comment-titles input")).each(function (i, el) {
                let name = $(el).data("name");
                titles[name] = $(el).val();
            });

            type = $($(master).find(".cm-comment-type-selector")[0]).val();
            category = $($(master).find(".cm-comment-category-selector")[0]).val();

            allPageOptions[index].addons = addons;
            allPageOptions[index].titles = titles;
            allPageOptions[index].type = type;
            allPageOptions[index].category = category;
        });

        settingsInput.val(JSON.stringify(allPageOptions));
    }


    function savePerPostTypeSettings() {
        let allCustomSettings = $(".cm-cutom-post-settings");
        let settingsInput = $("#cm_option_post_type_settings");
        let allPostOptions = JSON.parse(settingsInput.val() == "" ? "[]" : settingsInput.val());

        allCustomSettings.each(function (index, master) {
            let addons = [];
            let titles = {};
            let type = "Comment";
            let category = "";

            $($(master).find(".cm-post-use-addons-container ul li input:checked")).each(function (i, el) {
                addons.push($(el).val());
            });

            $($(master).find(".cm-post-comment-titles input")).each(function (i, el) {
                let name = $(el).data("name");
                titles[name] = $(el).val();
            });

            type = $($(master).find(".cm-comment-type-selector")[0]).val();
            category = $($(master).find(".cm-comment-category-selector")[0]).val();

            allPostOptions[index].addons = addons;
            allPostOptions[index].titles = titles;
            allPostOptions[index].type = type;
            allPostOptions[index].category = category;
            console.log(allPostOptions);
        });

        settingsInput.val(JSON.stringify(allPostOptions));
    }


    /**
     * This will getter all the settings data par page and per post and seve it
     */
    $("#cm-options-save-changes").on("click", function (ev) {
        ev.preventDefault();

        savePerPageSettings();
        savePerPostTypeSettings();

        $("#cm-options-main-submit").click();
    });



    /**
     * Update existing comments with the new type and category
     */
    function updateExistingComments(args, updateCallback, doneCallback) {
        $.ajax({
            url: cmAjax.ajaxurl,
            data: {
                action: "cm_comment_update",
                type: args.type,
                post_id: args.id,
                post_type: args.id,
                comment_type: args.comment_type,
                comment_category: args.comment_category
            },
            type: 'POST',
            dataType: "text",
            beforeSend: function (jqXHR, settings) {
                var self = this;
                var xhr = settings.xhr;

                settings.xhr = function () {

                    var output = xhr();

                    output.onreadystatechange = function () {
                        if (typeof (updateCallback) == "function") {
                            updateCallback(this);
                        }
                    };

                    return output;
                };
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error(errorThrown);
            },
            success: function (data, status, jqXHR) {
                if (doneCallback) {
                    doneCallback(data);
                }
            }

        });
    }


    $(document).on("click", ".cm-update-type-cat", function () {

        if(!confirm("This will update all comments for the page. Proceed?")){
            return;
        }

        $(this).addClass("updating").val("Updating...");
        let self = $(this);
        let type = $(this).data("type");
        let id = $(this).data("post_id");
        let commentType = "";
        let commentCategory = "";

        if(type == "page") {
            commentType = $("#cm-cutom-page-settings-"+id+" .cm-page-comment-type select").val();
            commentCategory = $("#cm-cutom-page-settings-"+id+" .cm-page-comment-category select").val();
        }else{
            
            commentType = $("#cm-cutom-post-settings-"+id+" .cm-post-comment-type select").val();
            commentCategory = $("#cm-cutom-post-settings-"+id+" .cm-post-comment-category select").val();
        }

        updateExistingComments({
            id: id,
            type: type,
            comment_type: commentType,
            comment_category: commentCategory,
        }, function (jqXHR) {

            // if it's '3', then it's an update, 
            if (jqXHR.readyState == 3) {
                let newVal = jqXHR.responseText.split(",")[jqXHR.responseText.split(",").length - 1];

                //Update progress
                self.val("Updating... (" + newVal + ")");
            }
        }, function (data) {

            //Update done
            self.removeClass("updating").val("Update");
        });
    });


    $(document).on("click", ".cm-custom-tag-list li", function(){
        isAddTag = (!$(this).hasClass("active") == true);
        isSpecial = ($(this).data("special") && $(this).data("special") != "");
        specialData = $(this).data("special-value") ?? "";

        if(isSpecial) {
            return;
        }

        $(this).toggleClass("active");

        resultElement = $("input#" + $(this).data("result-element"));
        prevValue = resultElement.val();
        prevItems = prevValue.trim() != "" ? JSON.parse(prevValue) : [];

        if(typeof(prevItems) != "object") {
            prevItems = [];

            if(specialData) {
                prevItems.push(specialData);
            }

        }
        
        newValue = "";

        if(isAddTag) {
            prevItems.push($(this).text().trim());
            newValue = JSON.stringify(prevItems);
            resultElement.val(newValue);

            return;
        }

        if(prevItems.length == 0 && !isAddTag) {
            return;
        }

        found = prevItems.indexOf($(this).text().trim());
        if(found > -1) {
            prevItems.splice(found, 1);
            newValue = JSON.stringify(prevItems);
            resultElement.val(newValue);
        }
    });
});