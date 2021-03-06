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
 * Returns the modules given the semester ID, the faculty ID and the student type ID.
 * The course_id must be given in order to check the user capabilities.
 *
 * Parameters:
 *  1. course_id
 *  2. semster_id
 *  3. faculty_id
 *  4. student_type_id
 *
 * @package    enrol_usi
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");

/*
 * Permissions check
 */

// get the ID of the course on which the user is working
$id = required_param('course_id', PARAM_INT);

// retrieve the course and its context from the DB 
$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);

// check that the user is logged in
require_login($course);

// check that the user has the capabilities to access that page
require_capability('moodle/course:enrolconfig', $context);
require_capability('enrol/usi:config', $context);

/*
 * Parameters extraction
 */

// get the required parameters
$semester_id = required_param('semester_id', PARAM_INT);
$faculty_id = required_param('faculty_id', PARAM_INT);
$student_type_id = required_param('student_type_id', PARAM_INT);

/*
 * Request processing
 */

// retrieve the required modules from the USI Web Service
$usi_ws = null;
$result = null;
try {
    $usi_ws = \enrol_usi\ws\factory::create();
} catch (\enrol_usi\ws\ws_creation_failed_exception $fault) {
    // trigger an event to signal the failure
    \enrol_usi\event\ws_creation_failed::create(array(
        'context' => $context,
        'other' => array('error_message' => $fault->getMessage())
    ))->trigger();

    // set the result so that it says that the web service is not available
    $result = array('error' => 'The USI Web Service is not available: ' . $fault->getMessage());
}

// if the web service is available, then get the modules
if ($usi_ws != null && $result == null) {
    try {
        $result = $usi_ws->get_modules($semester_id, $faculty_id, $student_type_id);
    } catch (\enrol_usi\ws\remote_call_failed_exception $fault) {
        // trigger an event to signal the Web Service failure
        \enrol_usi\event\ws_remote_call_failed::create(array(
            'context' => context_course::instance($context),
            'other' => array('error_message' => $fault->getMessage())
        ))->trigger();

        // set the result so that it says that the web service is not available
        $result = array('error' => 'The USI Web Service is not available: ' . $fault->getMessage());
    }
}

/*
 * Results output
 */

// return the JSON representation of the modules
echo json_encode($result);

