<?php

/**
 * In this file are specified the timings that Moodle uses with cron in order to 
 * periodically launch tasks.
 *
 * @package    enrol_usi
 * @copyright  2014 Guglielmo Fachini {@link http://fachini.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$tasks = array(
    /**
     * USI WS Cache cleanup task
     */
    array(
        'classname' => 'enrol_usi\task\ws_cache_cleanup',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '2',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ),

    /**
     * Enrolments synchronization task
     */
    array(
        'classname' => 'enrol_usi\task\enrolments_sync',
        'blocking' => 0,
        'minute' => '30',
        'hour' => '2',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    )
);

