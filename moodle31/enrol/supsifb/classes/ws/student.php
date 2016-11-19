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
 * Definition of the class that represents the student resource.
 *
 * @package    enrol_supsifb
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_supsifb\ws;

/**
 * A student represents a resource which has:
 *	1. name
 *	2. lastname
 *	3. unique id
 */
class student extends resource {
	private $name;
	private $lastname;

    public function __construct($unique_id, $name, $lastname) {
        parent::__construct($unique_id, $name + ' ' + $lastname);    
		$this->name = $name;
		$this->lastname = $lastname;
    }

    public function get_name() {
		return $this->name;
	}

    public function get_lastname() {
		return $this->lastname;
	}
    
    public function jsonSerialize() {
        return array('id' => parent::get_id());
    }
}

