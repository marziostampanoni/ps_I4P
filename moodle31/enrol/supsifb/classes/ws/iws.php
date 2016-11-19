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

namespace enrol_supsifb\ws;

/**
 * Definition of the methods that a concrete Web Service class must expose.
 * @package    enrol_supsifb
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
     * @return array the available academic years
     */
	function get_academic_years();

    /**
     * In case of error throw a RemoteCallFailedException exception.
	 *
	 * @param int the ID of the academic year for which the academic units are 
	 *			  requested
	 *
     * @throws remote_call_failed_exception when the web service fails to 
     *         return a response
     *
	 * @return array the available academic units (a.k.a. semesters)
     */
    function get_academic_units($academic_year_id);

    /**
     * In case of error throw a RemoteCallFailedException exception.
	 *
 	 * @param int the ID of the academic year for which the curricula are 
	 *			  requested
	 *
     * @throws remote_call_failed_exception when the web service fails to 
     *         return a response
     *
     * @return array the available curricula
     */
    function get_curricula($academic_year_id);

    /**
     * In case of error throw a RemoteCallFailedException exception.
     *
     * @param int $academic_unit_id the academic unit (semester) identifier
     * @param int $curricula_id the curricula identifier
     *
     * @throws remote_call_failed_exception when the web service fails to 
     *         return a response
     *
     * @return array the requested modules
     */
    function get_modules($academic_unit_id, $curricula_id);

    /**
     * In case of error throw a RemoteCallFailedException exception.
     *
     * @param string $module_id the module identifier
     *
     * @throws remote_call_failed_exception when the web service fails to 
     *         return a response
     *
     * @return array the students enrolled in the given module
     */
    function get_students_enrolled($module_id);
}

