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

class FakeWS implements \enrol_supsifb\ws\iws {

    private $fakeStudentTypes;
    private $fakeFaculties;
    private $fakeSemesters;
    private $fakeModules;
    private $fakeUniqueID;

    private function __construct() {
        // create the fake academic years to return
        $this->fakeAcademicYears = array();

        // create the fake academic units to return
        $this->fakeAcademicUnits = array();

        // create the fake curricula to return
        $this->fakeCurricula = array();

        // create the fake modules to return
        $this->fakeModules = array();

        // create the fake unique IDs to return
        $this->fakeStudents = array();
    }

    public static function create() {
        return new FakeWSWrapper(new FakeWS());
    }

    public function get_academic_years() {
        return $this->fakeAcademicYears;
    }

    public function get_academic_units() {
        return $this->fakeAcademicUnits;
    }

    public function get_curricula() {
        return $this->fakeCurricula;
    }

    public function get_modules($academic_unit_id, $curricula_id) {
        return $this->fakeModules;
    }

    public function get_students_enrolled($module_id) {
        return $this->fakeStudents[$module_id];
    }

    /*
     * Injection related methods.
     */

    public function inject_students($module_id, $unique_id) {
        $this->fakeStudents[$module_id] = $unique_id;
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

