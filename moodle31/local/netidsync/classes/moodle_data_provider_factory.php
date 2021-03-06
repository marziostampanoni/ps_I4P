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
 * This class provides a method that returns an instance of the 
 * moodle_data_provider class.
 *
 * @package    local_netidsync
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_data_provider_factory {

    /** 
     * @var closure the closure from which the moodle data provider instances are generated.
     */
    private static $generator;

    /**
     * Inject a generator of moodle data provider in the factory.
     * After the execution of that method, all the requested moodle data provider instances
     * will be generated by the injected generator.
     * 
     * @param a closure that accepts the parameters needed to create an instance 
     *        of the moodle data provider .
     */
    public static function inject($generator) {
        moodle_data_provider_factory::$generator = $generator; 
    }

    /**
     * Inject a moodle data provider object.
     * This is method is similar to 'inject' because it injects something to 
     * return when a moodle data provider instance is requested. The main difference is 
     * that 'inject' accepts a Closure that each time returns a new instance, 
     * while this method accepts an object that is always returned when the 
     * 'generate' method is called.
     * This is a sort of 'singleton' object injection.
     *
     * @param the object to inject
     */
    public static function inject_object($object) {
        moodle_data_provider_factory::inject(function () use ($object) {
            return $object;
        });
    }

    /**
     * @return moodle_data_provider a new instance of the moodle_data_provider class
     */
    public static function create() {
		return moodle_data_provider_factory::generate();
	}

    /**
	 * @return netid the instance of a moodle data provider accordingly to the fact that it 
	 * can be an injected closure that generates a fake moodle data provider. 
     */
    private static function generate() {
        // if nothing has been injected, return the default moodle data provider
        // implementation
        if (is_null(moodle_data_provider_factory::$generator)) {
			return new moodle_data_provider();
        }

        // otherwise, return the injected implementation
        return moodle_data_provider_factory::$generator->__invoke();
    }
}

