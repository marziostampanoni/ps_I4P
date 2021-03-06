<?php

namespace enrol_supsifb\event;

defined('MOODLE_INTERNAL') || die();

class ws_creation_failed extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'c'; 
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public static function get_name() {
        return get_string('ws_creation_failed_event', 'enrol_supsifb');
    }

    public function get_description() {
        $description  = "The SUPSI Web Service has not been created due to an error: ";
        $description .= '[' . $this->other['error_message'] . "]";

        return $description;
    }
}

