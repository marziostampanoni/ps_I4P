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
 * This interface defines how a formatter must behave in order to provide a mechanism 
 * to format the changes computed by the netid_sync component.
 *
 * @package    local_netidsync
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface sync_changes_formatter {

	/**
	 * Format and return the synchronization changes given as argument.
	 *
	 * @param sync_changes the changes to format
	 *
	 * @return string the formatted representation of the changes 
	 */
	function format($changes);
}

