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
 * @package    enrol_usi
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_usi\ws;

/**
 * A module is a resource that has:
 *  1. a faculty ID
 *  2. a student type ID
 *  3. a context ID
 *  4. a year
 */
class module extends resource {
    private $faculty_id;
    private $student_type_id;
    private $context_id;
    private $year;

    public function __construct($id, $faculty_id, $student_type_id, $context_id, $year, $name) {
        parent::__construct($id, $name);
        $this->faculty_id = $faculty_id;
        $this->student_type_id = $student_type_id;
        $this->context_id = $context_id;
        $this->year = $year;
    }

    public function get_faculty_id() {
        return $this->faculty_id;
    }

    public function get_student_type_id() {
        return $this->student_type_id;
    }

    public function get_context_id() {
        return $this->context_id;
    }

    public function get_year() {
        return $this->year;
    }

    public function jsonSerialize() {
        return array_merge(parent::jsonSerialize(),
                            array('faculty_id' => $this->faculty_id,
                                  'student_type_id' => $this->student_type_id,
                                  'context_id' => $this->context_id,
                                  'year' => $this->year));
    }
}

