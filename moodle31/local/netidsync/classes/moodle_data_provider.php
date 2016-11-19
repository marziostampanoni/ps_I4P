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
 * This class provides a convenient method for retrieving the moodle users
 * that need to be synchronized.
 *
 * @package    local_netidsync
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_data_provider {
    /**
     * Retrieve the usernames of all the moodle users that are valid (deleted = 0) and have 
     * 'shibboleth' as authentiction method.
     *
     * @return array an associative array of Moodle users (the key is the 
     *               username, while the value is the user object)
     */
    public function get_users() {
        global $DB;

        // create the conditions to filter out all the needed moodle users
        $conditions = array();
        $conditions['auth'] = 'shibboleth';
        $conditions['deleted'] = 0;

        // specify which fields to extract
        $needed_fields = 'id,username,firstname,lastname,email';

        // the sorting is not needed
        $no_sort = '';

        // retrieve all the moodle users
        $rs = $DB->get_recordset('user', $conditions, $no_sort, $needed_fields);

        // extract the usernames from the recordset
        $moodle_users = array();
        foreach ($rs as $record) {
            $user = new \stdClass();
            $user->id = $record->id;
            $user->username = $record->username;
			$user->firstname = $record->firstname;
			$user->lastname = $record->lastname;
			$user->email = $record->email;
            $moodle_users[$user->username] = $user;
        }

        // close the record set
        $rs->close();

        return $moodle_users;
    }
}

