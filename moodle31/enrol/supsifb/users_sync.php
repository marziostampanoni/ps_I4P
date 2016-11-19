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
 * This is the file from which the users can select the SUPSI students to enrol in 
 * their Moodle courses.
 *
 * @package    enrol_supsifb
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

/*
 * Global variables
 */
global $DB;

/*
 * Permissions check
 */

// check that the required parameters have been given
$id = required_param('id', PARAM_INT); // course id

// retrieve the context from the given course id
$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);

// check that the user is logged in
require_login($course);

// check that the user has the capabilities to access that page
require_capability('moodle/course:enrolconfig', $context);
require_capability('enrol/supsifb:config', $context);

/*
 * Page initialization
 */

// set the page URL
$PAGE->set_url('/enrol/supsifb/users_sync.php', array('id'=>$course->id));

// set the page layout
$PAGE->set_pagelayout('admin');

navigation_node::override_active_url(new moodle_url('/enrol/instances.php', array('id'=>$course->id)));

/*
 * Plugin operations
 */

$enrol = enrol_get_plugin('supsifb');
if (!$enrol->get_newinstance_link($course->id)) {
    redirect(new moodle_url('/enrol/instances.php', array('id'=>$course->id)));
}

// get a form instance
$mform = new \enrol_supsifb\forms\users_sync_form(NULL, $course, 'post', '', array('id'=>'supsifb_sync_form'));

// handle the form submit
if ($mform->is_cancelled()) {
    redirect(new moodle_url('/enrol/instances.php', array('id'=>$course->id)));
} else if ($data = $mform->get_data()) {

    // retrieve the chosen module information 
    $target_module = '';
    if (isset($data->target_module)) {
        $target_module = $data->target_module;
    } else {
        $target_module = $_POST['target_module'];
    }
    $target_module_name = $data->target_module_name;
 
    // decode the JSON field that contains the students unique IDs
    $students_ids = array_map(function($elem) { return $elem->id; }, json_decode($data->unique_ids));

    // enrol the students to the current course
    $enrol->enrol_students_first_time($students_ids, $course->id, $target_module, $target_module_name);

    // redirect to the proper page
    redirect(new moodle_url('/enrol/instances.php', array('id'=>$course->id)));
}

/*
 * HTML generation stuff
 */
 
$PAGE->set_heading($course->fullname);
$PAGE->set_title(get_string('pluginname', 'enrol_supsifb'));

// define the strings to pass to the YUI module
$PAGE->requires->string_for_js('choosedots', 'moodle'); 
$PAGE->requires->string_for_js('ws_not_available', 'enrol_supsifb'); 

// include the YUI module
$PAGE->requires->yui_module('moodle-enrol_supsifb-supsifbws', 'M.enrol_supsifb.supsifbws.init');

// output the HTML code
echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();

