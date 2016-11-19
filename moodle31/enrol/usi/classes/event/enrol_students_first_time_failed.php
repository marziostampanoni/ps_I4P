<?php

namespace enrol_usi\event;

defined('MOODLE_INTERNAL') || die();

class enrol_students_first_time_failed extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'c'; 
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'enrol';
    }

    public static function get_name() {
        return get_string('enrol_students_first_time_failed_event', 'enrol_usi');
    }

    public function get_description() {
        $description  = "The USI students of the course with id ";
        $description .= "'" . $this->other['usi_module_id'] . "'";
        $description .= "(" . $this->other['usi_module_name'] . ")";
        $description .= " have NOT been enrolled in the Moodle course ";
        $description .= " with id '" . $this->courseid . "' because of an error during the mass enrolment action.";

        return $description;
    }
}

