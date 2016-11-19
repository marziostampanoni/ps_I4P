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
 * This file simulate the users synchronization between the LDAP server and Moodle.
 * That means that the Moodle database will not be touched, instead a useful CSV
 * report will be generated in response.
 *
 * @package    local_netidsync
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

global $USER;

// set the header in order to signal the CSV output
header("Content-type: text/csv");
//header("Content-Disposition: attachment; filename=synchronization_changes.csv");
header("Pragma: no-cache");
header("Expires: 0");

// check that the user that is calling this page is an admin of Moodle
if (is_siteadmin($USER)) {
    // create the object needed to perform the synchronization
    $netid_sync_handler = \local_netidsync\netid_sync_factory::create();

    // execute the synchronization
    $changes = $netid_sync_handler->compute_synchronization_changes();

	// output the CSV report
	echo($changes->build_report(new \local_netidsync\csv_formatter()));
}

