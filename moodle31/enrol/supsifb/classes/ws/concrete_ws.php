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
 * This file contains the class that provide access to the SUPSI web service.
 *
 * @package    enrol_supsifb
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_supsifb\ws;

// include the autoloader for the composer dependencies
require_once($CFG->dirroot . '/enrol/supsifb/vendor/autoload.php');

/**
 * This is the concrete class through which the SUPSI web service can be interrogated.
 * Before starting the use of that class you must know where the service is 
 * located (its URI) and the username and password to use for the 
 * authentication.
 */
class concrete_ws implements iws {

	/**
	 * @var Client the Guzzle client used to make HTTP requests
	 */
	private $http_client;

	/**
	 * @var string the base URL of the web service
	 */
	private $base_url;

	/**
     * @var string $username the user to use for accessing the web service
	 */
	private $username;
	
	/*
     * @var string $password the password to use for accessing the web service
	 */
	private $password;

    /**
     * @param string $base_url the location of the web service
     * @param string $username the user to use for accessing the web service
     * @param string $password the password to use for accessing the web service
     */
    private function __construct($base_url, $username, $password) {
		$this->client = new \GuzzleHttp\Client();
        $this->base_url = $base_url;
		$this->username = $username;
		$this->password = $password;;
    }

    /**
     * Create and return an instance of the ConcreteWS.
     * In case of errors, throw a ws_creation_failed_exception.
     */
    public static function create($base_url, $username, $password) {
        // return the desired instance
        return new concrete_ws($base_url, $username, $password);
    }

	public function get_academic_years() {
		// perform the GET request
		$response = null;
		$request_url = $this->base_url . '/GetAcademicYears';
		try {
			$response = $this->client->get($request_url);
		} catch (\GuzzleHttp\Exception\TransferException $e) {
			throw new remote_call_failed_exception('HTTP request failed: ' . $request_url);
		}

		// decode the results
		$academic_years = array();
		$json_objects = json_decode($response->getBody());
		foreach ($json_objects as $obj) {
			$academic_years[] = new academic_year($obj->IdAcademicYear, $obj->Name, $obj->BeginDate, $obj->EndDate);
		}

		return $academic_years;
	}

	public function get_academic_units($academic_year_id) {
		// perform the GET request
		$response = null;
		$request_url = $this->base_url . '/GetAcademicUnits/' . $academic_year_id;
		try {
			$response = $this->client->get($request_url);
		} catch (\GuzzleHttp\Exception\TransferException $e) {
			throw new remote_call_failed_exception('HTTP request failed: ' . $request_url);
		}

		// decode the results
		$academic_units = array();
		$json_objects = json_decode($response->getBody());
		foreach ($json_objects as $obj) {
			$academic_units[] = new academic_unit($obj->IdAcademicUnit, $obj->Name, $obj->BeginDate, $obj->EndDate);
		}

		return $academic_units;
	}

	public function get_curricula($academic_year_id) {
		// perform the GET request
		$response = null;
		$request_url = $this->base_url . '/GetCurriculaForAcademicYear/' . $academic_year_id;
		try {
			$response = $this->client->get($request_url);
		} catch (\GuzzleHttp\Exception\TransferException $e) {
			throw new remote_call_failed_exception('HTTP request failed: ' . $request_url);
		}

		// decode the results
		$curricula = array();
		$json_objects = json_decode($response->getBody());
		foreach ($json_objects as $obj) {
			$curricula[] = new curricula($obj->IdCurricula, $obj->Name, $obj->Acronym);
		}

		return $curricula;
	}

	public function get_modules($academic_unit_id, $curricula_id) {
		// perform the GET request
		$response = null;
		$request_url = $this->base_url . '/GetModules/' . $academic_unit_id . '/' . $curricula_id;
		try {
			$response = $this->client->get($request_url);
		} catch (\GuzzleHttp\Exception\TransferException $e) {
			throw new remote_call_failed_exception('HTTP request failed: ' . $request_url);
		}

		// decode the results
		$modules = array();
		$json_objects = json_decode($response->getBody());
		foreach ($json_objects as $obj) {
			$modules[] = new module($obj->IdModule, $obj->Name, $obj->Code, $obj->Ects, $obj->Department);
		}

		return $modules;
	}

	public function get_students_enrolled($module_id) {
		// perform the GET request
		$response = null;
		$request_url = $this->base_url . '/GetStudentsEnrolled/' . $module_id;
		try {
			$response = $this->client->get($request_url);
		} catch (\GuzzleHttp\Exception\TransferException $e) {
			throw new remote_call_failed_exception('HTTP request failed: ' . $request_url);
		}

		// decode the results
		$students = array();
		$json_objects = json_decode($response->getBody());
		foreach ($json_objects as $obj) {
			$students[] = new student($obj->UniqueId, $obj->Name, $obj->Lastname);
		}

		return $students;
	}
}

