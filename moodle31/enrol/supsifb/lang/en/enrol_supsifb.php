<?php
/**
 * This file contains all the strings seen by the end user while using this 
 * plugin.
 *
 * @package    enrol_supsifb
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Plugin
 */

$string['pluginname'] = 'SUPSI Students Sync';
$string['pluginname_desc'] = 'This page allows you to define the parameters used by SUPSI plugin';

/**
 * Settings
 */

$string['ws_location_name'] = 'WS Location';
$string['ws_location_description'] = 'Location of the SUPSI Web Service';
$string['ws_user_name'] = 'WS Username';
$string['ws_user_description'] = 'Username needed to access the SUPSI Web Service';
$string['ws_password_name'] = 'WS Password';
$string['ws_password_description'] = 'Password needed to access the SUPSI Web Service';

/**
 * Enrolment form
 */

$string['target_academic_year'] = 'Select an academic year';
$string['target_academic_unit'] = 'Select an academic unit';
$string['target_curricula'] = 'Select a curricula';
$string['target_module'] = 'Select a module';
$string['enrol_students'] = 'Enrol students';
$string['number_of_students'] = 'Number of students';
$string['table_uniqueid'] = 'UniqueID';
$string['ws_not_available'] = 'The SUPSI Web Service is not available, please retry later.';

/**
 * Tasks
 */

$string['enrolments_sync_task'] = 'SUPSI Enrolments Synchronization';

/**
 * Events
 */

// web service
$string['ws_creation_failed_event'] = 'SUPSI Web Service creation failed';
$string['ws_remote_call_failed_event'] = 'SUPSI Web Service remote call failed';

// USI students enrolment
$string['enrol_students_first_time_started_event'] = 'New SUPSI Students enrolment started';
$string['enrol_students_first_time_completed_event'] = 'New SUPSI Students enrolment completed';
$string['enrol_students_first_time_failed_event'] = 'New SUPSI Students enrolment failed';

// plugin single-course synchronization
$string['plugin_enrolment_synchronization_started_event'] = 'Single course enrolments synchronization started';
$string['plugin_enrolment_synchronization_completed_event'] = 'Single course enrolments synchronization completed successfully';
$string['plugin_enrolment_synchronization_failed_event'] = 'Single course enrolments synchronization failed';

// plugin all-courses synchronization
$string['plugin_enrolment_mass_synchronization_started_event'] = 'All courses enrolments synchronization started';
$string['plugin_enrolment_mass_synchronization_completed_event'] = 'All courses enrolments synchronization completed successfully';
$string['plugin_enrolment_mass_synchronization_failed_event'] = 'All courses enrolments synchronization failed';

