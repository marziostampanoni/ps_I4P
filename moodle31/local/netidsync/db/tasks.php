<?php

/**
 * In this file are specified the timings that Moodle uses with cron in order to 
 * periodically launch tasks.
 *
 * @package    local_netidsync
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$tasks = array(
    /**
     * NetID synchronization task
     */
    array(
        'classname' => 'local_netidsync\task\netid_synchronization',
        'blocking' => 0,
        'minute' => '30',
        'hour' => '1',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    )
);

