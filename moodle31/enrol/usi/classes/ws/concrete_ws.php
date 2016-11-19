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
 * This file contains the class that provide access to the USI web service.
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
 */
class concrete_ws implements iws {
    /**
     * @var SoapClient the SOAP Client used to interrogate the web service
     */
    private $client;

    /**
     * @var string the API key used to acces the web service
     */
    private $API_key;

    /**
     * @param string $wsdl_location the location of the web service WSDL file
     * @param string $API_key the key to use for accessing the web service
     */
    private function __construct($soap_client, $API_key) {
        $this->client = $soap_client;
        $this->API_key = $API_key;
    }

    /**
     * Create and return an instance of the ConcreteWS.
     * In case of errors, throw a WSCreationFailedException.
     */
    public static function create($wsdl_location, $API_key) {
        // create the soap client
        $soap_client = null;
        try {
            $soap_client = new \SoapClient($wsdl_location, array('exceptions' => true));
        } catch (\SoapFault $fault) {
            // there has been an error, I can't return the desired instance
            // thus I throw an exception to say that the Web Service cannot be 
            // created
            throw new ws_creation_failed_exception($fault->faultstring . ', [FAUTLCODE: ' . $fault->faultcode . ']');
        }

        // return the desired instance
        return new concrete_ws($soap_client, $API_key);
    }

    public function get_student_types() {
        // convert a raw response in a StudentType object
        $toStudentType = function($stdObj) {
            return new student_type($stdObj->idTipoStudente, $stdObj->Nome, $stdObj->noAnni);
        };

        // build the parameters
        $parameters = array();
        $parameters['keyString'] = $this->API_key;

        // execute the remote function call
        $response = null;
        try {
            $response = $this->client->ListaTipiStudenti($parameters);
        } catch (\SoapFault $fault) {
            // the remote call produced an error, thus I throw an exception to 
            // the caller
            throw new remote_call_failed_exception($fault->faultstring . ', [FAULTCODE: ' . $fault->faultcode . ']');
        }

        // check that the response is valid
        if (property_exists($response->ListaTipiStudentiResult, 'TipoStudente')) {
            return array_map($toStudentType, $response->ListaTipiStudentiResult->TipoStudente);
        }

        // throw an exception since the response doesn't contain what expected
        throw new remote_call_failed_exception('Invalid response from the Web Service: missing a mandatory field');
    }

    public function get_faculties() {
        // convert a raw response in a Faculty object
        $toFaculty = function($stdObj) {
            return new faculty($stdObj->idFacolta, $stdObj->Nome);
        };

        // build the parameters
        $parameters = array();
        $parameters['keyString'] = $this->API_key;

        // execute the remote function call
        $response = null;
        try {
            $response = $this->client->ListaFacolta($parameters);
        } catch (\SoapFault $fault) {
            // the remote call produced an error, thus I throw an exception to 
            // the caller
            throw new remote_call_failed_exception($fault->faultstring . ', [FAULTCODE: ' . $fault->faultcode . ']');
        }

        // check that the response is valid
        if (property_exists($response->ListaFacoltaResult, 'Facolta')) {
            return array_map($toFaculty, $response->ListaFacoltaResult->Facolta);
        }

        // throw an exception since the response doesn't contain what expected
        throw new remote_call_failed_exception('Invalid response from the Web Service: missing a mandatory field');
    }

    public function get_semesters() {
        $toSemester = function($stdObj) {
            return new semester($stdObj->idSemestre, $stdObj->nome, $stdObj->dataInizio, $stdObj->dataFine);
        };

        // build the parameters
        $parameters = array();
        $parameters['keyString'] = $this->API_key;

        // execute the remote function call
        $response = null;
        try {
            $response = $this->client->ListaSemestri($parameters);
        } catch (\SoapFault $fault) {
            // the remote call produced an error, thus I throw an exception to 
            // the caller
            throw new remote_call_failed_exception($fault->faultstring . ', [FAULTCODE: ' . $fault->faultcode . ']');
        }

        // check that the response is valid
        if (property_exists($response->ListaSemestriResult, 'Semestre')) {
            return array_map($toSemester, $response->ListaSemestriResult->Semestre);
        }

        // throw an exception since the response doesn't contain what expected
        throw new remote_call_failed_exception('Invalid response from the Web Service: missing a mandatory field');
    }

    public function get_modules($semester_id, $faculty_id, $student_type_id) {
        $toModule = function($stdObj) {
            return new module($stdObj->idModulo, $stdObj->idFacolta, $stdObj->idTipoStudente, 
                              $stdObj->idContestuale, $stdObj->anno, $stdObj->nome);
        };

        // build the parameters
        $parameters = array();
        $parameters['keyString'] = $this->API_key;
        $parameters['idSemestre'] = $semester_id;
        $parameters['idFacolta'] = $faculty_id;
        $parameters['idTipoStudente'] = $student_type_id;

        // execute the remote function call
        $response = null;
        try {
            $response = $this->client->ListaModuli($parameters);
        } catch (\SoapFault $fault) {
            // the remote call produced an error, thus I throw an exception to 
            // the caller
            throw new remote_call_failed_exception($fault->faultstring . ', [FAULTCODE: ' . $fault->faultcode . ']');
        }

        // check that the response is valid
        if (property_exists($response->ListaModuliResult, 'Modulo')) {
            return array_map($toModule, $response->ListaModuliResult->Modulo);
        }

        // throw an exception since the response doesn't contain what expected
        throw new remote_call_failed_exception('Invalid response from the Web Service: missing a mandatory field');
    }

    public function get_unique_ids($module_id) {
        $toUniqueID = function($stdObj) {
            return new unique_id($stdObj);
        };

        // build the parameters
        $parameters = array();
        $parameters['keyString'] = $this->API_key;
        $parameters['idModulo'] = $module_id;

        // execute the remote function call
        $response = null;
        try {
            $response = $this->client->ListaUniqueID($parameters);
        } catch (\SoapFault $fault) {
            // the remote call produced an error, thus I throw an exception to 
            // the caller
            throw new remote_call_failed_exception($fault->faultstring . ', [FAULTCODE: ' . $fault->faultcode . ']');
        }
   
        // check that the response is valid
        if (property_exists($response->ListaUniqueIDResult, 'string')) {
            return array_map($toUniqueID, $response->ListaUniqueIDResult->string);
        }

        // throw an exception since the response doesn't contain what expected
        throw new remote_call_failed_exception('Invalid response from the Web Service: missing a mandatory field');
    }
}

