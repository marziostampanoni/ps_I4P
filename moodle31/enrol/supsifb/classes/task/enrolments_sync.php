<?php
/*
 * This file is part of Moodle - http://moodle.org/
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * This class represents the task that must be executed in order to synchronize 
 * the enrolments performed by that plugin.
 *
 * @package    enrol_supsifb
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_supsifb\task;

class enrolments_sync extends \core\task\scheduled_task {      
    public function get_name() {
        return get_string('enrolments_sync_task', 'enrol_supsifb');
    }
 
    public function execute() {
        // start the synchronization of the enrolments
        $supsifb_plugin = enrol_get_plugin('supsifb');
        $supsifb_plugin->synchronize_all_plugin_enrolments();
    }
}

