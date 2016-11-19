<?php

namespace enrol_usi\event;

defined('MOODLE_INTERNAL') || die();

class enrol_students_first_time_started extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'c'; 
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public static function get_name() {
        return get_string('enrol_students_first_time_started_event', 'enrol_usi');
    }

    public function get_description() {
        $description  = "The enrolment of the USI students (USI course with id ";
        $description .= "'" . $this->other['usi_module_id'] . "'";
        $description .= ", " . $this->other['usi_module_name'] . ")";
        $description .= " in the Moodle course";
        $description .= " with id '" . $this->courseid . "'";
        $description .= " is started.";

        return $description;
    }
}

