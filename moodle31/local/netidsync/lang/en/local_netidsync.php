<?php
/**
 * This file contains all the strings seen by the end user while using this 
 * plugin.
 * 
 * @package    local_netidsync
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Plugin
 */

$string['pluginname'] = 'NetID Users Sync';
$string['pluginname_desc'] = 'This plugin synchronizes the Moodle DB with the NetID LDAP tree.';

/**
 * Settings
 */

$string['ldap_host_name'] = 'LDAP Host';
$string['ldap_host_description'] = 'The address of the LDAP server';
$string['ldap_port_name'] = 'LDAP Server Port';
$string['ldap_port_description'] = 'The port number on which the LDAP server is listening';
$string['ldap_binddn_name'] = 'LDAP Bind DN';
$string['ldap_binddn_description'] = 'The DN to use for the LDAP binding';
$string['ldap_password_name'] = 'LDAP Password';
$string['ldap_password_description'] = 'The password to use for the LDAP binding';
$string['ldap_protocol_version_name'] = 'LDAP Protocol Version';
$string['ldap_protocol_version_description'] = 'The version number of the LDAP protocol';
$string['ldap_domain_components_name'] = 'LDAP Domain Components';
$string['ldap_domain_components_description'] = 'The domain components on which the search must be performed';

/**
 * Events
 */

$string['netid_synchronization_started_event'] = 'NetID synchronization started';
$string['netid_synchronization_started_message'] = 'The NetID users synchronization is started'; 
$string['netid_synchronization_completed_event'] = 'NetID synchronization completed';
$string['netid_synchronization_completed_message'] = 'The NetID users synchronization completed successfully'; 
$string['netid_synchronization_failed_event'] = 'NetID synchronization failed';
$string['netid_synchronization_failed_message'] = 'The NetID users synchronization failed because of an error'; 

/**
 * Tasks
 */
$string['netid_synchronization_task'] = 'NetID Synchronization';
