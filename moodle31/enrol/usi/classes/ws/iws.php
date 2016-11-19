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

namespace enrol_usi\ws;

/**
 * Definition of the methods that a concrete Web Service class must expose.
 * @package    enrol_usi
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface iws {
    /**
     * In case of error throw a RemoteCallFailedException exception.
     *
     * @throws remote_call_failed_exception when the web service fails to 
     *         return a response
     *
     * @return array the available student types
     */
    function get_student_types();

    /**
     * In case of error throw a RemoteCallFailedException exception.
     *
     * @throws remote_call_failed_exception when the web service fails to 
     *         return a response
     *
     * @return array the available faculties
     */
    function get_faculties();

    /**
     * In case of error throw a RemoteCallFailedException exception.
     *
     * @throws remote_call_failed_exception when the web service fails to 
     *         return a response
     *
     * @return array the available semesters
     */
    function get_semesters();

    /**
     * In case of error throw a RemoteCallFailedException exception.
     *
     * @param int $semester_id the semester identifier
     * @param int $faculty_id the faculty identifier
     * @param int $studet_type_id the student type identifier
     *
     * @throws remote_call_failed_exception when the web service fails to 
     *         return a response
     *
     * @return array the requested modules
     */
    function get_modules($semester_id, $faculty_id, $student_type_id);

    /**
     * In case of error throw a RemoteCallFailedException exception.
     *
     * @param string $module_id the module identifier
     *
     * @throws remote_call_failed_exception when the web service fails to 
     *         return a response
     *
     * @return array the unique IDs of the students enrolled in the given module
     */
    function get_unique_ids($module_id);
}

