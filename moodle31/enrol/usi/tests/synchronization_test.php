<?php

/**
 * Synchronization related tests.
 *
 * @package    enrol_usi
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
defined('MOODLE_INTERNAL') || die();

require_once('FakeWS.php');
  
class enrol_usi_synchronization_testcase extends advanced_testcase {

    protected $users;
    protected $student_usernames;
    protected $other_users;
    protected $other_student_usernames;
    protected $course;
    protected $context;
    protected $enrol_id;
    protected $usi_plugin;
    protected $fake_module_id;
    protected $fake_module_name;
    protected $fake_ws;

    protected function setUp() {
        // reset all fater the test operations
        $this->resetAfterTest(true);
 
        // disable the web service caching
        set_config('cached_ws_flag', 0, 'enrol_usi');

        // retrieve an instance of the USI plugin
        $this->usi_plugin = enrol_get_plugin('usi');

        // create a fake course
        $this->course = $this->getDataGenerator()->create_course();
        $this->context = context_course::instance($this->course->id, MUST_EXIST);

        // create some fake users to enrol in the course 
        $this->users = array();
        foreach (range(0,3) as $i) {
            $this->users[] = $this->getDataGenerator()->create_user();
        }
        
        // create some other fake users that will be enrolled with the sync
        $this->other_users = array();
        foreach (range(0,5) as $i) {
            $this->other_users[] = $this->getDataGenerator()->create_user();
        }

        // create an array for the users' usernames
        $this->students_usernames = array();
        foreach ($this->users as $user) {
            $this->students_usernames[] = $user->username;
        }

        // create an array for the other users' usernames
        $this->other_students_usernames = array();
        foreach ($this->other_users as $user) {
            $this->other_students_usernames[] = $user->username;
        }

        /*
         * Enrolments initialization
         */

        // prepare fake data for the USI module
        $this->fake_module_id = 'FAKE_MODULE';
        $this->fake_module_name = 'Fake Module In Random Science';

        // enrol the first students
        $this->enrol_id = $this->usi_plugin->enrol_students_first_time($this->students_usernames,
            $this->course->id, $this->fake_module_id, $this->fake_module_name);

        /*
         * Fake Web Service initialization
         */

        // create a fake web service instance
        $this->fake_ws = FakeWS::create();

        // inject the fake web service in the WS factory so that we can tests all the possible
        // situations
        \enrol_usi\ws\factory::inject_object($this->fake_ws);
    }

    /**
     * Execute a sync operation that should not modify the enrolled 
     * students (the Moodle students are equals to the ones returned by the Web 
     * Service).
     */
    public function test_synchronize_plugin_enrolment_without_changes() {
        global $CFG, $DB;

        // redirect the events
        $sink = $this->redirectEvents();

        // prepare the fake WS returned data so that they reflect the current 
        // situation (thus no enrolments/unenrolments will be executed during the 
        // synchronization)
        $fake_unique_id = array();
        foreach ($this->students_usernames as $username) {
            $fake_unique_id[] = new \enrol_usi\ws\unique_id($username);
        }

        // inject the fake data in the web service
        $this->fake_ws->inject_unique_ids($this->fake_module_id, $fake_unique_id);

        // execute the sync and check that it succeds
        $this->assertTrue($this->usi_plugin->synchronize_plugin_enrolment($this->enrol_id));

        // retrieve the catched events
        $events = $sink->get_events();

        // stop the events redirection
        $sink->close();

        // check that the enrolled users are always the same
        $this->assertEquals(count($this->users), $DB->count_records('user_enrolments'));
        foreach ($this->users as $user) {
            $this->assertTrue(is_enrolled($this->context, $user));
        }

        // check that the triggered events are two (the start and the end of the 
        // operation)
        $this->assertEquals(2, count($events));

        // check that the first event is the start event
        $start_event = $events[0];
        $this->assertInstanceOf('\enrol_usi\event\plugin_enrolment_synchronization_started', $start_event);

        // check that the second event is the end event
        $end_event = $events[1];
        $this->assertInstanceOf('\enrol_usi\event\plugin_enrolment_synchronization_completed', $end_event);
    }

    /**
     * Execute a sync operation after respectively adding 5 students and 
     * removing only one from the fake remote course (the results returned by 
     * the Web Service).
     */
    public function test_synchronize_plugin_enrolment_with_changes() {
        global $CFG, $DB;

        // redirect the events
        $sink = $this->redirectEvents();

        // create some fake unique IDs
        $fake_unique_id = array();

        // add all the old students but the first
        foreach (array_slice($this->students_usernames, 1) as $username) {
            $fake_unique_id[] = new \enrol_usi\ws\unique_id($username);
        }

        // add all the other students
        foreach ($this->other_students_usernames as $username) {
            $fake_unique_id[] = new \enrol_usi\ws\unique_id($username);
        }

        // inject the new ID in the fake Web Service
        $this->fake_ws->inject_unique_ids($this->fake_module_id, $fake_unique_id);

        // execute the sync
        $this->assertTrue($this->usi_plugin->synchronize_plugin_enrolment($this->enrol_id));

        // retrieve the catched events
        $events = $sink->get_events();

        // stop the events redirection
        $sink->close();

        // check the number of enrolled students
        $this->assertEquals(count($this->users) - 1 + count($this->other_users), $DB->count_records('user_enrolments'));

        // check that the new students have been correctly enrolled
        foreach ($this->other_users as $user) {
            $this->assertTrue(is_enrolled($this->context, $user));
        }

        // check that the old students are remained except the first one
        $this->assertFalse(is_enrolled($this->context, $this->users[0]));
        foreach (array_slice($this->users,1) as $user) {
            $this->assertTrue(is_enrolled($this->context, $user));
        }

        // check that the first event is the start event
        $start_event = $events[0];
        $this->assertInstanceOf('\enrol_usi\event\plugin_enrolment_synchronization_started', $start_event);

        // check that the last event is the end event
        $end_event = $events[count($events)-1];
        $this->assertInstanceOf('\enrol_usi\event\plugin_enrolment_synchronization_completed', $end_event);

        // the other events are all related to Moodle and I don't need to check 
        // them too
    }

    /**
     * Execute a sync operation that fails because of a WSCreationFailedException.
     */
    public function test_synchronize_plugin_enrolment_ws_creation_failure() {
        // redirect the events
        $sink = $this->redirectEvents();

        // inject a generator that throw a WSCreationFailedException
        \enrol_usi\ws\factory::inject(function($wsdl, $key) {
            throw new \enrol_usi\ws\ws_creation_failed_exception('FAKE_EXCEPTION');
        });

        // execute the sync and check that it fails
        $this->assertFalse($this->usi_plugin->synchronize_plugin_enrolment($this->enrol_id));

        // retrieve the catched events
        $events = $sink->get_events();

        // stop the events redirection
        $sink->close();

        // check that the triggered events are three (the start, the ws creation 
        // failure the end of the operation)
        $this->assertEquals(3, count($events));

        // check that the first event is the start event
        $start_event = $events[0];
        $this->assertInstanceOf('\enrol_usi\event\plugin_enrolment_synchronization_started', $start_event);

        // check that the second event is the WS creation failure event
        $ws_creation_failure_event = $events[1];
        $this->assertInstanceOf('\enrol_usi\event\ws_creation_failed', $ws_creation_failure_event);

        // check that the third event is the failure event
        $failure_event = $events[2];
        $this->assertInstanceOf('\enrol_usi\event\plugin_enrolment_synchronization_failed', $failure_event);
    }

    /**
     * Execute a sync operation that fails because of a WSRemoteCallFailedException.
     */
    public function test_synchronize_plugin_enrolment_ws_remote_call_failure() {
        // redirect the events
        $sink = $this->redirectEvents();

        // change the fake WS so that when a call to getUniqueID is made, the 
        // given closure is called
        $this->fake_ws->fail_on('get_unique_ids', function ($module_id) {
            throw new \enrol_usi\ws\remote_call_failed_exception('FAKE_EXCEPTION');
        }); 

        // execute the sync and check that it fails
        $this->assertFalse($this->usi_plugin->synchronize_plugin_enrolment($this->enrol_id));

        // retrieve the catched events
        $events = $sink->get_events();

        // stop the events redirection
        $sink->close();

        // check that the triggered events are three (the start, the remote call 
        // failure and the sync failure)
        $this->assertEquals(3, count($events));

        // check that the first event is the start event
        $start_event = $events[0];
        $this->assertInstanceOf('\enrol_usi\event\plugin_enrolment_synchronization_started', $start_event);

        // check that the second event is the remote call failure event
        $remote_call_failure_event = $events[1];
        $this->assertInstanceOf('\enrol_usi\event\ws_remote_call_failed', $remote_call_failure_event);

        // check that the third event is the failure event
        $failure_event = $events[2];
        $this->assertInstanceOf('\enrol_usi\event\plugin_enrolment_synchronization_failed', $failure_event);
    }

    /**
     * Execute a sync operation with the web service that returns 0 
     * students.
     * The expected behaviour is that the synchronization fails because of 
     * the strange situation (all the students would be normally unenrolled from 
     * the Moodle course because the Web Service returned 0 students enrolled).
     */
    public function test_synchronize_plugin_enrolment_zero_students_returned_failure() {
        // redirect the events
        $sink = $this->redirectEvents();

        // inject an empty array in the fake Web Service
        $this->fake_ws->inject_unique_ids($this->fake_module_id, array());
        
        // execute the sync and check that it fails
        $this->assertFalse($this->usi_plugin->synchronize_plugin_enrolment($this->enrol_id));

        // retrieve the catched events
        $events = $sink->get_events();

        // stop the events redirection
        $sink->close();

        // check that the triggered events are two (the start and the failure of the 
        // operation)
        $this->assertEquals(2, count($events));

        // check that the first event is the start event
        $start_event = $events[0];
        $this->assertInstanceOf('\enrol_usi\event\plugin_enrolment_synchronization_started', $start_event);

        // check that the second event is the failure event
        $failure_event = $events[1];
        $this->assertInstanceOf('\enrol_usi\event\plugin_enrolment_synchronization_failed', $failure_event);
    }
}

