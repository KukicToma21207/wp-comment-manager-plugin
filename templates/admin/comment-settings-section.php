<?php

/**
 * Render various comment settings
 */
add_settings_field('cm_comment_type_settings', 'Custom options', 'cm_comment_types_option', 'plugin', 'plugin_comments_settings');
function cm_comment_types_option()
{
    $options = get_option('cm_plugin_options');
    $typeOptions = $options['cm_comment_types_option'] ?? "";
    $optionsList = [];

    if (!empty($typeOptions) && $typeOptions != "") {
        $optionsList = json_decode($typeOptions);
    }
?>
    <div class="cm-custom-options-wrapper">
        <div class="cm-custom-options-types">
            <h3>Types</h3>
            <input type="text" id="new-comment-type-text" class="new-option-text" data-type="comment-type" value="" />
            <input type="button" value="Add" class="add-button" data-type="comment-type" />
            <ul id="cm-comment-type-list" class="cm-comment-type-list">
                <?php foreach ($optionsList as $option) { ?>
                    <li><?php echo $option; ?></li>
                <?php } ?>
            </ul>
            <input type="button" id="remove-a-type" value="Remove" data-type="comment-type" disabled="disabled" class="remove-button" />


            <input hidden id='cm-comment-type-option' name='cm_plugin_options[cm_comment_types_option]' value="<?php echo esc_html($typeOptions); ?>" type="text" />
        </div>

        <?php
        $catOptions = $options['cm_comment_category_option'] ?? "";
        $categoriesList = [];

        if (!empty($catOptions) && $catOptions != "") {
            $categoriesList = json_decode($catOptions);
        }
        ?>

        <div class="cm-custom-options-categories">
            <h3>Categories</h3>
            <input type="text" id="new-comment-category-text" class="new-option-text" data-type="comment-category" value="" />
            <input type="button" value="Add" class="add-button" data-type="comment-category" />
            <ul id="cm-comment-category-list" class="cm-comment-type-list">
                <?php foreach ($categoriesList as $option) { ?>
                    <li><?php echo $option; ?></li>
                <?php } ?>
            </ul>
            <input type="button" value="Remove" id="remove-a-category" data-type="comment-category" disabled="disabled" class="remove-button" />


            <input hidden id='cm-comment-category-option' name='cm_plugin_options[cm_comment_category_option]' value="<?php echo esc_html($catOptions); ?>" type="text" />
        </div>

        <?php
        $catOptions = $options['cm_comment_sub_category_option'] ?? "";
        $subCategoriesList = [];

        if (!empty($catOptions) && $catOptions != "") {
            $subCategoriesList = json_decode($catOptions);
        }
        ?>

        <div class="cm-custom-options-categories">
            <h3>Sub-Categories</h3>
            <input type="text" id="new-comment-sub-category-text" class="new-option-text" data-type="comment-sub-category" value="" />
            <input type="button" value="Add" class="add-button" data-type="comment-sub-category" />
            <ul id="cm-comment-sub-category-list" class="cm-comment-type-list">
                <?php foreach ($subCategoriesList as $option) { ?>
                    <li><?php echo $option; ?></li>
                <?php } ?>
            </ul>
            <input type="button" value="Remove" id="remove-a-sub-category" data-type="comment-sub-category" disabled="disabled" class="remove-button" />


            <input hidden id='cm-comment-sub-category-option' name='cm_plugin_options[cm_comment_sub_category_option]' value="<?php echo esc_html($catOptions); ?>" type="text" />
        </div>
    </div>
<?php
}
