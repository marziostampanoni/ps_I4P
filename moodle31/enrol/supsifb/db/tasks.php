<?php

/**
 * In this file are specified the timings that Moodle uses with cron in order to 
 * periodically launch tasks.
 *
 * @package    enrol_supsifb
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$tasks = array(
    /**
     * Enrolments synchronization task
     */
    array(
        'classname' => 'enrol_supsifb\task\enrolments_sync',
        'blocking' => 0,
        'minute' => '30',
        'hour' => '2',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    )
);

