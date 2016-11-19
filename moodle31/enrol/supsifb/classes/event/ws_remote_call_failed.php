<?php

namespace enrol_supsifb\event;

defined('MOODLE_INTERNAL') || die();

class ws_remote_call_failed extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'r'; 
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public static function get_name() {
        return get_string('ws_remote_call_failed_event', 'enrol_supsifb');
    }

    public function get_description() {
        $description  = "The SUPSI Web Service failed to return the response of a remote call: ";
        $description .= "[" . $this->other['error_message'] . "]";

        return $description;
    }
}

