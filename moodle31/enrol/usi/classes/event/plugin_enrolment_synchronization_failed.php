<?php

namespace enrol_usi\event;

defined('MOODLE_INTERNAL') || die();

class plugin_enrolment_synchronization_failed extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'u'; 
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public static function get_name() {
        return get_string('plugin_enrolment_synchronization_failed_event', 'enrol_usi');
    }

    public function get_description() {
        $description  = "The enrolments synchronization on the course with id=";
        $description .= $this->courseid;
        $description .= " failed because of an error: ";
        $description .= '[' . $this->other['error_message'] . "]";

        return $description;
    }
}

