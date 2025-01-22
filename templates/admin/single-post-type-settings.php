<fieldset id="cm-cutom-post-settings-<?php echo $postType->id; ?>" class="cm-cutom-post-settings">
    <a href="#" class="cm-remove-per-post-settings" title="Remove sttings for this post" data-post_id="<?php echo $postType->id; ?>">X</a>
    <legend><?php echo "$postType->title"; ?></legend>

    <input type="text" name="" value="<?php echo $postType->id; ?>" hidden />

    <div class="cm-post-use-addons-container">
        <h4>Select addons to use</h4>
        <ul class="cm-addons-selector">
            <?php foreach ($addonsList as $addon): ?>
                <li class="cm-addon-option">
                    <label for="cm-include-addon"><?php echo $addon; ?></label>
                    <input
                        type="checkbox"
                        id="cm-include-addon"
                        value="<?php echo $addon; ?>"
                        <?php echo ($postType->addons && in_array($addon, $postType->addons) ? "checked='checked'" : ""); ?> />
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="cm-post-comment-titles">

        <h4>Change title options</h4>

        <div class="cm-post-comment-title-container">
            <label>Main title</label>
            <input type="text" data-name="main" value="<?php echo ($postType->titles && $postType->titles->main ? $postType->titles->main : ''); ?>">
        </div>

        <div class="cm-post-comment-title-container">
            <label>Main form title</label>
            <input type="text" data-name="main_form" value="<?php echo ($postType->titles && $postType->titles->main_form ? $postType->titles->main_form : ''); ?>">
        </div>

        <div class="cm-post-comment-title-container">
            <label>Reply form title</label>
            <input type="text" data-name="reply_form" value="<?php echo ($postType->titles && $postType->titles->reply_form ? $postType->titles->reply_form : ''); ?>">
        </div>
        
        <div class="cm-page-comment-title-container">
            <label>Main form placeholder</label>
            <input type="text" data-name="main_placeholder" value="<?php echo ($postType->titles && $postType->titles->main_placeholder ? $postType->titles->main_placeholder : ''); ?>">
        </div>

        <div class="cm-page-comment-title-container">
            <label>Reply form placeholder</label>
            <input type="text" data-name="reply_placeholder" value="<?php echo ($postType->titles && $postType->titles->reply_placeholder ? $postType->titles->reply_placeholder : ''); ?>">
        </div>
    </div>

    <div class="cm-post-comment">
        <h4>Select type/category</h4>

        <div class="cm-post-comment-type">
            <label for="cm-comment-type-selector">Type</label>
            <select name="" class="cm-comment-type-selector">
                <option value="Comment" <?php echo $postType->type == "Comment" ? "selected='selected'" : ""; ?>>Comment</option>
                <?php foreach ($commentTypes as $type): ?>
                    <?php $typeValue = str_replace(" ", "_", strtolower($type)); ?>
                    <option value="<?php echo $typeValue; ?>" <?php echo $postType->type == $typeValue ? "selected='selected'" : ""; ?>><?php echo $type; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="cm-post-comment-category">
            <label for="cm-comment-category-selector">Category</label>
            <select name="" class="cm-comment-category-selector">
                <option value="" <?php echo empty($postType->category) ? "selected='selected'" : ""; ?>>No Category</option>
                <?php foreach ($commentCategories as $category): ?>
                    <?php $categoryValue = str_replace(" ", "_", strtolower($category)); ?>
                    <option value="<?php echo $categoryValue; ?>" <?php echo $postType->category == $categoryValue ? "selected='selected'" : ""; ?>><?php echo $category; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="cm-comment-type-cat-update">
            <label for="">Update existing comments</label>
            <input class="cm-update-type-cat" data-type="post" data-post_id="<?php echo $postType->id; ?>" type="button" value="Update" >
        </div>
    </div>

    <div class="cm-page-addons-options">
        <h4>Addon options</h4>
        <?php do_action("cm-admin-addon-options-page", ["variable_name" => "cm_plugin_options", "options" => $options, "page_id" => $postType->id]); //If addon has some options that we can change it will render them here?>
    </div>
</fieldset>