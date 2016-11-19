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

namespace local_netidsync;

/**
 * This file defines a class for retrieving the LDAP users along with some 
 * useful functions that allows to build LDAP filters in an easy way.
 *
 * @package    local_netidsync
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @param string the domain components to format
 * @return string the given domain components in the LDAP format 
 */
function dc() {
    $toDC = function($dc) { return "dc=" . $dc; };
    return join(",", array_map($toDC, func_get_args()));         
}

/**
 * Enclose a string between two parenthesis.
 * For instance:
 *      enclose_with_paren('abc') => '(abc)'
 *
 * @param string a string
 * @return string the string eclosed in two parenthesis
 */
function enclose_with_paren($str) {
    return "(" . $str . ")";
}

/**
 * @param string the field to pattern match against
 * @param string the pattern that the filter must satisfy
 * @return string the pattern match in the LDAP format
 */
function match($field, $pattern) {
    return enclose_with_paren($field . "=" . $pattern);
}

/**
 * Negate an LDAP filter.
 * @param string a valid LDAP filter
 * @return string the given filter negated
 */
function not($rule) {
    return enclose_with_paren("!" . $rule);
}

/*+
 * Build an LDAP filter that must satisfy all the given filters.
 * @param string the filter to match
 * @return string the new filter that match all the given filters
 */
function all() {
    return enclose_with_paren("&" . join('', func_get_args()));
}

/*+
 * Build an LDAP filter that must satisfy at least one of the given filters.
 * @param string the filter to match
 * @return string the new filter that match at least one of the given filters
 */
function any() {
    return enclose_with_paren("|" . join('', func_get_args()));
}

/**
 * This class allows to retrieve users from the given LDAP servers.
 */
class netid {

    /**
     * @var string the LDAP server address
     */
    private $ldap_server;

    /**
     * @var int the LDAP server port
     */
    private $ldap_port;

    /**
     * @var string the DN to use for the binding
     */
    private $bind_dn;

    /**
     * @var string the password to use for the authentication
     */
    private $password;

    /**
     * @var int the LDAP protocol version (by default is 3)
     */
    private $protocol_version;

	/**
	 * @var array the domain components on which the search must be executed
	 */
	private $domain_components; 

    public function __construct($ldap_server, $ldap_port, $bind_dn, $password, $version, $dcs) {
        $this->ldap_server = $ldap_server;
        $this->ldap_port = $ldap_port;
        $this->bind_dn = $bind_dn;
        $this->password = $password; 
        $this->protocol_version = $version;
		$this->domain_components = $dcs;
    }

    /**
     * @return array an array that contains the users found on the LDAP server.
     */
    public function get_users() {
        // connect to the LDAP server
        $dcs = array();
		$ldap_connections = array();
		$main_connection = $this->connect();
		foreach ($this->domain_components as $domain) {
			$dcs[] = dc($domain, 'ch');
			$ldap_connections[] = $main_connection;
		}

        // initialize the session parameters
		$this->init($main_connection);

        // execute the binding
	    $this->bind($main_connection);

        // define the filter that hides the @netid aliases
        $no_alias_filter = not(match('uid', '*@netid*'));

        // execute the searches and aggregate the results
		$users = array();
		if (count($ldap_connections) == 0) {
			// DO NOTHING
		} else if (count($ldap_connections) == 1) {
			$users = $this->search($main_connection, $no_alias_filter, $dcs);
		} else {
			$users = $this->parallel_search($ldap_connections, $no_alias_filter, $dcs);
		}

        // terminate the session
		$this->close($main_connection);

        return $users;
    }

    private function connect() {
        $ldap_connection = ldap_connect($this->ldap_server, $this->ldap_port);

        // if the connection can't be estabilished, throw the relative exception
        if (!$ldap_connection) {
            throw new netid_connection_failed_exception(ldap_error($ldap_connection));
        } 
        
        return $ldap_connection;  
    }

    private function init($ldap_connection) {
        // set the protocol version
        ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, $this->protocol_version);

