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
 * Definition of the class that represents the student type resource.
 *
 * @package    enrol_usi
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_usi\ws;

/**
 * A student_type is an object that represents a kind of students.
 * It has the number of years of study as an additional attribute.
 */
class student_type extends resource {
    private $years;

    public function __construct($id, $name, $years) {
        parent::__construct($id, $name);
        $this->years = $years;
    }

    public function get_years() {
        return $this->years;
    }

    public function jsonSerialize() {
        return array_merge(parent::jsonSerialize(), array('years' => $this->years));
    }
}

