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
 * Here there is the form class definition that is used to select the USI 
 * students to enrol in a defined Moodle course.
 *
 * @package    enrol_usi
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_usi\forms;

// include the lib that allows to create HTML forms
require_once("$CFG->libdir/formslib.php");

/**
 * This class defines the form used to enrol the USI students in a given course. 
 */
class users_sync_form extends \moodleform {
    protected $course;

    /**
     * @param $values the resources to make selectable
     * 
     * @return array an associative array which contains the given resources
     *               as key-value bindings.
     */
    private function makeSelectable($values) {
        $result = array();
        foreach ($values as $attr) {
            foreach ($attr->as_map() as $key => $value) {
                $result[$key . ' '] = $value;
            }
        }

        return $result;
    }

    /**
     * @param $options the original options
     * @param $text the text value of the new option
     * @param $value the value of the new option
     *
     * @return array an associative array with the new option added
     */
    private function addOption($options, $text, $value) {
        return array_merge(array($text => $value), $options);
    }

    private function addChooseOption($options) {
        $dots = get_string('choosedots');
        return $this->addOption($options, $dots, $dots);
    }

    function definition() {
        global $CFG, $DB;

        // retrieve the internal field on which the form is built
        $mform  = $this->_form;
        $course = $this->_customdata;
        $this->course = $course;

        // add the form header
        $mform->addElement('header','general', get_string('pluginname', 'enrol_usi'));

        // create a web service object to interrogate the USI Web Service
        $usi_ws = null;
        try {
            $usi_ws = \enrol_usi\ws\factory::create();
        } catch (\enrol_usi\ws\ws_creation_failed_exception $fault) {
            // trigger an event to signal the failure
            \enrol_usi\event\ws_creation_failed::create(array(
                'context' => context_course::instance($course->id),
                'other' => array('error_message' => $fault->getMessage())
            ))->trigger();
        }

        /* 
         * Retrieve the remote values for: student types, faculties and 
         * semesters
         * If there is an error, stop the form generation and returns that 
         * error.
         */
        $raw_student_types = array();
        $raw_faculties = array();
        $raw_semesters = array();
        $other_errors_happened = false;
        if ($usi_ws != null) {
            try {
                $raw_student_types = $usi_ws->get_student_types();
                $raw_faculties = $usi_ws->get_faculties();
                $raw_semesters = $usi_ws->get_semesters();
            } catch (\enrol_usi\ws\remote_call_failed_exception $fault) {
                // trigger an event to signal the Web Service failure
                \enrol_usi\event\ws_remote_call_failed::create(array(
                    'context' => context_course::instance($course->id),
                    'other' => array('error_message' => $fault->getMessage())
                ))->trigger();

                $other_errors_happened = true;
            }
        }

        // the Web Service is not available, thus I can't output the form
        if ($usi_ws == null || $other_errors_happened) {
            $error_message = get_string('ws_not_available', 'enrol_usi');
            $mform->addElement('html', "<h3>$error_message</h3>");
        }
        else {
            // the Web Service is available, thus I can create the right form

            // add the student type select box
            $student_types = $this->addChooseOption($this->makeSelectable($raw_student_types));
            $mform->addElement('select', 'target_student_type', get_string('target_student_type', 'enrol_usi'), $student_types);
            
            // add the faculty select box
            $faculties = $this->addChooseOption($this->makeSelectable($raw_faculties));
            $mform->addElement('select', 'target_faculty', get_string('target_faculty', 'enrol_usi'), $faculties);

            // add the semester select box
            $semesters = $this->addChooseOption($this->makeSelectable($raw_semesters));
            $mform->addElement('select', 'target_semester', get_string('target_semester', 'enrol_usi'), $semesters);

            // add the target module select box
            $modules = $this->addChooseOption(array());
            $mform->addElement('select', 'target_module', get_string('target_module', 'enrol_usi'), $modules);

            // add the number of students label
            $numberOfStudentsLabel  = '<h3>';
            $numberOfStudentsLabel .= get_string('number_of_students', 'enrol_usi');
            $numberOfStudentsLabel .= ': <span id="number_of_students">0</span>';
            $numberOfStudentsLabel .= '</h3>'; 
            $mform->addElement('html', $numberOfStudentsLabel);

            // add the ID of the Moodle course on which we are working
            $mform->addElement('hidden', 'id', null);
            $mform->setType('id', PARAM_INT);

            // add the field that will hold the unique IDs of the chosen course students
            $mform->addElement('hidden', 'unique_ids', '');
            $mform->setType('unique_ids', PARAM_TEXT);

            // add the field that will hold the name of the chosen module
            $mform->addElement('hidden', 'target_module_name', '');
            $mform->setType('target_module_name', PARAM_TEXT);

            // add the submit button
            $this->add_action_buttons(true, get_string('enrol_students', 'enrol_usi'));

            // set the course id parameter
            $this->set_data(array('id'=>$course->id));
        }
    }
}

