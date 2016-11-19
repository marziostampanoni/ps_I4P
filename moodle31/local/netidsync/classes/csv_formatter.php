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
 * This class implements a mechanism that allows to export the synchronization 
 * changes as a CSV string.
 *
 * @package    local_netidsync
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class csv_formatter implements sync_changes_formatter {

	public function format($changes) {
		$output = '';

		// write the CSV header
		$output .= "status, userid, username, firstname, lastname, email\n";

		// write the new users
		foreach ($changes->get_new_users() as $user) {
			$output .= 'new,';
			$output .= 'n/a, ';
			$output .= $this->sanitize_field($user->get_swiss_edu_person_unique_id()) . ', ';
			$output .= $this->sanitize_field($user->get_given_name()) . ', ';
			$output .= $this->sanitize_field($user->get_surname()) . ', ';
			$output .= $this->sanitize_field($user->get_mail());
			$output .= "\n";
		}

		// write the old users
		foreach ($changes->get_old_users() as $user) {
			$output .= 'old,';
			$output .= $this->sanitize_field($user->id) . ', ';
			$output .= $this->sanitize_field($user->username) . ', ';
			$output .= $this->sanitize_field($user->firstname) . ', ';
			$output .= $this->sanitize_field($user->lastname) . ', ';
			$output .= $this->sanitize_field($user->email);
			$output .= "\n";
		}

		return $output;
	}

	private function stringify($str) {
		return '"' . $str . '"';
	}

	private function sanitize_field($field) {
		return $this->stringify(str_replace('"', '\"', $field));
	}
}

