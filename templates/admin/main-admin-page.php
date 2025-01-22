
<div>
<h2>Comment Manager</h2>
<form id="cm-comments-settings-form" action="options.php" method="post">

    <?php settings_fields('cm_plugin_options'); ?>
    
    <?php do_settings_sections('plugin'); ?>

    <input id="cm-options-main-submit" type="submit" name="submit" value="Save Changes" hidden />
    <input id="cm-options-save-changes" type="button" name="" value="Save Changes" />

</form>
</div>