        // remove the search size limit
        ldap_set_option($ldap_connection, LDAP_OPT_SIZELIMIT, 0); 

        // remove the search time limit
        ldap_set_option($ldap_connection, LDAP_OPT_TIMELIMIT, 0); 
    }

    private function bind($ldap_connection) {
        if (!ldap_bind($ldap_connection, $this->bind_dn, $this->password)) {
            throw new netid_binding_failed_exception(ldap_error($ldap_connection));
        }
    }

   /**
     * Execute a parallel search on the LDAP $domain_components provided with $filter as 
     * query.
     *
     * @return array an array that contains the users found
     */
    private function parallel_search($ldap_connections, $filter, $domain_components) {
		// define which fields must be extracted from the LDAP searches
        $fields = array('cn', 'givenname', 'uid', 'mail', 'swissedupersonuniqueid', 'sn', 'dn');

		// perform the LDAP parallel searches
        $results = ldap_search($ldap_connections, $domain_components, $filter, $fields, 0, 0, 0);

		// if there is an error
		if ($results == false) {
			throw new netid_search_failed_exception("Parallel LDAP search failure");
		}

		// extract and aggregate the results
		$i = 0;
		$users = array();
		foreach ($results as $result) {
			// if there is an error
			if ($result == false) {
				$message  = 'DC = ' . $domain_components[$i] . ' ; ';
				$message .= 'FILTER = ' . $filter . ' ; ';
				$message .= 'ERROR = ' . ldap_error($ldap_connection[$i]);
				throw new netid_search_failed_exception($message);
			}

			// parse the results
			$current_users = $this->parse_search_result(ldap_get_entries($ldap_connections[$i], $result));

			// aggregate the users
			$users = array_merge($users, $current_users);

			// increment the connections counter
			$i++;
		}

		return $users;
    }

   /**
     * Execute a search on the LDAP $domain_components provided with $filter as 
     * query.
     *
     * @return array an array that contains the users found
     */
    private function search($ldap_connection, $filter, $domain_components) {
        $fields = array('cn', 'givenname', 'uid', 'mail', 'swissedupersonuniqueid', 'sn', 'dn');
        $results = ldap_search($ldap_connection, $domain_components, $filter, $fields, 0, 0, 0);

        // if there is an error
		if ($results == false) {
			$message  = 'DC = ' . $domain_components . ' ; ';
			$message .= 'FILTER = ' . $filter . ' ; ';
			$message .= 'ERROR = ' . ldap_error($ldap_connection);
			throw new netid_search_failed_exception($message);
        }

        // parse the results
        return $this->parse_search_result(ldap_get_entries($ldap_connection, $results));
    }

    /**
     * Try to extract the given field ($key) from $map. If nothing is found, 
     * return $default.
     *
     * @param string the field to extract
     * @param array the map from which the field must be extracted
     * @param string the default value to return if the field doesn't exist
     *
     * @return string the extracted field or $default if the field doesn't exist
     */
    private function extract_field($key, $map, $default = null) {
        if (array_key_exists($key, $map)) {
            return $map[$key][0];
        }

        return $default;
    }

    /**
     * Parse the LDAP result returned from a search, then return the array with 
     * the results.
     *
     * @param array the LDAP search result to parse
     * 
     * @return array the array of users parsed from the given LDAP result
     */
    private function parse_search_result($search_result) {
        $users = array();

        for ($i = 0; $i < $search_result['count']; $i++) {
            $current = $search_result[$i];
            $username = $this->extract_field('swissedupersonuniqueid', $current);

            // if the username is valid, I can parse the other fields
            if ($username != null) {
                $users[$username] = new user($this->extract_field('cn', $current),
                                             $this->extract_field('givenname', $current),
                                             $this->extract_field('uid', $current),
                                             $this->extract_field('mail', $current),
                                             $username, 
                                             $this->extract_field('sn', $current),
                                             $this->extract_field('dn', $current));
            }
        }

        return $users;
    }

    private function close($ldap_connection) {
        ldap_close($ldap_connection); 
    }
}

