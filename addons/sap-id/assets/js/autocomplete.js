
jQuery(document).ready(function ($) {

    //Deselect (hide) autocomplete panel
    $(document).on("click", "body", function () {
        $("ul.cm-sap-id-autocomplete-data").children().remove();
    });

    
    //Check if the number is pressed, if not cancel the pressed key
    $(document).on("keypress", ".cm-sap-id.cm-addon-field", function (e) {
        if(e.code == 'Space') {
            e.preventDefault();
            return;
        }

        let keyPressed = Number(e.key);

        if (!Number.isInteger(keyPressed)) {
            e.preventDefault();
            return;
        }
    });


    //After number is pressed lets do the autocomplete logic
    $(document).on("keyup", ".cm-sap-id.cm-addon-field", function (e) {
        if(e.key == "Escape") {
            updateAutocompleteData([], 0);
            return;
        }

        let newValue = $(this).val();
        let commentID = $(this).data("comment-id");
        
        if (newValue.trim() === "" || newValue.length < 3) {
            $("ul.cm-sap-id-autocomplete-data").children().remove();
            return;
        }

        $.ajax({
            type: "post",
            dataType: "json",
            url: cmAjax.ajaxurl,
            data: {
                action: "cm_sap_id_autocomplete",
                sap_id: newValue,
            },
            success: function (response) {
                if (response.type != "success") {
                    console.error(response.message);
                    return;
                }

                updateAutocompleteData(response.data, commentID);
            },
            error: function (response) {
                console.error("Error: " + response);
            },
        });
    });


    //Click on autocoplete item in the list
    $(document).on("click", "li.cm-sap-id-autocomplete-item", function () {
        let value = $(this).data('value');
        let commentId = $(this).data("comment_id");

        $("#cm-sap-id_" + commentId).val(value).focus();
    });


    //Update autocomplete data after Ajax call is successfully returned data
    function updateAutocompleteData(data, commentID) {
        let autocompleteHolder = $("#cm-sap-id-component_" + commentID + " ul.cm-sap-id-autocomplete-data");
        autocompleteHolder.children().remove();

        $(data).each(function (i, item) {
            autocompleteHolder.append("<li class='cm-sap-id-autocomplete-item' data-comment_id='" + commentID + "' data-value='" + item.sap + "'>" + item.name + "&nbsp;(" + item.sap + ")</li>");
        });
    }
});