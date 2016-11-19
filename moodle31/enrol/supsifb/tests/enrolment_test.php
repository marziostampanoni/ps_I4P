<?php

/**
 * Enrolment related tests.
 *
 * @package    enrol_supsifb
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
defined('MOODLE_INTERNAL') || die();
  
class enrol_supsifb_enrolment_testcase extends advanced_testcase {

    public function test_enrol_students_first_time() {
        global $CFG, $DB;

        // reset all fater the test operations
        $this->resetAfterTest(true);

        // retrieve an instance of the SUPSI plugin
        $supsifb_plugin = enrol_get_plugin('supsifb');

        // create some fake users     
        $users = array();
        foreach (range(0,3) as $i) {
            $users[] = $this->getDataGenerator()->create_user();
        }

        // create some fake courses
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id, MUST_EXIST);

        // create an array for the users' usernames
        $students_usernames = array();
        foreach ($users as $user) {
            $students_usernames[] = $user->username;
        }

        // prepare fake data for the SUPSI module
        $supsifb_module_id = 'SUPSI_COURSE';
        $supsifb_module_name = 'SUPSI Course In Random Science';

        // redirect the events
        $sink = $this->redirectEvents();

        // enrol the users in the courses
        $eid = $supsifb_plugin->enrol_students_first_time($students_usernames, $course->id, $supsifb_module_id, $supsifb_module_name);

        // retrieve the catched events
        $events = $sink->get_events();

        // stop the events redirection
        $sink->close();

        // check that the first event is the start event
        $start_event = $events[0];
        $this->assertInstanceOf('\enrol_supsifb\event\enrol_students_first_time_started', $start_event);

        // check that the last event is the end event
        $end_event = $events[count($events)-1];
        $this->assertInstanceOf('\enrol_supsifb\event\enrol_students_first_time_completed', $end_event);

        /* 
         * Check that the enrolment has been successful
         */ 

        // check that the returned enrolment instance ID is correct
        $this->assertTrue($DB->record_exists('enrol', array('id' => $eid)));

        // check that the users have been effectively enrolled in the course 
        $this->assertEquals(count($users), $DB->count_records('user_enrolments'));
        foreach ($users as $user) {
            $this->assertTrue(is_enrolled($context, $user));
        }
    }

    public function test_enrol_students() {
        global $CFG, $DB;

        // reset all fater the test operations
        $this->resetAfterTest(true);

        // retrieve an instance of the SUPSI plugin
        $supsifb_plugin = enrol_get_plugin('supsifb');

        // create some fake users     
        $users = array();
        foreach (range(0,3) as $i) {
            $users[] = $this->getDataGenerator()->create_user();
        }

        // create some fake courses
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id, MUST_EXIST);

        // create an array for the users' usernames
        $students_usernames = array();
        foreach ($users as $user) {
            $students_usernames[] = $user->username;
        }

        // prepare fake data for the SUPSI module
        $supsifb_module_id = 'SUPSI_COURSE';
        $supsifb_module_name = 'SUPSI Course In Random Science';

        // enrol the users in the courses
        $eid = $supsifb_plugin->enrol_students_first_time($students_usernames, $course->id, $supsifb_module_id, $supsifb_module_name);

        /*
         * Check that new users can be enrolled in the just created course.
         */

        // create some other fake users  
        $other_users = array();
        foreach (range(0,3) as $i) {
            $other_users[] = $this->getDataGenerator()->create_user();
        }
        
        // create an array for the other users' usernames
        $other_students_usernames = array();
        foreach ($other_users as $user) {
            $other_students_usernames[] = $user->username;
        }

        // enrol the new users
        $supsifb_plugin->enrol_students($other_students_usernames, $eid);

        /*
         * Check that the enrolments action has been successfully executed.
         */

        // check that the users have been effectively enrolled in the course 
        $this->assertEquals(count($users) + count($other_users), $DB->count_records('user_enrolments'));
        foreach ($other_users as $user) {
            $this->assertTrue(is_enrolled($context, $user));
        }

        /*
         * Check that the function doesn't enrol users with inexistent 
         * usernames.
         */
        $random_usernames = array('invalid_username_1', 'invalid_username_2', 'invalid_username_3');
        $supsifb_plugin->enrol_students($other_students_usernames, $eid);
        $this->assertEquals(count($users) + count($other_users), $DB->count_records('user_enrolments'));

        /*
         * Check that the users already enrolled are not considered by the 
         * enrolment function.
         */ 
        $supsifb_plugin->enrol_students($other_students_usernames, $eid);
        $this->assertEquals(count($users) + count($other_users), $DB->count_records('user_enrolments'));
    }
 
    public function test_unenrol_students() {
        global $CFG, $DB;

        // reset all fater the test operations
        $this->resetAfterTest(true);

        // retrieve an instance of the SUPSI plugin
        $supsifb_plugin = enrol_get_plugin('supsifb');

        // create some fake users     
        $users = array();
        foreach (range(0,3) as $i) {
            $users[] = $this->getDataGenerator()->create_user();
        }

        // create some fake courses
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id, MUST_EXIST);

        // create an array for the users' usernames
        $students_usernames = array();
        foreach ($users as $user) {
            $students_usernames[] = $user->username;
        }

        // prepare fake data for the SUPSI module
        $supsifb_module_id = 'SUPSI_COURSE';
        $supsifb_module_name = 'SUPSI Course In Random Science';

        // enrol the users in the courses
        $eid = $supsifb_plugin->enrol_students_first_time($students_usernames, $course->id, $supsifb_module_id, $supsifb_module_name);

        /*
         * Check that new users can be enrolled in the just created course.
         */

        // create some other fake users  
        $other_users = array();
        foreach (range(0,3) as $i) {
            $other_users[] = $this->getDataGenerator()->create_user();
        }
        
        // create an array for the other users' usernames
        $other_students_usernames = array();
        foreach ($other_users as $user) {
            $other_students_usernames[] = $user->username;
        }

        // enrol the new users
        $supsifb_plugin->enrol_students($other_students_usernames, $eid);

        // now unenrol the initially enrolled students
        $supsifb_plugin->unenrol_students($students_usernames, $eid);

        // chec the unenrolment action
        $this->assertEquals(count($other_users), $DB->count_records('user_enrolments'));
    }
 
    public function test_get_enrolled_users_with_plugin() {
        global $CFG, $DB;

        // reset all fater the test operations
        $this->resetAfterTest(true);

        // retrieve an instance of the SUPSI plugin
        $supsifb_plugin = enrol_get_plugin('supsifb');

        // create some fake users     
        $users = array();
        foreach (range(0,3) as $i) {
            $users[] = $this->getDataGenerator()->create_user();
        }

        // create some fake courses
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id, MUST_EXIST);

        // create an array for the users' usernames
        $students_usernames = array();
        foreach ($users as $user) {
            $students_usernames[] = $user->username;
        }

        // prepare fake data for the SUPSI module
        $supsifb_module_id = 'SUPSI_COURSE';
        $supsifb_module_name = 'SUPSI Course In Random Science';

        // enrol the users in the courses
        $eid = $supsifb_plugin->enrol_students_first_time($students_usernames, $course->id, $supsifb_module_id, $supsifb_module_name);

        // enrol some other users with a manual enrol
        $num_of_rand_users = 3;
        $student = $DB->get_record('role', array('shortname'=>'student'));
        foreach (range(0, $num_of_rand_users) as $i) {
            $r_user = $this->getDataGenerator()->create_user();
            $this->getDataGenerator()->enrol_user($r_user->id, $course->id, $student->id);
        }
        $this->assertEquals(count($users)+$num_of_rand_users+1, $DB->count_records('user_enrolments'));
        
        /*
         * Check that the function identifies the only the enrolled users with 
         * the plugin.
         */
        $usernames = $supsifb_plugin->get_enrolled_users_with_plugin($course->id, $eid); 
        $this->assertEquals(count($users), count($usernames));
    }

    public function test_compute_students_to_enrol_and_unenrol() {
        global $CFG, $DB;

        // reset all fater the test operations
        $this->resetAfterTest(true);

        // retrieve an instance of the SUPSI plugin
        $supsifb_plugin = enrol_get_plugin('supsifb');

        // create some fake users  
        $supsifb_users = array();
        foreach (range(0,9) as $i) {
            $supsifb_users[] = $this->getDataGenerator()->create_user();
        }

        // create some other fake users  
        $moodle_users = array();
        foreach (range(0,6) as $i) {
            $moodle_users[] = $this->getDataGenerator()->create_user();
        }
    
        // create an array for the users' usernames
        $supsifb_usernames = array();
        foreach ($supsifb_users as $user) {
            $supsifb_usernames[] = $user->username;
        }

        // create an array for the other users' usernames
        $moodle_usernames = array();
        foreach ($moodle_users as $user) {
            $moodle_usernames[] = $user->username;
        }

        // compute the users to enrol 
        $users_to_enrol = $supsifb_plugin->compute_students_to_enrol($supsifb_usernames, $moodle_usernames);
        $this->assertEquals(count($supsifb_users), count($users_to_enrol));

        // compute the users to unenrol 
        $users_to_unenrol = $supsifb_plugin->compute_students_to_unenrol($supsifb_usernames, $moodle_usernames);
        $this->assertEquals(count($moodle_users), count($users_to_unenrol));
    }
}

