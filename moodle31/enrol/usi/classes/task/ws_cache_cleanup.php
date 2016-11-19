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
 * This class represents the task that must be executed for cleaning
 * the USI Web Service cache.
 *
 * @package    enrol_usi
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_usi\task;

class ws_cache_cleanup extends \core\task\scheduled_task {      
    public function get_name() {
        return get_string('ws_cache_cleanup_task', 'enrol_usi');
    }
 
    public function execute() {
        // cleanup the USI Web Service cache 
        \enrol_usi\ws\module_cache::cleanup();

        // trigger an event to signal the failure
        \enrol_usi\event\ws_cache_cleanup_executed::create(array(
            'context' => \context_system::instance()
        ))->trigger();
     }
}

