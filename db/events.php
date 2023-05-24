<?php

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\core\event\course_module_completion_updated',
        'callback' => '\block_progress_time\observers\modulecompleted::observer',
        'internal' => false
    ]
];