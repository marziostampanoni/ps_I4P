<?php

namespace enrol_supsifb\event;

defined('MOODLE_INTERNAL') || die();

class enrol_students_first_time_completed extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'c'; 
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'enrol';
    }

    public static function get_name() {
        return get_string('enrol_students_first_time_completed_event', 'enrol_supsifb');
    }

    public function get_description() {
        $description  = "The SUPSI students of the course with id ";
        $description .= "'" . $this->other['supsifb_module_id'] . "'";
        $description .= "(" . $this->other['supsifb_module_name'] . ")";
        $description .= " have been enrolled in the Moodle course ";
        $description .= " with id '" . $this->courseid . "'.";

        return $description;
    }
}

