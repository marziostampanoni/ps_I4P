<?php
/*
 * This file is part of Moodle - http://moodle.org/
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * This file defines the settings page that contains the parameters needed by the plugin.
 *
 * @package    enrol_usi
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    if (!during_initial_install()) {
        /*
         * Header
         */

        // the plugin name
        $plugin_description = get_string('pluginname_desc', 'enrol_usi'); 
        $plugin_header = new admin_setting_heading('enrol_usi_settings', '', $plugin_description);

        /*
         * Moodle specific settings
         */

        // add the default role select box
        $options = get_default_enrol_roles(context_system::instance());
        $student = get_archetype_roles('student');
        $student = reset($student);
        $default_role = get_string('defaultrole', 'role'); 
        $default_role_select = new admin_setting_configselect('enrol_usi/roleid',
                                                              $default_role,
                                                              '',
                                                              $student->id,
                                                              $options);

        /*
         * USI Web Service settings
         */

        // the WSDL location
        $wsdl_config_name = 'enrol_usi/usi_wsdl_location';
        $wsdl_name = get_string('usi_wsdl_name', 'enrol_usi');
        $wsdl_description = get_string('usi_wsdl_description', 'enrol_usi');
        $default_location = 'https://ssl.lu.usi.ch/wsModuliMoodle/service.asmx?WSDL';
        $wsdl_text = new admin_setting_configtext($wsdl_config_name,
                                                  $wsdl_name,
                                                  $wsdl_description,
                                                  $default_location,
                                                  PARAM_TEXT);

        // the WS access key
        $key_config_name = 'enrol_usi/usi_ws_key';
        $key_name = get_string('usi_ws_key_name', 'enrol_usi');
        $key_description = get_string('usi_ws_key_description', 'enrol_usi');
        $default_key = null;
        $key_text = new admin_setting_configtext($key_config_name,
                                                 $key_name,
                                                 $key_description,
                                                 $default_key,
                                                 PARAM_TEXT);

        /*
         * USI Web Service Caching settings
         */

        // the cache mechanism flag
        $cache_flag_config_name = 'enrol_usi/cached_ws_flag';
        $cache_flag_name = get_string('cached_ws_flag_name', 'enrol_usi');
        $cache_flag_description = get_string('cached_ws_flag_description', 'enrol_usi');
        $cache_checkbox = new admin_setting_configcheckbox($cache_flag_config_name,
                                                           $cache_flag_name,
                                                           $cache_flag_description,
                                                           1);

        // the cache TTL value
        $cache_ttl_config_name = 'enrol_usi/cache_ttl';
        $cache_ttl_name = get_string('cache_ttl_name', 'enrol_usi');
        $cache_ttl_description = get_string('cache_ttl_description', 'enrol_usi');
        $default_ttl = 23;
        $ttl_text = new admin_setting_configtext($cache_ttl_config_name,
                                                 $cache_ttl_name,
                                                 $cache_ttl_description,
                                                 $default_ttl,
                                                 PARAM_INT);

        /*
         * Compose the settings page
         */

        $settings->add($plugin_header);
        $settings->add($default_role_select);
        $settings->add($wsdl_text);
        $settings->add($key_text);
        $settings->add($cache_checkbox);
        $settings->add($ttl_text);
    }
}
