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

class fake_moodle_data_provider {

	private $fake_users;

    private function __construct() {
		$this->fake_users = array();
    }

    public static function create() {
        return new decorated_fake_moodle_data_provider(new fake_moodle_data_provider());
	}

	public function get_users() {
		return $this->fake_users;
	}

    /*
     * Injection related methods.
     */

    public function inject_users($users) {
        $this->fake_users = $users;
    }
}

class decorated_fake_moodle_data_provider {
    // the real netid
    private $real_moodle_data_provider;

    // methods that must fail when called
    private $fail_methods;
   
    public function __construct($real_moodle_data_provider) {
        $this->real_moodle_data_provider = $real_moodle_data_provider;

        // by default all the methods must succssed
        $this->fail_methods = array();
    }

    public function fail_on($method_name, $closure) {
        $this->fail_methods[$method_name] = $closure;
    }

    public function __call($method, $args) {
        // if the method called must fail, invoke the relative closure
        if (array_key_exists($method, $this->fail_methods)) {
            return $this->fail_methods[$method]->__invoke($args);
        }

        // otherwise return the execution of the existing method
        return call_user_func_array(array($this->real_moodle_data_provider, $method), $args);
    }
}

