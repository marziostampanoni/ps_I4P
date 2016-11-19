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
 * Definition of the class that represents a web service resource.
 *
 * @package    enrol_usi
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_usi\ws;

/**
 * Represents a resource retrieved with the USI Web Service.
 * 
 * A Resource has an identifier and a name which can be respectively
 * accessed with the getter methods 'getId' and 'getName'.
 * All its attributes are read-only, thus a resource is also immutable.
 *
 * Every Resource is JsonSerializable so that you can obtain a JSON
 * representation by calling the json_encode function with the object as
 * parameter.
 *
 * A 'map' representation can be obtained too by using the 'asMap' method.
 * It returns the resource's attributes as an associative array.
 */
abstract class resource implements \JsonSerializable {
    private $id;
    private $name;

    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public function get_id() {
        return $this->id;
    }

    public function get_name() {
        return $this->name;
    }

    public function as_map() {
        return array($this->id => $this->name);
    }

    public function jsonSerialize() {
        return array('id' => $this->id, 'name' => $this->name);
    }
}

