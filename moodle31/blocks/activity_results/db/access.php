<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'local/requestmanager:use' => array(
        'riskbitmask' => RISK_SPAM,
        'captype' => 'write',
        'contextlevel' => CONTEXT_USER,
        'archetypes' => array(
            'teacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        )
    ),
);
