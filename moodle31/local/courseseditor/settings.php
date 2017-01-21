<?php

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    // create the settings page
    $settings = new admin_settingpage('local_courseseditor', get_string('pluginname', 'local_courseseditor'));

    // add the settings page to the admin menu
    $ADMIN->add('localplugins', $settings);

    /*
     * Header
     */

    // the plugin name
    $plugin_header = new admin_setting_heading('local_courseseditor_settings', '', '');

    /*
     * Web service settings
     */

    // the supsi server address
    $supsi_host_config_name = 'local_courseseditor/supsi_host';
    $supsi_host_name = get_string('supsi_host_name', 'local_courseseditor');
    $supsi_host_description = get_string('supsi_host_description', 'local_courseseditor');
    $default_location = 'http://localhost:8888/moodle31/local/courseseditor/ws_per_test/supsi.php';
    $supsi_host_text = new admin_setting_configtext($supsi_host_config_name,
        $supsi_host_name,
        $supsi_host_description ,
        $default_location,
        PARAM_TEXT);

// the usi server address
    $usi_host_config_name = 'local_courseseditor/usi_host';
    $usi_host_name = get_string('usi_host_name', 'local_courseseditor');
    $usi_host_description = get_string('usi_host_description', 'local_courseseditor');
    $default_location = 'http://localhost:8888/moodle31/local/courseseditor/ws_per_test/usi.php';
    $usi_host_text = new admin_setting_configtext($usi_host_config_name,
        $usi_host_name,
        $usi_host_description ,
        $default_location,
        PARAM_TEXT);


    /*
   * Mail notification settings
   */

    // mail address receiver
    $config_name = 'local_courseseditor/to_mail';
    $name = get_string('to_mail_name', 'local_courseseditor');
    $description = get_string('to_mail_description', 'local_courseseditor');
    $default = 'rezart.lohja@student.supsi.ch,marzio.stampanoni@student.supsi.ch';
    $to_mail_text = new admin_setting_configtext($config_name, $name, $description , $default, PARAM_TEXT);

    // mail address sender
    $config_name = 'local_courseseditor/from_mail';
    $name = get_string('from_mail_name', 'local_courseseditor');
    $description = get_string('from_mail_description', 'local_courseseditor');
    $default = 'moodle@moodle.moodle';
    $from_mail_text = new admin_setting_configtext($config_name, $name, $description , $default, PARAM_TEXT);

    // mail address receiver
    $config_name = 'local_courseseditor/subject_mail';
    $name = get_string('subject_mail_name', 'local_courseseditor');
    $description = get_string('subject_mail_description', 'local_courseseditor');
    $default = 'Nuova richiesta modifiche corsi';
    $subject_mail_text = new admin_setting_configtext($config_name, $name, $description , $default, PARAM_TEXT);

    // mail address receiver
    $config_name = 'local_courseseditor/message_mail';
    $name = get_string('message_mail_name', 'local_courseseditor');
    $description = get_string('message_mail_description', 'local_courseseditor');
    $default = 'Ci sono nuove richiesti di modifiche corsi su moodle.';
    $message_mail_text = new admin_setting_configtext($config_name, $name, $description , $default, PARAM_TEXT);

    /*
     * Compose the settings page
     */

    $settings->add($plugin_header);
    $settings->add($supsi_host_text);
    $settings->add($usi_host_text);
    $settings->add($to_mail_text);
    $settings->add($from_mail_text);
    $settings->add($subject_mail_text);
    $settings->add($message_mail_text);
}
