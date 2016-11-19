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
 * This class represents the user entity built upon the results
 * given by a LDAP query to the TI-EDU servers.
 *
 * @package    local_netidsync
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user {
    private $cn;
    private $givenName;
    private $uid;
    private $mail;
    private $swissEduPersonUniqueID;
    private $sn;
    private $dn;

    public function __construct($cn, $givenName, $uid, $mail, $swiss_uid, $sn, $dn) {
        $this->cn = $cn;
        $this->givenName = $givenName;
        $this->uid = $uid;
        $this->mail = $mail;
        $this->swissEduPersonUniqueID = $swiss_uid;
        $this->sn = $sn;
        $this->dn = $dn;
    }

    public function get_swiss_edu_person_unique_id() {
        return $this->swissEduPersonUniqueID;
    }

    public function get_given_name() {
        return $this->givenName;
    }

    public function get_common_name() {
        return $this->cn;
    }

    public function get_surname() {
        return $this->sn;
    }

    public function get_mail() {
        return $this->mail;
    }

    public function get_user_id() {
        return $this->uid;
    }

    public function get_dn() {
        return $this->dn;
    }
}

