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
 * This class represents a container that must be used to hold the 
 * synchronization changes values (=> new users and old users).
 *
 * @package    local_netidsync
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sync_changes {

	/**
	 * @var array the new users
	 */
	private $new_users;

	/**
	 * @var array the old users
	 */
	private $old_users;

	public function __construct($new_users, $old_users) {
		$this->new_users = $new_users;
		$this->old_users = $old_users;
	}

	/**
	 * @return array the new users
	 */
	public function get_new_users() {
		return $this->new_users;
	}

	/**
	 * @return array the old users
	 */
	public function get_old_users() {
		return $this->old_users;
	}

	/**
	 * Build and return a report about the changes by using the provided
	 * formatter.
	 *
	 * @param sync_changes_formatter the formatter to use
	 *
	 * @return string the formatted output of the changes
	 */
	public function build_report($sync_changes_formatter) {
		return $sync_changes_formatter->format($this);
	}
}

