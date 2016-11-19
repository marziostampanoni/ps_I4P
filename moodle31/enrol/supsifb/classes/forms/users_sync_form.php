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
 * Here there is the form class definition that is used to select the SUPSI 
 * students to enrol in a defined Moodle course.
 *
 * @package    enrol_supsifb
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_supsifb\forms;

// include the lib that allows to create HTML forms
require_once("$CFG->libdir/formslib.php");

/**
 * This class defines the form used to enrol the SUPSI students in a given course. 
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
        $mform->addElement('header','general', get_string('pluginname', 'enrol_supsifb'));

        // create a web service object to interrogate the SUPSI Web Service
        $supsifb_ws = null;
        try {
            $supsifb_ws = \enrol_supsifb\ws\factory::create();
        } catch (\enrol_supsifb\ws\ws_creation_failed_exception $fault) {
            // trigger an event to signal the failure
            \enrol_supsifb\event\ws_creation_failed::create(array(
                'context' => \context_course::instance($course->id),
                'other' => array('error_message' => $fault->getMessage())
            ))->trigger();
        }

        /* 
		 * Retrieve the remote values for: academic years, academic units
		 * and curricula.
         * If there is an error, stop the form generation and returns that 
         * error.
         */
        $raw_academic_years = array();
        $other_errors_happened = false;
        if ($supsifb_ws != null) {
            try {
                $raw_academic_years = $supsifb_ws->get_academic_years();
            } catch (\enrol_supsifb\ws\remote_call_failed_exception $fault) {
                // trigger an event to signal the Web Service failure
                \enrol_supsifb\event\ws_remote_call_failed::create(array(
                    'context' => \context_course::instance($course->id),
                    'other' => array('error_message' => $fault->getMessage())
                ))->trigger();

                $other_errors_happened = true;
            }
        }

        // the Web Service is not available, thus I can't output the form
        if ($supsifb_ws == null || $other_errors_happened) {
            $error_message = get_string('ws_not_available', 'enrol_supsifb');
            $mform->addElement('html', "<h3>$error_message</h3>");
        }
        else {
            // the Web Service is available, thus I can create the right form

            // add the accademic year select box
            $academic_years = $this->addChooseOption($this->makeSelectable($raw_academic_years));
            $mform->addElement('select', 'target_academic_year', get_string('target_academic_year', 'enrol_supsifb'), $academic_years);
 
			// add the accademic unit box
			$raw_academic_units = array();
            $academic_units = $this->addChooseOption($this->makeSelectable($raw_academic_units));
            $mform->addElement('select', 'target_academic_unit', get_string('target_academic_unit', 'enrol_supsifb'), $academic_units);

            // add the curricula select box
			$raw_curricula = array();
            $curricula = $this->addChooseOption($this->makeSelectable($raw_curricula));
            $mform->addElement('select', 'target_curricula', get_string('target_curricula', 'enrol_supsifb'), $curricula);

            // add the target module select box
            $modules = $this->addChooseOption(array());
            $mform->addElement('select', 'target_module', get_string('target_module', 'enrol_supsifb'), $modules);

            // add the number of students label
            $numberOfStudentsLabel  = '<h3>';
            $numberOfStudentsLabel .= get_string('number_of_students', 'enrol_supsifb');
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
            $this->add_action_buttons(true, get_string('enrol_students', 'enrol_supsifb'));

            // set the course id parameter
            $this->set_data(array('id'=>$course->id));
        }
    }
}

