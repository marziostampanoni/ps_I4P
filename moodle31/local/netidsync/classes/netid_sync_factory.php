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

namespace local_netidsync;

/**
 * This class allows to retrieve an instance of the netid_sync class. 
 *
 * @package    local_netidsync
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class netid_sync_factory {
    /**
     * @return netid_sync a new instance of the netid_sync class
     */
    public static function create() {
        // retrieve an instance of the netid class
        $netid_handler = netid_factory::create();

        // retrieve an instance of the moodle data provider
        $moodle_data_provider = moodle_data_provider_factory::create();

        // return a new instance of the netid_sync class
        return new netid_sync($netid_handler, $moodle_data_provider);
	}
}

