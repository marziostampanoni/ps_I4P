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
    $plugin_description = get_string('pluginname_desc', 'local_courseseditor');
    $plugin_header = new admin_setting_heading('local_courseseditor_settings', '', $plugin_description);

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
     * Compose the settings page
     */

    $settings->add($plugin_header);
    $settings->add($supsi_host_text);
    $settings->add($usi_host_text);
}
