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

global $CFG;

class FakeWS implements \enrol_usi\ws\iws {

    private $fakeStudentTypes;
    private $fakeFaculties;
    private $fakeSemesters;
    private $fakeModules;
    private $fakeUniqueID;

    private function __construct() {
        // create the fake student types to return
        $this->fakeStudentTypes = array();

        // create the fake faculties to return
        $this->fakeFaculties = array();

        // create the fake semesters to return
        $this->fakeSemesters = array();

        // create the fake modules to return
        $this->fakeModules = array();

        // create the fake unique IDs to return
        $this->fakeUniqueID = array();
    }

    public static function create() {
        return new FakeWSWrapper(new FakeWS());
    }

    public function get_student_types() {
        return $this->fakeStudentTypes;
    }

    public function get_faculties() {
        return $this->fakeFaculties;
    }

    public function get_semesters() {
        return $this->fakeSemesters;
    }

    public function get_modules($semester_id, $faculty_id, $student_type_id) {
        return $this->fakeSemesters; 
    }

    public function get_unique_ids($module_id) {
        return $this->fakeUniqueID[$module_id];
    }

    /*
     * Injection related methods.
     */

    public function inject_unique_ids($module_id, $unique_id) {
        $this->fakeUniqueID[$module_id] = $unique_id;
    }
}

class FakeWSWrapper {
    // the real Fake WS
    private $realWS;

    // methods that must fail when called
    private $failMethods;
   
    public function __construct($realWS) {
        $this->realWS = $realWS;

        // by default all the methods must succssed
        $this->failMethods = array();
    }

    public function fail_on($method_name, $closure) {
        $this->failMethods[$method_name] = $closure;
    }

    public function __call($method, $args) {
        // if the method called must fail, invoke the relative closure
        if (array_key_exists($method, $this->failMethods)) {
            return $this->failMethods[$method]->__invoke($args);
        }

        // otherwise return the execution of the existing method
        return call_user_func_array(array($this->realWS, $method), $args);
    }
}

