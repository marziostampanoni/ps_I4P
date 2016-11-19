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
 * Definition of the cache used to store the unique ids of the students.
 *
 * @package    enrol_usi
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_usi\ws;

// moodle requirements
require_once("$CFG->dirroot/config.php");

/*
 * Define some useful costants
 */

// the name of the cache table 
define('CACHE_TABLE', 'usiws_cache');

// the name of the field that contains the cached data
define('CACHE_DATA_FIELD', 'json_ids');

// the name of the field that contains the USI module identifier
define('CACHE_MODULE_ID', 'module_id');

// the name of the field that contains the last update date
define('CACHE_LAST_UPDATE_FIELD', 'last_update');

/**
 * This class represents a cache that allows to store and retrieve the unique ids of the 
 * students returned from the web service.
 */
class module_cache {
    // the difference in seconds used as threshold when considering whether a cached 
    // record is old or not
    private $cacheTTL;

    public function __construct($cacheTTL) {
        $this->cacheTTL = $cacheTTL;
    }

    /**
     * Check if the students of a module are cached.
     *
     * @return bool true if the students are cached, false otherwise
     */
    public function exists($module_id) {
        global $DB;

        // restrict the query on the module ID
        $conditions = array(CACHE_MODULE_ID => $module_id);

        return $DB->record_exists(CACHE_TABLE, $conditions);
    }

    /**
     * Check if the students of a module are cached and not obsolete.
     *
     * @return bool true if the students are cached and up to date, false 
     *              otherwise
     */
    public function is_up_to_date($module_id) {
        global $DB;

        // return false if the given module is not cached
        if (!($this->exists($module_id))) {
            return false;
        }

        // restrict the query on the module ID
        $conditions = array(CACHE_MODULE_ID => $module_id);

        // extract the last_update field
        $last_update = $DB->get_field(CACHE_TABLE, CACHE_LAST_UPDATE_FIELD, $conditions);

        // compare the retrieved timestamp with the current timestamp
        // in order to determine whether the record is too old or not
        return !($this->is_too_old($last_update));
    }

    /**
     * Retrieve the cached students of the given module.
     *
     * @return array the array of unique_id objects relative to the given module
     */
    public function get($module_id) {
        global $DB;

        // restrict the query on the module ID
        $conditions = array(CACHE_MODULE_ID => $module_id);

        // perform the select query
        $raw_ids = $DB->get_field(CACHE_TABLE, CACHE_DATA_FIELD, $conditions); 

        // decode e return the cached data
        return $this->decode_cached_data($raw_ids);
    }

    /**
     * Insert or update the cached unique ids of the given module.
     */
    public function put($module_id, $raw_ids) {
        global $DB;

        // encode the real IDs so that they can be easily cached
        $encoded_ids = $this->encode_cached_data($raw_ids);

        // check which operation must be performed (insert or update)
        if ($this->exists($module_id)) {
            // modify the existing record
            $updated_record = new \stdClass();
            $updated_record->id = $this->get_id($module_id);
            $updated_record->json_ids = $encoded_ids;
            $updated_record->last_update = $this->get_current_timestamp();

            // update the existing record
            $DB->update_record(CACHE_TABLE, $updated_record);
        } else {
            // create the new record
            $new_record = new \stdClass();
            $new_record->module_id = $module_id;
            $new_record->json_ids = $encoded_ids;
            $new_record->last_update = $this->get_current_timestamp();

            // put the JSON IDs in the cache
            $DB->insert_record(CACHE_TABLE, $new_record);
        }
    }

    /**
     * Perform a complete cleanup of the cache table.
     */
    public static function cleanup() {
        global $DB;

        $DB->delete_records(CACHE_TABLE);
    }

    /***************************************************************************
     * Helper methods.
     **************************************************************************/

    /**
     * Decode and return the real cached value.
     *
     * @return array the array of unique_id parsed from $raw_data 
     */
    private function decode_cached_data($raw_data) {
        $unique_ids = array();
        $raw_ids = json_decode($raw_data);
        foreach ($raw_ids as $raw_id) {
            $unique_ids[] = new unique_id($raw_id->id);
        }

        return $unique_ids;       
    }

    /**
     * Encode and return the cache representation of $raw_data.
     *
     * @return string the cache representation of the given data
     */
    private function encode_cached_data($raw_data) {
        return json_encode($raw_data);
    }

    /**
     * @return int the current timestamp
     */
    private function get_current_timestamp() {
        return time();
    }

    /**
     * Check whether a given timestamp is too old or not.
     *
     * @return bool true if $ts is too old, false otherwise
     */ 
    private function is_too_old($ts) {
        // return true if the timestamp is at least 23 hours older than the
        // current timestamp
        return ($this->get_current_timestamp() - $ts) > $this->cacheTTL;
    }

    /**
     * Retrieve and return the database ID of the given module.
     * 
     * @return int the database ID of the given module; -1 in case of error
     */
    private function get_id($module_id) {
        global $DB;

        // return -1 if the module doesn't exist
        if (!$this->exists($module_id)) {
            return -1;
        }

        // restrict the query on the module ID
        $conditions = array(CACHE_MODULE_ID => $module_id);

        return $DB->get_field(CACHE_TABLE, 'id', $conditions);
    }
}

