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
 * This class allows to perform the synchronization between LDAP and the Moodle 
 * database. In order to create an instance of that class, you must provide a 
 * netid handler (netid instance) along with a moodle data provider 
 * (moodle_data_provider instance).
 *
 * @package    local_netidsync
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class netid_sync {

    /**
     * @var netid the object used to retrieve the NetID users
     */ 
    private $netid_handler;

    /**
     * @var moodle_data_provider the object that is used to retrieve moodle 
     *                           users.
     */
    private $moodle_data_provider;
    
    public function __construct($netid_handler, $moodle_data_provider) {
        $this->netid_handler = $netid_handler;
        $this->moodle_data_provider = $moodle_data_provider;
    }

    public function compute_synchronization_changes() {
        // retrieve the NetID users
        $netid_users = $this->netid_handler->get_users();

        // extract the NetID usernames
        $netid_usernames = $this->extract_netid_usernames($netid_users);

        // retrieve the Moodle users
        $moodle_users = $this->moodle_data_provider->get_users();

        // extract the Moodle users' usernames
        $moodle_usernames = $this->extract_moodle_usernames($moodle_users);

        // compute the users that must be created
        $new_moodle_users = $this->compute_new_moodle_users($netid_usernames, $moodle_usernames);

        // keep only the new NetID users instances
        $raw_new_moodle_users = array_intersect_key($netid_users, array_flip($new_moodle_users));

        // compute the users that must be deleted
        $old_moodle_users = $this->compute_moodle_users_to_delete($netid_usernames, $moodle_usernames);

        // extract the new and old users
        $new_users = $this->filter_valid_users($raw_new_moodle_users);
        $old_users = array_intersect_key($moodle_users, array_flip($old_moodle_users));

        // return the changes
        return new sync_changes($new_users, $old_users);
    }

    /**
     * Retrieve the NetID users and synchronize them with the Moodle database.
     */
    public function perform_synchronization() {
		// trigger an event to signal the begin of the action
		event\netid_synchronization_started::create()->trigger();

		// try to compute the synchronization changes
		$changes = null;
		try {
			$changes = $this->compute_synchronization_changes();
		} catch (netid_connection_failed_exception $fault) {
			// trigger an event to signal the failure
			event\netid_synchronization_failed::create(array(
				'other' => array('error_message' => $fault->getMessage())
			))->trigger();

			return false;
		} catch (netid_binding_failed_exception $fault) {
			// trigger an event to signal the failure
			event\netid_synchronization_failed::create(array(
				'other' => array('error_message' => $fault->getMessage())
			))->trigger();

			return false;
		} catch (netid_search_failed_exception $fault) {
			// trigger an event to signal the failure
			event\netid_synchronization_failed::create(array(
				'other' => array('error_message' => $fault->getMessage())
			))->trigger();

			return false;
		}

        // create the new users
		$no_errors_happened = $this->create_moodle_users($changes->get_new_users());

        // remove the old moodle users only if there are not errors from the 
        // previous step
        if ($no_errors_happened) {
            $no_errors_happened = $this->delete_moodle_users($changes->get_old_users());
		}

		// signal the failure or the end of the synchronization
		if ($no_errors_happened) {
			// trigger an event to signal the end of the action
			event\netid_synchronization_completed::create(array(
				'other' => array('total_created_users' => count($changes->get_new_users()),
							     'total_deleted_users' => count($changes->get_old_users()))
			))->trigger();

			return true;
		}
		else {
			// trigger an event to signal the end of the action
			event\netid_synchronization_failed::create(array(
				'other' => array('error_message' => $fault->getMessage())
			))->trigger();

			return false;
		}
    }

    private function extract_netid_usernames($netid_users) {
        return array_keys($netid_users);
    }

    private function extract_moodle_usernames($moodle_users) {
        return array_keys($moodle_users);
    }

    private function compute_new_moodle_users($netid_users, $moodle_users) {
        return array_diff($netid_users, $moodle_users);
    }

    private function compute_moodle_users_to_delete($netid_users, $moodle_users) {
        return array_diff($moodle_users, $netid_users);
    }

    private function filter_valid_users($users) {
        $valid_users = array();

        foreach ($users as $username => $user) {
            if ($username != null && $username != '') {
                if ($user != null
                        && $user->get_given_name() != null
                        && $user->get_surname() != null
                        && $user->get_mail() != null) {
                    $valid_users[$username] = $user;
                }
            }
        }

        return $valid_users;
    }

    private function create_moodle_users($new_users) {
        global $CFG, $DB;
		require_once($CFG->dirroot . '/user/profile/lib.php');
		require_once($CFG->dirroot . '/user/lib.php');

        // start the transaction
        $transaction = $DB->start_delegated_transaction();

        try {
            foreach ($new_users as $new_user) {
				// create the user in the way that the shibboleth plugin does
                $username = strtolower($new_user->get_swiss_edu_person_unique_id());
				$password = generate_password(8);

				// compose a new object with the new user fields
                $moodle_user = new \stdClass();
                $moodle_user->email = $new_user->get_mail();
				$moodle_user->city = '';
                $moodle_user->auth = 'shibboleth';
				$moodle_user->firstname = $new_user->get_given_name();
                $moodle_user->lastname =  $new_user->get_surname();
                $moodle_user->username = $username;
				$moodle_user->password = $password;
				$moodle_user->lang = $CFG->lang;
				$moodle_user->confirmed = 1;
				$moodle_user->lastip = getremoteaddr();
				$moodle_user->timecreated = time();
				$moodle_user->timemodified = $moodle_user->timecreated;
				$moodle_user->mnethostid = $CFG->mnet_localhost_id;

				// create the user
				$moodle_user->id = \user_create_user($moodle_user, // the user to create 
								   					 false,		  // the password must not be taken from shibboleth 
													 true);		  // the user_created event must be triggered

				// save user profile data
				\profile_save_data($moodle_user);
            } 

            // commit the changes
            $transaction->allow_commit();
        } catch (Exception $e) {
            $transaction->rollback($e);
            return false;
        }
        
        return true;
    }

    private function delete_moodle_users($users) {
        global $DB;

        // start the transaction
        $transaction = $DB->start_delegated_transaction();

        // execute the delete actions
        try {
            // remove the time and memory limits
            \core_php_time_limit::raise();
            raise_memory_limit(MEMORY_EXTRA);

            // delete each user
            foreach ($users as $user) {
                delete_user($user); 
            }

            // commit the changes
            $transaction->allow_commit();
        } catch (Exception $e) {
            // rollback the changes
            $transaction->rollback($e);

            return false;
        }

        return true; 
    }
}

