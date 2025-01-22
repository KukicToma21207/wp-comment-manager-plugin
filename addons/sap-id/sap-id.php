<?php
include CM_ROOT_PATH . "/include/custom-tag-list.php";

define("CM_ADDON_SAP_VER", "1.1.0");


add_action("wp_ajax_cm_sap_id_autocomplete", "cm_sap_id_autocomplete");
add_action("wp_ajax_nopriv_cm_sap_id_autocomplete", "cm_sap_id_autocomplete");
function cm_sap_id_autocomplete()
{

    //Load up users with given SAP ID
    $reqSap = sanitize_text_field(trim($_REQUEST['sap_id']));
    if (empty($reqSap)) {
        echo json_encode(["type" => "success", "data" => [], "message" => ""]);
        die();
    }
    $result = [];

    $users = get_users([
        "meta_query" => [
            [
                'key' => 'sap',
                'value' => "^$reqSap.*",
                'compare' => 'REGEXP',
            ]
        ],
        "fields" => ["ID", "display_name"],
        "number" => 10,
    ]);

    if (!empty($users)) {
        foreach ($users as $user) {
            $sapMeta = get_user_meta($user->ID, 'sap', true);
            $result[] = [
                "id" => $user->ID,
                "name" => $user->display_name,
                "sap" => $sapMeta,
            ];
        }
    }

    echo json_encode(["type" => "success", "data" => $result, "message" => ""]);
    die();
}


add_action("admin_enqueue_scripts", "load_sap_id_admin_scripts");
function load_sap_id_admin_scripts()
{

    if (wp_doing_ajax()) {
        wp_enqueue_script("cm-addon-sap-aucomplete-script", CM_ADDONS_URL . "sap-id/assets/js/autocomplete.js", ['jquery'], CM_ADDON_SAP_VER);
        return;
    }

    wp_enqueue_script("cm-addon-sap-admin-script", CM_ADDONS_URL . "sap-id/assets/js/admin-script.js", ['jquery'], CM_ADDON_SAP_VER);
    wp_enqueue_style("cm-addon-sap-style", CM_ADDONS_URL . "sap-id/assets/css/admin-style.css", [], CM_ADDON_SAP_VER);
}



add_action("wp_enqueue_scripts", "load_sap_id_scripts");
function load_sap_id_scripts()
{
    wp_enqueue_script("cm-addon-sap-script", CM_ADDONS_URL . "sap-id/assets/js/script.js", ['jquery'], CM_ADDON_SAP_VER);
    wp_enqueue_script("cm-addon-sap-aucomplete-script", CM_ADDONS_URL . "sap-id/assets/js/autocomplete.js", ['jquery'], CM_ADDON_SAP_VER);
    wp_enqueue_style("cm-addon-sap-style", CM_ADDONS_URL . "sap-id/assets/css/style.css", [], CM_ADDON_SAP_VER);
}



add_action("cm_after_comment_form_fields", "show_sap_id_field");
function show_sap_id_field($args)
{

    //We need to determine if this page/post should display sap-id addon
    $options = Comment_Manager::instance()->getCustomOptionsForPost($args['post_id']);
    if (!empty($options['settings']) && !in_array("sap-id", $options['settings']->addons)) {
        return;
    }

    ob_start();
    include "templates/main.php";
    echo ob_get_clean();
}



add_filter("cm-bafore-comment-save", "change_user_based_on_sap");
function change_user_based_on_sap($commentdata)
{
    $sapID = empty($_REQUEST['sap_id']) ? null : intval($_REQUEST['sap_id']);

    if (!$sapID) {
        return $commentdata;
    }

    // Query user by SAP_ID
    $user_query = new WP_User_Query(array(
        'meta_key' => 'sap',
        'meta_value' => $sapID,
        'number' => 1,
    ));

    if (!empty($user_query->results)) {
        $userID = intval($user_query->results[0]->ID);
        $user = $user_query->results[0]->data;

        $commentdata['user_id'] = $userID;
        $commentdata['comment_author'] = $user->display_name;
        $commentdata['comment_author_email'] = $user->user_email;
        $commentdata['comment_author_url'] = $user->user_url;
    }

    return $commentdata;
}



/**
 * This will render admin options for this addon
 */
add_action("cm-admin-addon-options-page", function ($args) {
    global $wp_roles;
    $roles = [];
    foreach($wp_roles->roles as $key => $item) {
        $roles[] = $key;
    }
    $mainVar = $args['variable_name'];
    $sapIDOptions = $args['options'];
    $pID = $args['page_id'];
?>
    <div class="cm-admin-addon-sap-id-wrapper">
        <div class="cm-admin-addon-sap-id-sub-wrapper">
            <label for="cm-addon-sap-id-title">SAP ID Title</label>
            <input type="text" name="<?php echo $mainVar . '[cm-addon-sap-id-title_' . $pID . ']'; ?>" id="cm-addon-sap-id-title_<?php echo $pID; ?>" value="<?php echo esc_html($sapIDOptions['cm-addon-sap-id-title_' . $pID]); ?>" />
        </div>

        <div class="cm-admin-addon-sap-id-sub-wrapper">
            <label for="cm-addon-sap-id-placeholder">SAP ID Placeholder</label>
            <input type="text" name="<?php echo $mainVar . '[cm-addon-sap-id-placeholder_' . $pID . ']'; ?>" id="cm-addon-sap-id-placeholder_<?php echo $pID; ?>" value="<?php echo esc_html($sapIDOptions['cm-addon-sap-id-placeholder_' . $pID]); ?>" />
        </div>

        <label for="cm-addon-sap-id-placeholder">SAP ID Roles</label>
        <input  type="hidden" 
                name="<?php echo $mainVar . '[cm-addon-sap-id-roles_' . $pID . ']'; ?>" id="cm-addon-sap-id-roles_<?php echo $pID; ?>" 
                value="<?php echo (empty($sapIDOptions['cm-addon-sap-id-roles_' . $pID]) || 
                                    $sapIDOptions['cm-addon-sap-id-roles_' . $pID] == "[]"  ? 
                                    '["administrator"]' : 
                                    esc_html($sapIDOptions['cm-addon-sap-id-roles_' . $pID])); ?>" 
                />
        <?php do_action("cm_custom_tag_list", 
                                [
                                    "data" => $roles, 
                                    "result_element_id" => 'cm-addon-sap-id-roles_' . $pID, 
                                    "existing" => json_decode($sapIDOptions['cm-addon-sap-id-roles_' . $pID] ?? "[]"),
                                    "special" => "administrator",
                                ]); ?>
    </div>
<?php
});
