<?php
/**
 * This file contains all the strings seen by the end user while using this 
 * plugin.
 *
 * @package    enrol_usi
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Plugin
 */

$string['pluginname'] = 'USI Students Sync';
$string['pluginname_desc'] = 'This page allows you to define the parameters used by USI plugin';

/**
 * Enrolment form
 */

$string['target_module'] = 'Select a module';
$string['target_student_type'] = 'Select a student type';
$string['target_faculty'] = 'Select a faculty';
$string['target_semester'] = 'Select a semester';
$string['enrol_students'] = 'Enrol students';
$string['number_of_students'] = 'Number of students';
$string['table_uniqueid'] = 'UniqueID';
$string['ws_not_available'] = 'The USI Web Service is not available, please retry later.';

/**
 * Settings
 */

$string['usi_wsdl_name'] = 'USI WSDL location';
$string['usi_wsdl_description'] = 'Location of the USI Web Service WSDL';
$string['usi_ws_key_name'] = 'USI WS Access Key';
$string['usi_ws_key_description'] = 'Key used to access the USI Web Service';
$string['cached_ws_flag_name'] = 'USI WS Caching';
$string['cached_ws_flag_description'] = 'Enable/disable the web service caching mechanism';
$string['cache_ttl_name'] = 'USI WS Cache TTL';
$string['cache_ttl_description'] = 'The number of hours that determines when a cache entry must be updated';

/**
 * Tasks
 */

$string['ws_cache_cleanup_task'] = 'USI Web Service Cache Cleanup';
$string['enrolments_sync_task'] = 'USI Enrolments Synchronization';

/**
 * Events
 */

// web service
$string['ws_creation_failed_event'] = 'USI Web Service creation failed';
$string['ws_remote_call_failed_event'] = 'USI Web Service remote call failed';
$string['ws_cache_cleanup_executed_event'] = 'USI Web Service cache cleanup executed';

// USI students enrolment
$string['enrol_students_first_time_started_event'] = 'New USI Students enrolment started';
$string['enrol_students_first_time_completed_event'] = 'New USI Students enrolment completed';
$string['enrol_students_first_time_failed_event'] = 'New USI Students enrolment failed';

// plugin single-course synchronization
$string['plugin_enrolment_synchronization_started_event'] = 'Single course enrolments synchronization started';
$string['plugin_enrolment_synchronization_completed_event'] = 'Single course enrolments synchronization completed successfully';
$string['plugin_enrolment_synchronization_failed_event'] = 'Single course enrolments synchronization failed';

// plugin all-courses synchronization
$string['plugin_enrolment_mass_synchronization_started_event'] = 'All courses enrolments synchronization started';
$string['plugin_enrolment_mass_synchronization_completed_event'] = 'All courses enrolments synchronization completed successfully';
$string['plugin_enrolment_mass_synchronization_failed_event'] = 'All courses enrolments synchronization failed';

