<?php

/**
 * Synchronization related tests.
 *
 * @package    local_netidsync
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
defined('MOODLE_INTERNAL') || die();

require_once('fake_netid.php');
require_once('fake_moodle_data_provider.php');
  
function create_fake_netid_user($id) {
	return new \local_netidsync\user('CommonName', 'GivenName', $id . '_uid', 'mail_' . $id, $id, 'Surname', null);
}

class local_netidsync_synchronization_testcase extends advanced_testcase {

    protected $moodle_users;
    protected $netid_users;
    protected $fake_netid;
    protected $fake_moodle_data_provider;

	protected function setUp() {
        // reset all fater the test operations
        $this->resetAfterTest(true);

        // create some fake Moodle users 
        $this->moodle_users = array();
        foreach (range(0,3) as $i) {
            $this->moodle_users[] = $this->getDataGenerator()->create_user();
        }
        
        // create some fake NetID users 
        $this->netid_users = array();
        foreach (range(0,3) as $i) {
			$this->netid_users[] = create_fake_netid_user("000$i@unisi.ch"); 
        }
       
        /*
         * Fake NetID initialization
         */

        // create a fake NetID Sync instance
        $this->fake_netid = fake_netid::create();

		// inject the fake NetID in the netid factory so that we can tests 
		// all the possible situations
        \local_netidsync\netid_factory::inject_object($this->fake_netid);

		/*
		 * Fake Moodle data provider initialization
		 */
		$this->fake_moodle_data_provider = fake_moodle_data_provider::create();

		// inject the fake moodle data provider in the moodle data provider factory so that we can tests 
		// all the possible situations
		\local_netidsync\moodle_data_provider_factory::inject_object($this->fake_moodle_data_provider);
    }

	/**
	 * Compute the changes of the synchronization and check the results.
	 */
	public function test_compute_changes() {
        // prepare the fake NetID Sync returned data so that they reflect the current 
        // situation (thus no enrolments/unenrolments will be executed during the 
        // synchronization)
        $fake_netid_users = array();
		$moodle_fake_users = array();
        foreach ($this->moodle_users as $user) {
            $fake_netid_users[$user->username] = create_fake_netid_user($user->username);
			$fake_moodle_users[$user->username] = $user;
        }

        // inject the fake data in the NetID handler
        $this->fake_netid->inject_users($fake_netid_users);

		// inject the fake data in the moodle data provider
		$this->fake_moodle_data_provider->inject_users($fake_moodle_users);

		// execute the sync and check that it succeds
		$netid_sync = \local_netidsync\netid_sync_factory::create();
        $changes = $netid_sync->compute_synchronization_changes();

		// check that there are zero changes
		$this->assertEquals(0, count($changes['new_users']));
		$this->assertEquals(0, count($changes['old_users']));
	}

    /**
     * Execute a sync operation that should not modify the existing students 
     * (the Moodle students are equals to the ones returned by the NetID 
	 * handler). 
	 */
    public function test_netid_synchronization_without_changes() {
        global $CFG, $DB;

        // redirect the events
        $sink = $this->redirectEvents();

        // prepare the fake NetID Sync returned data so that they reflect the current 
        // situation (thus no enrolments/unenrolments will be executed during the 
        // synchronization)
        $fake_netid_users = array();
		$fake_moodle_users = array();
        foreach ($this->moodle_users as $user) {
            $fake_netid_users[$user->username] = create_fake_netid_user($user->username);
			$fake_moodle_users[$user->username] = $user;
        }

        // inject the fake data in the NetID handler
        $this->fake_netid->inject_users($fake_netid_users);

		// inject the fake data in the moodle data provider
		$this->fake_moodle_data_provider->inject_users($fake_moodle_users);

		// execute the sync and check that it succeds
		$netid_sync = \local_netidsync\netid_sync_factory::create();
        $this->assertTrue($netid_sync->perform_synchronization());

        // retrieve the catched events
        $events = $sink->get_events();

        // stop the events redirection
        $sink->close();

        // check that the enrolled users are always the same
        $this->assertEquals(0, $DB->count_records('user', array('auth' => 'shibboleth')));

        // check that the triggered events are two (the start and the end of the 
        // operation)
        $this->assertEquals(2, count($events));

        // check that the first event is the start event
        $start_event = $events[0];
        $this->assertInstanceOf('\local_netidsync\event\netid_synchronization_started', $start_event);

        // check that the second event is the end event
        $end_event = $events[1];
        $this->assertInstanceOf('\local_netidsync\event\netid_synchronization_completed', $end_event);
    }

    /**
     * Execute a sync operation after respectively adding new students and 
	 * removing only one from the fake NetID. 
	 */
    public function test_netid_synchronization_with_changes() {
        global $CFG, $DB;

        // redirect the events
        $sink = $this->redirectEvents();

        // prepare the fake NetID Sync returned data so that they reflect the current 
        // situation (thus no enrolments/unenrolments will be executed during the 
        // synchronization)
        $fake_netid_users = array();
		$fake_moodle_users = array();
        foreach ($this->moodle_users as $user) {
	        $fake_netid_users[$user->username] = create_fake_netid_user($user->username);
			$fake_moodle_users[$user->username] = $user;
        }

		// add other NetID users
		foreach($this->netid_users as $user) {
			$fake_netid_users[$user->get_swiss_edu_person_unique_id()] = $user;
		}

        // inject the fake data in the NetID handler
        $this->fake_netid->inject_users($fake_netid_users);

		// inject the fake data in the moodle data provider
		$this->fake_moodle_data_provider->inject_users($fake_moodle_users);

		// execute the sync and check that it succeds
		$netid_sync = \local_netidsync\netid_sync_factory::create();
        $this->assertTrue($netid_sync->perform_synchronization());

        // retrieve the catched events
        $events = $sink->get_events();

        // stop the events redirection
        $sink->close();

        // check that the enrolled users are always the same
        $this->assertEquals(count($this->netid_users), $DB->count_records('user', array('auth' => 'shibboleth')));

        // check that the triggered events are two (the start and the end of the 
        // operation)
        $this->assertEquals(2, count($events));

        // check that the first event is the start event
        $start_event = $events[0];
        $this->assertInstanceOf('\local_netidsync\event\netid_synchronization_started', $start_event);

        // check that the second event is the end event
        $end_event = $events[1];
        $this->assertInstanceOf('\local_netidsync\event\netid_synchronization_completed', $end_event);
    }

    /**
     * Execute a synchronization that fails because of a netid_connection_failed_exception.
	 */
    public function test_netid_synchronization_with_netid_connection_failure() {
		global $DB;

		// keep track of how many users there were before the operation
		$initial_users_count = $DB->count_records('user', array('auth' => 'shibboleth'));

        // redirect the events
        $sink = $this->redirectEvents();

        // change the fake NetID so that when a call to get_users is made, the 
        // given closure is called
        $this->fake_netid->fail_on('get_users', function ($module_id) {
			throw new \local_netidsync\netid_connection_failed_exception('FAKE_EXCEPTION');
        }); 	

        // execute the sync and check that it fails
		$netid_sync = \local_netidsync\netid_sync_factory::create();
        $this->assertFalse($netid_sync->perform_synchronization());

        // retrieve the catched events
        $events = $sink->get_events();

        // stop the events redirection
        $sink->close();

        // check that the enrolled users are always the same
        $this->assertEquals($initial_users_count, $DB->count_records('user', array('auth' => 'shibboleth')));

        // check that the triggered events are two (the start and the end of the operation)
        $this->assertEquals(2, count($events));

        // check that the first event is the start event
        $start_event = $events[0];
        $this->assertInstanceOf('\local_netidsync\event\netid_synchronization_started', $start_event);

        // check that the second event is the failure event
        $failure_event = $events[1];
        $this->assertInstanceOf('\local_netidsync\event\netid_synchronization_failed', $failure_event);
    }

    /**
     * Execute a synchronization that fails because of a netid_binding_failed_exception.
	 */
    public function test_netid_synchronization_with_netid_binding_failure() {
		global $DB;

		// keep track of how many users there were before the operation
		$initial_users_count = $DB->count_records('user', array('auth' => 'shibboleth'));

        // redirect the events
        $sink = $this->redirectEvents();

        // change the fake NetID so that when a call to get_users is made, the 
        // given closure is called
        $this->fake_netid->fail_on('get_users', function ($module_id) {
			throw new \local_netidsync\netid_binding_failed_exception('FAKE_EXCEPTION');
        }); 	

        // execute the sync and check that it fails
		$netid_sync = \local_netidsync\netid_sync_factory::create();
        $this->assertFalse($netid_sync->perform_synchronization());

        // retrieve the catched events
        $events = $sink->get_events();

        // stop the events redirection
        $sink->close();

        // check that the enrolled users are always the same
        $this->assertEquals($initial_users_count, $DB->count_records('user', array('auth' => 'shibboleth')));

        // check that the triggered events are two (the start and the end of the operation)
        $this->assertEquals(2, count($events));

        // check that the first event is the start event
        $start_event = $events[0];
        $this->assertInstanceOf('\local_netidsync\event\netid_synchronization_started', $start_event);

        // check that the second event is the failure event
        $failure_event = $events[1];
        $this->assertInstanceOf('\local_netidsync\event\netid_synchronization_failed', $failure_event);
    }

    /**
     * Execute a synchronization that fails because of a netid_search_failed_exception.
	 */
    public function test_netid_synchronization_with_netid_search_failure() {
		global $DB;

		// keep track of how many users there were before the operation
		$initial_users_count = $DB->count_records('user', array('auth' => 'shibboleth'));

        // redirect the events
        $sink = $this->redirectEvents();

        // change the fake NetID so that when a call to get_users is made, the 
        // given closure is called
        $this->fake_netid->fail_on('get_users', function ($module_id) {
			throw new \local_netidsync\netid_search_failed_exception('FAKE_EXCEPTION');
        }); 	

        // execute the sync and check that it fails
		$netid_sync = \local_netidsync\netid_sync_factory::create();
        $this->assertFalse($netid_sync->perform_synchronization());

        // retrieve the catched events
        $events = $sink->get_events();

        // stop the events redirection
        $sink->close();

        // check that the enrolled users are always the same
        $this->assertEquals($initial_users_count, $DB->count_records('user', array('auth' => 'shibboleth')));

        // check that the triggered events are two (the start and the end of the operation)
        $this->assertEquals(2, count($events));

        // check that the first event is the start event
        $start_event = $events[0];
        $this->assertInstanceOf('\local_netidsync\event\netid_synchronization_started', $start_event);

        // check that the second event is the failure event
        $failure_event = $events[1];
        $this->assertInstanceOf('\local_netidsync\event\netid_synchronization_failed', $failure_event);
    }
}

