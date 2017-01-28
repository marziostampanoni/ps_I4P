<?php

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    // create the settings page
    $settings = new admin_settingpage('local_requestmanager', get_string('pluginname', 'local_requestmanager'));

    // add the settings page to the admin menu
    $ADMIN->add('localplugins', $settings);

    /*
     * Header
     */

    // the plugin name
    $plugin_header = new admin_setting_heading('local_requestmanager_settings', '', '');

    /*
     * Web service settings
     */

    // the supsi server address
    $config_name = 'local_requestmanager/supsi_host';
    $name = get_string('supsi_host_name', 'local_requestmanager');
    $description = get_string('supsi_host_description', 'local_requestmanager');
    $default = 'http://localhost:8888/moodle31/local/requestmanager/ws_per_test/supsi.php';
    $supsi_host_text = new admin_setting_configtext($config_name, $name, $description , $default, PARAM_TEXT);

    // the supsi key private for ws access
    $config_name = 'local_requestmanager/supsi_private_key';
    $name = get_string('supsi_private_key_name', 'local_requestmanager');
    $description = get_string('supsi_private_key_description', 'local_requestmanager');
    $default = 'supsi_key_sha_256';
    $supsi_private_key_text = new admin_setting_configtext($config_name, $name, $description , $default, PARAM_TEXT);


    // the usi server address
    $config_name = 'local_requestmanager/usi_host';
    $name = get_string('usi_host_name', 'local_requestmanager');
    $description = get_string('usi_host_description', 'local_requestmanager');
    $default = 'http://localhost:8888/moodle31/local/requestmanager/ws_per_test/usi.php';
    $usi_host_text = new admin_setting_configtext($config_name, $name, $description, $default, PARAM_TEXT);

    // the usi key private for ws access
    $config_name = 'local_requestmanager/usi_private_key';
    $name = get_string('usi_private_key_name', 'local_requestmanager');
    $description = get_string('usi_private_key_description', 'local_requestmanager');
    $default = 'usi_key_sha_256';
    $usi_private_key_text = new admin_setting_configtext($config_name, $name, $description, $default, PARAM_TEXT);


    /*
   * Notification from user to managers
   */

    // notify manager by mail?
    $config_name = 'local_requestmanager/notify_manager_by_mail';
    $name = get_string('notify_manager_by_mail_name', 'local_requestmanager');
    $description = get_string('notify_manager_by_mail_description', 'local_requestmanager');
    $default = 1;
    $notify_manager_by_mail_bool = new admin_setting_configcheckbox($config_name, $name, $description , $default);

    // mail subject
    $config_name = 'local_requestmanager/subject_mail';
    $name = get_string('subject_mail_name', 'local_requestmanager');
    $description = get_string('subject_mail_description', 'local_requestmanager');
    $default = get_string('new_request_subject', 'local_requestmanager');
    $subject_mail_text = new admin_setting_configtext($config_name, $name, $description , $default, PARAM_TEXT);

    // mail message
    $config_name = 'local_requestmanager/message_mail';
    $name = get_string('message_mail_name', 'local_requestmanager');
    $description = get_string('message_mail_description', 'local_requestmanager');
    $default = get_string('new_request_message', 'local_requestmanager');
    $message_mail_text = new admin_setting_configtextarea($config_name, $name, $description , $default);

    /**
     *Notification from manager to user
     */

    // notify user by mail?
    $config_name = 'local_requestmanager/notify_user_by_mail';
    $name = get_string('notify_user_by_mail_name', 'local_requestmanager');
    $description = get_string('notify_user_by_mail_description', 'local_requestmanager');
    $default = 1;
    $notify_user_by_mail_bool = new admin_setting_configcheckbox($config_name, $name, $description , $default);

    // mail subject of the notification of a clone executed
    $config_name = 'local_requestmanager/subject_clone_mail';
    $name = get_string('subject_clone_mail_name', 'local_requestmanager');
    $description = get_string('subject_clone_mail_description', 'local_requestmanager');
    $default = get_string('subject_clone_mail_default', 'local_requestmanager');
    $subject_clone_mail_text = new admin_setting_configtext($config_name, $name, $description , $default, PARAM_TEXT);

    // mail message of the notification of a clone executed
    $config_name = 'local_requestmanager/message_clone_mail';
    $name = get_string('message_clone_mail_name', 'local_requestmanager');
    $description = get_string('message_clone_mail_description', 'local_requestmanager');
    $default = get_string('message_clone_mail_default', 'local_requestmanager');
    $message_clone_mail_clone_text = new admin_setting_configtextarea($config_name, $name, $description , $default);

    // mail subject of the notification of a new course inserted
    $config_name = 'local_requestmanager/subject_new_course_mail';
    $name = get_string('subject_new_course_mail_name', 'local_requestmanager');
    $description = get_string('subject_new_course_mail_description', 'local_requestmanager');
    $default = get_string('subject_new_course_mail_default', 'local_requestmanager');
    $subject_new_course_mail_text = new admin_setting_configtext($config_name, $name, $description , $default, PARAM_TEXT);

    // mail message of the notification of a new course inserted
    $config_name = 'local_requestmanager/message_new_course_mail';
    $name = get_string('message_new_course_mail_name', 'local_requestmanager');
    $description = get_string('message_new_course_mail_description', 'local_requestmanager');
    $default = get_string('message_new_course_mail_default', 'local_requestmanager');
    $message_new_course_mail_clone_text = new admin_setting_configtextarea($config_name, $name, $description , $default);

    // mail subject of the notification of a delete executed
    $config_name = 'local_requestmanager/subject_delete_mail';
    $name = get_string('subject_delete_mail_name', 'local_requestmanager');
    $description = get_string('subject_delete_mail_description', 'local_requestmanager');
    $default = get_string('subject_delete_mail_default', 'local_requestmanager');
    $subject_delete_mail_text = new admin_setting_configtext($config_name, $name, $description , $default, PARAM_TEXT);

    // mail message of the notification of a delete executed
    $config_name = 'local_requestmanager/message_delete_mail';
    $name = get_string('message_delete_mail_name', 'local_requestmanager');
    $description = get_string('message_delete_mail_description', 'local_requestmanager');
    $default = get_string('message_delete_mail_default', 'local_requestmanager');
    $message_delete_mail_clone_text = new admin_setting_configtextarea($config_name, $name, $description , $default);

    // mail subject of the notification of a rejected request
    $config_name = 'local_requestmanager/subject_reject_mail';
    $name = get_string('subject_reject_mail_name', 'local_requestmanager');
    $description = get_string('subject_reject_mail_description', 'local_requestmanager');
    $default = get_string('subject_reject_mail_default', 'local_requestmanager');
    $subject_reject_mail_text = new admin_setting_configtext($config_name, $name, $description , $default, PARAM_TEXT);

    // mail message of the notification of a rejected request
    $config_name = 'local_requestmanager/message_reject_mail';
    $name = get_string('message_reject_mail_name', 'local_requestmanager');
    $description = get_string('message_reject_mail_description', 'local_requestmanager');
    $default = get_string('message_reject_mail_default', 'local_requestmanager');
    $message_reject_mail_clone_text = new admin_setting_configtextarea($config_name, $name, $description , $default);



    /*
     * Compose the settings page
     */

    $settings->add($plugin_header);
    $settings->add($supsi_host_text);
    $settings->add($supsi_private_key_text);
    $settings->add($usi_host_text);
    $settings->add($usi_private_key_text);
    $settings->add($notify_manager_by_mail_bool);
    $settings->add($subject_mail_text);
    $settings->add($message_mail_text);
    $settings->add($notify_user_by_mail_bool);
    $settings->add($subject_clone_mail_text);
    $settings->add($message_clone_mail_clone_text);
    $settings->add($subject_new_course_mail_text);
    $settings->add($message_new_course_mail_clone_text);
    $settings->add($subject_delete_mail_text);
    $settings->add($message_delete_mail_clone_text);
    $settings->add($subject_reject_mail_text);
    $settings->add($message_reject_mail_clone_text);


}


