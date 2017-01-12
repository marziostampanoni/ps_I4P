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
 * @package    local_netidsync
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // create the settings page
    $settings = new admin_settingpage('local_netidsync', get_string('pluginname', 'local_netidsync'));

    // add the settings page to the admin menu
    $ADMIN->add('localplugins', $settings);

    /*
     * Header
     */

    // the plugin name
    $plugin_description = get_string('pluginname_desc', 'local_netidsync'); 
    $plugin_header = new admin_setting_heading('local_netidsync_settings', '', $plugin_description);

    /*
     * LDAP settings
     */
    
    // the LDAP server address
    $ldap_host_config_name = 'local_netidsync/ldap_host';
    $ldap_host_name = get_string('ldap_host_name', 'local_netidsync');
    $ldap_host_description = get_string('ldap_host_description', 'local_netidsync');
    $default_location = 'niobio.ti-edu.ch';
    $ldap_host_text = new admin_setting_configtext($ldap_host_config_name,
                                                   $ldap_host_name,
                                                   $ldap_host_description ,
                                                   $default_location,
                                                   PARAM_TEXT);

    // the LDAP server port
    $ldap_port_config_name = 'local_netidsync/ldap_port';
    $ldap_port_name = get_string('ldap_port_name', 'local_netidsync');
    $ldap_port_description = get_string('ldap_port_description', 'local_netidsync');
    $default_port = 389;
    $ldap_port_text = new admin_setting_configtext($ldap_port_config_name,
                                                   $ldap_port_name,
                                                   $ldap_port_description,
                                                   $default_port,
                                                   PARAM_INT);

    // the LDAP bind dn
    $ldap_binddn_config_name = 'local_netidsync/ldap_binddn';
    $ldap_binddn_name = get_string('ldap_binddn_name', 'local_netidsync');
    $ldap_binddn_description = get_string('ldap_binddn_description', 'local_netidsync');
    $default_value = 'uid=argon_icorsi_test2,dc=infrastructure,dc=ch';
    $ldap_binddn_text = new admin_setting_configtext($ldap_binddn_config_name,
                                                     $ldap_binddn_name,
                                                     $ldap_binddn_description,
                                                     $default_value,
                                                     PARAM_TEXT);

    // the LDAP password
    $ldap_password_config_name = 'local_netidsync/ldap_password';
    $ldap_password_name = get_string('ldap_password_name', 'local_netidsync');
    $ldap_password_description = get_string('ldap_password_description', 'local_netidsync');
    $default_value = null;
    $ldap_password_text = new admin_setting_configtext($ldap_password_config_name,
                                                       $ldap_password_name,
                                                       $ldap_password_description,
                                                       $default_value,
                                                       PARAM_TEXT);

    // the LDAP protocol version
    $ldap_protocol_version_config_name = 'local_netidsync/ldap_protocol_version';
    $ldap_protocol_version_name = get_string('ldap_protocol_version_name', 'local_netidsync');
    $ldap_protocol_version_description = get_string('ldap_protocol_version_description', 'local_netidsync');
    $default_protocol_version = 3;
    $ldap_protocol_version_text = new admin_setting_configtext($ldap_protocol_version_config_name,
                                                               $ldap_protocol_version_name,
                                                               $ldap_protocol_version_description,
                                                               $default_protocol_version,
                                                               PARAM_INT);

    // the LDAP domain components on which the search must be executed 
	// the field is a text and the domain components must be separated by a 
	// comma
	// example: supsi,unisi,teologialugano
	//	=> DC: supsi,ch
	//	=> DC: unisi,ch
	//	=> DC: teologialugano,ch
	//
	//	The domain 'ch' is assumed to be the root
    $ldap_dcs_config_name = 'local_netidsync/ldap_domain_components';
    $ldap_dcs_name = get_string('ldap_domain_components_name', 'local_netidsync');
    $ldap_dcs_description = get_string('ldap_domain_components_description', 'local_netidsync');
    $default_value = 'supsi,unisi,teologialugano';
    $ldap_dcs_text = new admin_setting_configtext($ldap_dcs_config_name,
                                                  $ldap_dcs_name,
                                                  $ldap_dcs_description,
                                                  $default_value,
                                                  PARAM_TEXT);



	/*
     * Compose the settings page
     */

    $settings->add($plugin_header);
    $settings->add($ldap_host_text);
    $settings->add($ldap_port_text);
    $settings->add($ldap_binddn_text);
    $settings->add($ldap_password_text);
    $settings->add($ldap_protocol_version_text);
    $settings->add($ldap_dcs_text);
}
