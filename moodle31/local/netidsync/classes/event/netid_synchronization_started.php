<?php

namespace local_netidsync\event;

defined('MOODLE_INTERNAL') || die();

class netid_synchronization_started extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
		$this->context = \context_system::instance();
    }

    public static function get_name() {
        return get_string('netid_synchronization_started_event', 'local_netidsync');
    }

    public function get_description() {
		$description  = get_string('netid_synchronization_started_message', 'local_netidsync');

        return $description;
    }
}

