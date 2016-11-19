<?php

namespace enrol_usi\event;

defined('MOODLE_INTERNAL') || die();

class ws_cache_cleanup_executed extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'u'; 
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public static function get_name() {
        return get_string('ws_cache_cleanup_executed_event', 'enrol_usi');
    }

    public function get_description() {
        $description  = "The USI Web Service cache has been cleaned.";

        return $description;
    }
}

