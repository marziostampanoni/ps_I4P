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
 * Definition of the class that represents the semester resource.
 *
 * @package    enrol_usi
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_usi\ws;

/**
 * A semester represents a resource that has a period defined
 * by a start (getStartDate) and end date (getEndDate).
 */
class semester extends resource {
    private $start_date;
    private $end_date;

    public function __construct($id, $name, $start_date, $end_date) {
        parent::__construct($id, $name);
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function get_start_date() {
        return $this->start_date;
    }

    public function get_end_date() {
        return $this->end_date;
    }
}

