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
 * This file starts the synchronization of the course identified by the ID given 
 * as argument.
 *
 * @package    enrol_usi
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

// make use of the DB API
global $DB;

/*
 * Permissions check
 */

// get the identifiers of the enrol and course instances
$enrol_id = required_param('enrol_id', PARAM_INT);
$course_id = required_param('course_id', PARAM_INT);

// retrieve the course and its context from the DB 
$course = $DB->get_record('course', array('id' => $course_id), '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);

// check that the user is logged in
require_login($course);

// check that the user has the capabilities to access that page
require_capability('moodle/course:enrolconfig', $context);
require_capability('enrol/usi:config', $context);

// synchronize the given enrol instance
$enrol = enrol_get_plugin('usi');
$enrol->synchronize_plugin_enrolment($enrol_id);

// redirect to the proper page
redirect(new moodle_url('/enrol/instances.php', array('id'=>$course->id)));

