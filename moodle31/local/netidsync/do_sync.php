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
 * This file execute the users synchronization between the LDAP server and Moodle.
 *
 * @package    local_netidsync
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

global $USER;

// check that the user that is calling this page is an admin of Moodle
if (is_siteadmin($USER)) {
    // create the object needed to perform the synchronization
    $netid_sync_handler = \local_netidsync\netid_sync_factory::create();

	$start_time = microtime(true);

    // execute the synchronization
    $netid_sync_handler->perform_synchronization();

	echo("Elapsed time: " . (microtime(true) - $start_time) . " seconds");
}

