<?php
    $options = Comment_Manager::instance()->getOptions();
    $sapIDRoles = (empty($options['cm-addon-sap-id-roles_post']) ? ["administrator"] : json_decode($options['cm-addon-sap-id-roles_post']));
    $sapIDRoles = empty($sapIDRoles) ? ["administrator"] : $sapIDRoles;
    $user = get_user_by("id", get_current_user_id());
    
    $found = false;
    foreach($user->roles as $ownRole) {
        if(in_array($ownRole, $sapIDRoles)) {
            $found = true;
        }
    }
    
    if($found === false) {
        return;
    } 
    
    if(is_page($args['post_id'])) {
        
        $title = $options['cm-addon-sap-id-title_' . $args['post_id']] ?? "SAP ID";
        $placeholder = $options['cm-addon-sap-id-placeholder_' . $args['post_id']] ?? "Enter SAP ID";
    }else{
        
        $postType = get_post_type($args['post_id']);
        $title = $options['cm-addon-sap-id-title_' . $postType] ?? "SAP ID";
        $placeholder = $options['cm-addon-sap-id-placeholder_' . $postType] ?? "Enter SAP ID";
    }
?>

<div class="cm-sap-id-component-wrapper" id="cm-sap-id-component_<?php echo $args['comment_id']; ?>">
    <label class="cm-sap-id-label" for="sap-id"><?php echo $title; ?></label>
    <input class="cm-sap-id cm-addon-field" id="cm-sap-id_<?php echo $args['comment_id']; ?>" data-comment-id="<?php echo $args['comment_id']; ?>" type="text" name="sap_id" value="" placeholder="<?php echo $placeholder; ?>" autocomplete="off" />
    <ul class="cm-sap-id-autocomplete-data"></ul>
</div>
