<?php

add_action("cm_custom_tag_list", function ($args) {
    $dataList = $args['data'] ?? [];
    $resultElementId = $args['result_element_id'] ?? "";
    $existingTags = $args['existing'] ?? [];
    $special = $args['special'] ?? "";

?>
    <div class="cm-custom-tag-list-container" >

        <ul class="cm-custom-tag-list">

            <?php foreach ($dataList as $item): ?>
                <?php
                    $additionalClasses = (in_array($item, $existingTags) || $item == $special ? "active" : "") . ($item == $special ? " special" : "");
                ?>
                
                <li 
                    data-special="<?php echo ($special == $item ? "true" : ""); ?>" 
                    data-special-value="<?php echo $special; ?>" 
                    data-result-element="<?php echo $resultElementId; ?>" 
                    class="cm-custom-tag-item <?php echo $additionalClasses; ?>">
                        <?php echo $item; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php
});
