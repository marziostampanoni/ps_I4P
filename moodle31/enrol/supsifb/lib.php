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
 * SUPSI Students Sync plugin.
 *
 * @package    enrol_supsifb
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * In this class are defined some helper methods (get_instance_name, 
 * get_newinstance_link) along with the core methods used by the plugin
 * to enrol and update the students.
 *
 * @author    Guglielmo Fachini
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_supsifb_plugin extends enrol_plugin {

    /**
     * Returns localised name of enrol instance
     *
     * @param stdClass $instance (null is accepted too)
     * @return string
     */
    public function get_instance_name($instance) {
        global $DB;

        if (empty($instance)) {
            $enrol = $this->get_name();
            return get_string('pluginname', 'enrol_' . $enrol);
        } else if (empty($instance->name)) {
            $enrol = $this->get_name();
            $course_name = $instance->customchar2;
            return get_string('pluginname', 'enrol_' . $enrol) . ' (' . $course_name . ')';
        } else {
            return format_string($instance->name);
        }
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin in course.
     * @param int $courseid
     * @return moodle_url page url
     */
    public function get_newinstance_link($courseid) {
        // check the capabilities
        $context = context_course::instance($courseid, MUST_EXIST);
        if (!has_capability('moodle/course:enrolconfig', $context)
            or !has_capability('enrol/supsifb:config', $context)) {
    
            return NULL;
        }

        return new moodle_url('/enrol/supsifb/users_sync.php', array('id'=>$courseid));
    }

    /**
     * Returns edit icons for the page with the list of instances.
     * @param stdClass $instance
     * @return array
     */
    public function get_action_icons(stdClass $instance) {
        global $OUTPUT;

        // check that we are working on the right instance
        if ($instance->enrol !== 'supsifb') {
            throw new coding_exception('invalid enrol instance!');
        }
 
        // retrieve the course's context
        $context = context_course::instance($instance->courseid);

        // check the capabilities
        if (!has_capability('enrol/supsifb:config', $context)) {
            return array();
        }

        // create the URL parameters array
        $parameters = array();
        $parameters['enrol_id']  = $instance->id;
        $parameters['course_id'] = $instance->courseid;
        
        // create the sync URL
        $sync_link = new moodle_url("/enrol/supsifb/course_sync.php", $parameters);

        // create the sync icon
        $sync_icon = new pix_icon('a/refresh', get_string('refresh'), 'core', array('class' => 'iconsmall'));

        // create and return the icons array with the icon as element
        return array($OUTPUT->action_icon($sync_link, $sync_icon));
    }

    /*
     * Students enrolment.
     */

    /**
     * Enrol the students given their usernames.
     * This function must be called only the first time on a given course, 
     * because the first time that the plugin is used we need to create an 
     * enrolment instance. For all the other times, the function enrol_students 
     * should be the best way to enrol new users with the plugin (it 
     * needs the enrolment instance ID so that the updates are only executed upon the 
     * existing enrolment instance).
     *
     * @param array the identifiers (usernames) of the students to enrol
     * @param int the ID of the course in which the students must be enrolled 
     * @param string the SUPSI module identifier
     * @param string the SUPSI module name
     *
     * @return int the ID of the enrolment instance created
     */
    public function enrol_students_first_time($students_ids, $course_id, $target_module_id, $target_module_name) {
        global $DB;
        
        // start the transaction
        $transaction = $DB->start_delegated_transaction();

        try {
            // retrieve the course instance
            $course = get_course($course_id);

            // retrieve the context of the course
            $context = context_course::instance($course_id, MUST_EXIST);

            // trigger an event to signal this enrolment action
            \enrol_supsifb\event\enrol_students_first_time_started::create(array(
                'context' => $context,
                'other' => array('supsifb_module_name' => $target_module_name,
                                 'supsifb_module_id' => $target_module_id)
            ))->trigger();

            /*
             * Create a new instance for the enrolment plugin.
             * Use the 'customchar1' and 'customchar2' fields to respectively store 
             * the chosen module identifier and name
             */
            $custom_fields = array();
            $custom_fields['customchar1'] = $target_module_id;
            $custom_fields['customchar2'] = $target_module_name;

            // add an instance to the enrolment table
            $enrol_id = $this->add_instance($course, $custom_fields);

            // enrol the students
            $this->enrol_students($students_ids, $enrol_id);

            // commit the transaction
            $transaction->allow_commit();

            // trigger an event to signal this enrolment action
            \enrol_supsifb\event\enrol_students_first_time_completed::create(array(
                'objectid' => $enrol_id,
                'context' => $context,
                'other' => array('supsifb_module_name' => $target_module_name,
                                 'supsifb_module_id' => $target_module_id)
            ))->trigger();

            // return the enrolment instance created
            return $enrol_id;
        } catch (Exception $e) {
            // rollback the changes
            $transaction->rollback($e);
        }

        // trigger an event to signal the failure
        \enrol_supsifb\event\enrol_students_first_time_failed::create(array(
            'objectid' => $enrol_id,
            'context' => $context,
            'other' => array('supsifb_module_name' => $target_module_name,
                             'supsifb_module_id' => $target_module_id)
        ))->trigger();

        return -1;
    }

    public function enrol_students($students_ids, $enrol_id) {
        global $DB;

        // start the transaction
        $transaction = $DB->start_delegated_transaction();

        try {
            // retrieve the enrolment instance of this plugin
            $enrol_instance = $DB->get_record('enrol', array('id' => $enrol_id), '*', MUST_EXIST);
            $course_id = $enrol_instance->courseid;

            // retrieve the context of the course
            $context = context_course::instance($course_id, MUST_EXIST);

            /*
             * Loop through the IDs: get the user ids and enrol them in the course.
             */
            foreach ($students_ids as $current_id) {
                // check that the current user exists
                if ($DB->record_exists('user', array('username' => $current_id))) {
                    // retrieve the user's instance from the 'username' field
                    $moodle_user = $DB->get_record('user', array('username' => $current_id));

                    // check that the user is not already enrolled in the course
                    if (!is_enrolled($context, $moodle_user)) {
                        // retrieve the default role for the user
                        $default_role = get_config('enrol_supsifb', 'roleid');

                        // enrol the user
                        $this->enrol_user($enrol_instance,    // the enrol instance
                                          $moodle_user->id,   // the id of the user to enrol
                                          $default_role,      // the role of the user in the course
                                          0,                  // the start date (0 = unknown)
                                          0,                  // the end date (0 = forever)
                                          ENROL_USER_ACTIVE); // the user status 
                    } else {
                        // DO NOTHING
                        // the user is already enrolled in the course, thus I don't want 
                        // to enrol him twice
                    }
                } else {
                    // DO NOTHING
                    // the user doesn't exist, thus I can't enrol him in the course
                }
            }

            // assuming that the enrolments have been successful, I can commit 
            // the transaction
            $transaction->allow_commit();
        } catch (Exception $e) {
            $transaction->rollback($e);
            return false;
        }

        return true;
    }

    public function unenrol_students($students_ids, $enrol_id) {
        global $DB;

        // start the transaction
        $transaction = $DB->start_delegated_transaction();

        try {
            // retrieve the enrolment instance of this plugin
            $enrol_instance = $DB->get_record('enrol', array('id' => $enrol_id), '*', MUST_EXIST);
            $course_id = $enrol_instance->courseid;

            // retrieve the context of the course
            $context = context_course::instance($course_id, MUST_EXIST);

            /*
             * Loop through the IDs: get the user ids and unenrol them from the course.
             */
            foreach ($students_ids as $current_id) {
                // check that the current user exists
                if ($DB->record_exists('user', array('username' => $current_id))) {
                    // retrieve the user's instance from the 'username' field
                    $moodle_user = $DB->get_record('user', array('username' => $current_id));

                    // check that the user is not already enrolled in the course
                    if (is_enrolled($context, $moodle_user)) {
                        // unenrol the user from the course
                        $this->unenrol_user($enrol_instance, $moodle_user->id);
                    } else {
                        // DO NOTHING
                        // the user is not enrolled in the given course
                    }
                } else {
                    // DO NOTHING
                    // the user doesn't exist in the Moodle DB
                }
            }

            // assuming that the unenrolments have been successful, I can commit 
            // the transaction
            $transaction->allow_commit();
        } catch (Exception $e) {
            $transaction->rollback($e);
            return false;
        }

        return true;
    }

    /*
     * Course synchronization.
     */

    /**
     * Given the enrol instance ID, it updates the students (enrol/unenrol actions) by 
     * looking at what the SUPSI Web Service returns.
     *
     * @param int the ID of the enrolment instance to synchronize
     */
    public function synchronize_plugin_enrolment($enrol_id) {
        global $DB;

        // retrieve the enrol instance from the DB
        $enrol_instance = $DB->get_record('enrol', array('id' => $enrol_id), '*', MUST_EXIST);

        // retrieve the course ID
        $course_id = $enrol_instance->courseid;

        // trigger an event to signal that the synchronization is started
        \enrol_supsifb\event\plugin_enrolment_synchronization_started::create(array(
            'context' => context_course::instance($course_id),
            'courseid' => $course_id
        ))->trigger();

        // extract the SUPSI module identifier from the enrolment instance custom fields
        $supsifb_module_id = $enrol_instance->customchar1;
        $supsifb_module_name = $enrol_instance->customchar2;

        /*
         * SUPSI students retrieval
         */

        // try to get an instance of the SUPSI Web Service
        $supsifb_ws = null;
        try {
            $supsifb_ws = \enrol_supsifb\ws\factory::create();
        } catch (\enrol_supsifb\ws\ws_creation_failed_exception $fault) {
            // trigger an event to signal the Web Service failure
            \enrol_supsifb\event\ws_creation_failed::create(array(
                'context' => context_course::instance($course_id),
                'other' => array('error_message' => $fault->getMessage())
            ))->trigger();

            // trigger an event to signal that the synchronization has been 
            // interrupted
            \enrol_supsifb\event\plugin_enrolment_synchronization_failed::create(array(
                'context' => context_course::instance($course_id),
                'courseid' => $course_id,
                'other' => array('error_message' => 'Cannot create the SUPSI Web Service instance.')
            ))->trigger();

            // the Web Service is not available, thus I can't synchronize the 
            // desired enrolments
            return false;
        }
 
        // try to retrieve the students IDs from the Web Service
        // if there is an error, stop the synchronization
        $supsifb_students = null;
        try {
            $supsifb_students = $supsifb_ws->get_students_enrolled($supsifb_module_id);
        } catch (\enrol_supsifb\ws\remote_call_failed_exception $fault) {
            // trigger an event to signal the Web Service failure
            \enrol_supsifb\event\ws_remote_call_failed::create(array(
                'context' => context_course::instance($course_id),
                'other' => array('error_message' => $fault->getMessage())
            ))->trigger();

            // trigger an event to signal that the synchronization has been 
            // interrupted
            $error_message  = 'Cannot retrieve the students unique IDs from the SUPSI Web Service ';
            $error_message .= '[' . $fault->getMessage() . ']';
            \enrol_supsifb\event\plugin_enrolment_synchronization_failed::create(array(
                'context' => context_course::instance($course_id),
                'courseid' => $course_id,
                'other' => array('error_message' => $error_message)
            ))->trigger();

            return false;
        }

        // check the length of the students array: if it's zero there is 
        // something weird happening thus I don't want to proceed with the sync
        if (count($supsifb_students) == 0) {
            // trigger an event to signal that weird situation
            \enrol_supsifb\event\plugin_enrolment_synchronization_failed::create(array(
                'context' => context_course::instance($course_id),
                'courseid' => $course_id,
                'other' => array('error_message' => 'The number of SUPSI students is zero, this is really strange.')
            ))->trigger();

            return false; 
        }

        // normalize the supsifb students array (it will only contain the IDs as strings)
        $supsifb_students = array_map(function($student) { return $student->get_id(); }, $supsifb_students);

        /*
         * Moodle students retrieval
         */

        // retrieve the enrolled user by looking at the course and enrol instances IDs
        $moodle_students = $this->get_enrolled_users_with_plugin($course_id, $enrol_id);

        /*
         * Students comparison (SUPSI vs Moodle)
         */

        // find the students to enrol
        $students_to_enrol = $this->compute_students_to_enrol($supsifb_students, $moodle_students);

        // find the students to unenrol
        $students_to_unenrol = $this->compute_students_to_unenrol($supsifb_students, $moodle_students);

        /*
         * Enrol and Unenrol operations
         */

        // start the transaction
        $transaction = $DB->start_delegated_transaction();
        $errors_happened = false;

        try {
            // execute the enrol action
            $errors_happened = !$this->enrol_students($students_to_enrol, $enrol_id); 

            // execute the unenrol action only if the enrolment has been 
            // successful
            if (!$errors_happened) {
                $errors_happened = !$this->unenrol_students($students_to_unenrol, $enrol_id);
            }

            // assuming that the previous actions were correctly performed
            // I can commit the transaction
            if (!$errors_happened) {
                $transaction->allow_commit();
            }
        } catch (Exception $e) {
            // rollback all the changes
            $transaction->rollback($e);
            $errors_happened = true;
        }

        if ($errors_happened) {
            // trigger an event to signal that the synchronization has been 
            // interrupted
            \enrol_supsifb\event\plugin_enrolment_synchronization_failed::create(array(
                'context' => context_course::instance($course_id),
                'courseid' => $course_id,
                'other' => array('error_message' => 'An error occured during the enrolment/unenrolments actions')
            ))->trigger();

            return false;
        } else {
            // trigger an event to signal that the synchronization completed 
            // successfully
            \enrol_supsifb\event\plugin_enrolment_synchronization_completed::create(array(
                'context' => context_course::instance($course_id),
                'courseid' => $course_id
            ))->trigger();

            return true;
        }
    }

    /*
     * Synchronize all the courses that used this plugin for enrolling SUPSI students.
     */
    public function synchronize_all_plugin_enrolments() {
        global $DB;

        // trigger an event to signal that the synchronization is started
        \enrol_supsifb\event\plugin_enrolment_mass_synchronization_started::create(array(
            'context' => context_system::instance()
        ))->trigger();

        // restrict the query only on the plugin enrolment instances
        $conditions = array('enrol' => 'supsifb');

        // retrieve only the course IDs
        $fields = 'id';

        // perform the query
        $plugin_enrolments = $DB->get_records('enrol', $conditions, null, $fields);

        // start the transaction
        $transaction = $DB->start_delegated_transaction();

        try {
            // synchronize each course
            foreach ($plugin_enrolments as $plugin_enrol) {
                $this->synchronize_plugin_enrolment($plugin_enrol->id);
            }

            // assuming that the previous synchronizatons were correctly performed
            $transaction->allow_commit();
        } catch (Exception $e) {
            // rollback the changes
            $transaction->rollback($e);

            // trigger an event to signal that the synchronization has been 
            // interrupted
            \enrol_supsifb\event\plugin_enrolment_mass_synchronization_failed::create(array(
                'context' => context_system::instance(),
                'other' => array('error_message' => 'An error occured during the mass enrolment/unenrolments actions')
            ))->trigger();

            return false;
        }

        // trigger an event to signal that the synchronization completed 
        // successfully
        \enrol_supsifb\event\plugin_enrolment_mass_synchronization_completed::create(array(
            'context' => context_system::instance()
        ))->trigger();

        return true;
    }

    /*
     * Helper methods.
     */

    /**
     * Retrieves and returns the enrolled users given the course and enrol 
     * instances' identifiers.
     *
     * @param int the ID of the course from which the students will be extracted
     * @param int the ID of the enrol instance created by the plugin for the 
     *            enrolment
     *
     * @return array the usernames of the students that have been enrolled to 
     *               the course (course_id) with the given enrol (enrol_id) instance.
     */
    public function get_enrolled_users_with_plugin($course_id, $enrol_id) {
        global $DB;

        // retrieve the enrolled user by looking at the course and enrol instances IDs
        $enrolled_users = $DB->get_records_sql("
            SELECT u.username
              FROM {course} c 
              JOIN {context} ct on c.id = ct.instanceid
              JOIN {role_assignments} ra on ra.contextid = ct.id
              JOIN {user} u on u.id = ra.userid
              JOIN {role} r on r.id = ra.roleid
              JOIN {user_enrolments} ue on u.id = ue.userid
             WHERE c.id=$course_id and ue.enrolid=$enrol_id");

        // normalize the moodle students array (it will only contain the IDs as strings)
        $moodle_students = array();
        foreach ($enrolled_users as $user) {
            $moodle_students[] = $user->username;
        }

        return $moodle_students;
    }

    /**
     * Compute the difference between the given arrays in order
     * to return the users that need to be enrolled.
     * The arrays must contain only usernames (strings) because the difference
     * between sets is computed to obtain the users list. 
     *
     * @param array the uniqueIDs of the SUPSI students
     * @param array the usernames of the Moodle students 
     *
     * @return array the users that need to be enrolled
     */
    public function compute_students_to_enrol($supsifb_students, $moodle_students) {
        return array_diff($supsifb_students, $moodle_students);
    }

    /**
     * Compute the difference between the given arrays in order
     * to return the users that need to be unenrolled.
     * The arrays must contain only usernames (strings) because the difference
     * between sets is computed to obtain the users list. 
     *
     * @param array the uniqueIDs of the SUPSI students
     * @param array the usernames of the Moodle students 
     *
     * @return array the users that need to be unenrolled
     */
    public function compute_students_to_unenrol($supsifb_students, $moodle_students) {
        return array_diff($moodle_students, $supsifb_students);
    }
}

