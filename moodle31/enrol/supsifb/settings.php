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
 * @package    enrol_supsifb
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
        $plugin_description = get_string('pluginname_desc', 'enrol_supsifb'); 
        $plugin_header = new admin_setting_heading('enrol_supsifb_settings', '', $plugin_description);

        /*
         * Moodle specific settings
         */

        // add the default role select box
        $options = get_default_enrol_roles(context_system::instance());
        $student = get_archetype_roles('student');
        $student = reset($student);
        $default_role = get_string('defaultrole', 'role'); 
        $default_role_select = new admin_setting_configselect('enrol_supsifb/roleid',
                                                              $default_role,
                                                              '',
                                                              $student->id,
                                                              $options);

        /*
         * SUPSI Web Service settings
         */

        // the web service location
        $ws_location_config_name = 'enrol_supsifb/ws_location';
        $ws_location_name = get_string('ws_location_name', 'enrol_supsifb');
        $ws_location_description = get_string('ws_location_description', 'enrol_supsifb');
        $default_location = null;
        $ws_location_text = new admin_setting_configtext($ws_location_config_name,
						                                 $ws_location_name,
								                         $ws_location_description,
										                 $default_location,
											             PARAM_TEXT);

        // the web service username to use for the authentication
        $user_config_name = 'enrol_supsifb/ws_username';
        $user_name = get_string('ws_user_name', 'enrol_supsifb');
        $user_description = get_string('ws_user_description', 'enrol_supsifb');
        $default_user = null;
        $user_text = new admin_setting_configtext($user_config_name,
                                                  $user_name,
                                                  $user_description,
                                                  $default_user,
                                                  PARAM_TEXT);

        // the web service password to use for the authentication
        $password_config_name = 'enrol_supsifb/ws_password';
        $password_name = get_string('ws_password_name', 'enrol_supsifb');
        $password_description = get_string('ws_password_description', 'enrol_supsifb');
        $default_password = null;
        $password_text = new admin_setting_configtext($password_config_name,
													  $password_name,
													  $password_description,
													  $default_password,
													  PARAM_TEXT);
        /*
         * Compose the settings page
         */

        $settings->add($plugin_header);
        $settings->add($default_role_select);
        $settings->add($ws_location_text);
        $settings->add($user_text);
        $settings->add($password_text);
    }
}
