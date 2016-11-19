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
 * Definition of the class that represents the curricula resource.
 *
 * @package    enrol_supsifb
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_supsifb\ws;

/**
 * A curricula is the kind of path that the students choose (i.e. computer 
 * engineering).
 */
class curricula extends resource {

	/**
	 * @var string the acronym of the curricula
	 */
	private $acronym;

    public function __construct($id, $name, $acronym) {
        parent::__construct($id, $name);
        $this->acronym = $acronym;
    }

    public function get_acronym() {
        return $this->acronym;
    }
}

