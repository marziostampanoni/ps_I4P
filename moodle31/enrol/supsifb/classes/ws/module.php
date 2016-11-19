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
 * Definition of the class that represents the module resource.
 *
 * @package    enrol_supsifb
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_supsifb\ws;

/**
 * A module is a resource that has:
 *  1. an ID
 *  2. a code (i.e. CS194)
 *  3. a name (i.e. Foundations of Random Sciences)
 *  4. a number of ECTS (i.e. 10)
 *  5. a department (i.e. DTI)
 */
class module extends resource {
	private $code;
    private $ects;
    private $department;

    public function __construct($id, $name, $code, $ects, $department) {
        parent::__construct($id, $name);
		$this->code = $code;
		$this->ects = $ects;
		$this->department = $department;
    }

    public function get_code() {
        return $this->code;
    }

    public function get_ects() {
        return $this->ects;
    }

    public function get_department() {
        return $this->department;
    }

    public function jsonSerialize() {
        return array_merge(parent::jsonSerialize(),
                            array('code' => $this->code,
                                  'ects' => $this->ects,
                                  'department' => $this->department));
    }
}

