<?php

namespace enrol_supsifb\event;

defined('MOODLE_INTERNAL') || die();

class plugin_enrolment_mass_synchronization_completed extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'u'; 
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public static function get_name() {
        return get_string('plugin_enrolment_mass_synchronization_completed_event', 'enrol_supsifb');
    }

    public function get_description() {
        $description  = "The enrolments synchronization on all courses ";
        $description .= "has been executed successfully.";

        return $description;
    }
}

