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
 * This file contains the class that provide a cached access to the USI web service.
 *
 * @package    enrol_usi
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_usi\ws;

/**
 * This is the concrete class through which the USI web service can be interrogated.
 * Before starting the use of that class you must know where the service WSDL file is
 * located and what is the API key to access the various operations.
 * 
 * IMPORTANT: here the web service data are cached in order to improve the 
 *            performance of the whole plugin.
 */
class cached_ws implements iws {
    // the concrete WS instance
    private $concretews;

    // the cache for the students IDs
    private $cache;

    private function __construct($concretews, $cache) {
        $this->concretews = $concretews;
        $this->cache = $cache;
    }

    public static function create($wsdl_location, $API_key, $cache) {
        // create the concrete WS
        $concretews = concrete_ws::create($wsdl_location, $API_key);

        // return the cached version of the concrete WS
        return new cached_ws($concretews, $cache);
    }

    public function get_student_types() {
        return $this->concretews->get_student_types();
    }

    public function get_faculties() {
        return $this->concretews->get_faculties();
    }

    public function get_semesters() {
        return $this->concretews->get_semesters();
    }

    public function get_modules($semester_id, $faculty_id, $student_type_id) {
        return $this->concretews->get_modules($semester_id, $faculty_id, $student_type_id);
    }

    public function get_unique_ids($module_id) {
        global $DB;

        // declare the container for the result
        $result = array();

        // check if the requested data is cached and usable
        if ($this->cache->is_up_to_date($module_id)) { 
            $result = $this->cache->get($module_id);
        } else {
            // retrieve the original value
            $result = $this->concretews->get_unique_ids($module_id);

            // update the cache with new value
            $this->cache->put($module_id, $result);
        }

        // return the requested data
        return $result;
    }
}

