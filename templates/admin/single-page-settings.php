<fieldset id="cm-cutom-page-settings-<?php echo $page->id; ?>" class="cm-cutom-page-settings">
    <a href="#" class="cm-remove-per-page-settings" title="Remove sttings for this page" data-post_id="<?php echo $page->id; ?>">X</a>
    <legend><?php echo "$page->title"; ?></legend>

    <input type="text" name="" value="<?php echo $page->id; ?>" hidden />

    <div class="cm-page-use-addons-container">
        <h4>Select addons to use</h4>
        <ul class="cm-addons-selector">
            <?php foreach ($addonsList as $addon): ?>
                <li class="cm-addon-option">
                    <label for="cm-include-addon"><?php echo $addon; ?></label>
                    <input
                        type="checkbox"
                        id="cm-include-addon"
                        value="<?php echo $addon; ?>"
                        <?php echo ($page->addons && in_array($addon, $page->addons) ? "checked='checked'" : ""); ?> />
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="cm-page-comment-titles">

        <h4>Change title options</h4>

        <div class="cm-page-comment-title-container">
            <label>Main title</label>
            <input type="text" data-name="main" value="<?php echo ($page->titles && $page->titles->main ? $page->titles->main : ''); ?>">
        </div>

        <div class="cm-page-comment-title-container">
            <label>Main form title</label>
            <input type="text" data-name="main_form" value="<?php echo ($page->titles && $page->titles->main_form ? $page->titles->main_form : ''); ?>">
        </div>

        <div class="cm-page-comment-title-container">
            <label>Reply form title</label>
            <input type="text" data-name="reply_form" value="<?php echo ($page->titles && $page->titles->reply_form ? $page->titles->reply_form : ''); ?>">
        </div>

        <div class="cm-page-comment-title-container">
            <label>Main form placeholder</label>
            <input type="text" data-name="main_placeholder" value="<?php echo ($page->titles && $page->titles->main_placeholder ? $page->titles->main_placeholder : ''); ?>">
        </div>

        <div class="cm-page-comment-title-container">
            <label>Reply form placeholder</label>
            <input type="text" data-name="reply_placeholder" value="<?php echo ($page->titles && $page->titles->reply_placeholder ? $page->titles->reply_placeholder : ''); ?>">
        </div>
    </div>

    <div class="cm-page-comment">
        <h4>Select type/category</h4>

        <div class="cm-page-comment-type">
            <label for="cm-comment-type-selector">Type</label>
            <select name="" class="cm-comment-type-selector">
                <option value="comment" <?php echo (empty($page->type) || $page->type == "comment" ? "selected='selected'" : ""); ?>>Comment</option>
                <?php foreach ($commentTypes as $type): ?>
                    <?php $typeValue = str_replace(" ", "_", strtolower($type)); ?>
                    <option value="<?php echo $typeValue; ?>" <?php echo $page->type == $typeValue ? "selected='selected'" : ""; ?>><?php echo $type; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="cm-page-comment-category">
            <label for="cm-comment-category-selector">Category</label>
            <select name="" class="cm-comment-category-selector">
                <option value="" <?php echo empty($page->category) ? "selected='selected'" : ""; ?>>No Category</option>
                <?php foreach ($commentCategories as $category): ?>
                    <?php $categoryValue = str_replace(" ", "_", strtolower($category)); ?>
                    <option value="<?php echo $categoryValue; ?>" <?php echo $page->category == $categoryValue ? "selected='selected'" : ""; ?>><?php echo $category; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="cm-comment-type-cat-update">
            <label for="">Update existing comments</label>
            <input class="cm-update-type-cat" data-type="page" data-post_id="<?php echo $page->id; ?>" type="button" value="Update" >
        </div>
    </div>

    <div class="cm-page-addons-options">
        <h4>Addon options</h4>
        <?php do_action("cm-admin-addon-options-page", ["variable_name" => "cm_plugin_options", "options" => $options, "page_id" => $page->id]); //If addon has some options that we can change, it will render them here?>
    </div>
</fieldset>