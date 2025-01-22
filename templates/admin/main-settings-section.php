<?php

/**
 * Render field to check if we want to use custom templates for the form
 */
add_settings_field('cm_option_use_custom_template', 'Use custom template', 'cm_option_use_custom_template', 'plugin', 'plugin_main');
function cm_option_use_custom_template()
{
    $options = Comment_Manager::instance()->getOptions();
    $option = ((empty($options['cm_option_use_custom_template']) || $options['cm_option_use_custom_template'] == '') ? 0 : 1);
    
?>
    <input id='cm_option_use_custom_template' name='cm_plugin_options[cm_option_use_custom_template]' value="1" type="checkbox" <?php echo ($option ? "checked='checked'" : ""); ?> />
<?php
}


/**
 * Render field to set number of comments per page
 */
add_settings_field('cm_option_comments_per_page', 'Comments per page', 'cm_option_comments_per_page', 'plugin', 'plugin_main');
function cm_option_comments_per_page()
{
    $options = Comment_Manager::instance()->getOptions();
    $option = ((empty($options['cm_option_comments_per_page']) || $options['cm_option_comments_per_page'] == '') ? '' : $options['cm_option_comments_per_page']);
    
?>
    <input id='cm_option_comments_per_page' name='cm_plugin_options[cm_option_comments_per_page]' value="<?php echo $option; ?>" />
<?php
}
