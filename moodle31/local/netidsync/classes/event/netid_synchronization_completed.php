<?php

namespace local_netidsync\event;

defined('MOODLE_INTERNAL') || die();

class netid_synchronization_completed extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'u'; 
        $this->data['edulevel'] = self::LEVEL_OTHER;
		$this->context = \context_system::instance();
    }

    public static function get_name() {
        return get_string('netid_synchronization_completed_event', 'local_netidsync');
    }

    public function get_description() {
		$description  = get_string('netid_synchronization_completed_message', 'local_netidsync');
		$description .= '; total created users = ' . $this->other['total_created_users'];
		$description .= '; total deleted users = ' . $this->other['total_deleted_users'];

        return $description;
    }
}

