<?php

namespace enrol_supsifb\event;

defined('MOODLE_INTERNAL') || die();

class enrol_students_first_time_started extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'c'; 
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public static function get_name() {
        return get_string('enrol_students_first_time_started_event', 'enrol_supsifb');
    }

    public function get_description() {
        $description  = "The enrolment of the SUPSI students (SUPSI course with id ";
        $description .= "'" . $this->other['supsifb_module_id'] . "'";
        $description .= ", " . $this->other['supsifb_module_name'] . ")";
        $description .= " in the Moodle course";
        $description .= " with id '" . $this->courseid . "'";
        $description .= " is started.";

        return $description;
    }
}

