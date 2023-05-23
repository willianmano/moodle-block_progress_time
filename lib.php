<?php

defined('MOODLE_INTERNAL') || die();

/**
 * @param moodleform $formwrapper The moodle quickforms wrapper object.
 * @param MoodleQuickForm $mform The actual form object (required to modify the form).
 * https://docs.moodle.org/dev/Callbacks
 * This function name depends on which plugin is implementing it. So if you were
 * implementing mod_wordsquare
 * This function would be called wordsquare_coursemodule_standard_elements
 * (the mod is assumed for course activities)
 */
function block_progress_time_coursemodule_standard_elements($formwrapper, $mform) {
    if (!block_progress_time_course_has_block($formwrapper->get_current()->course)) {
        return;
    }

    $mform->addElement('header', 'progresstimeformheader', get_string('progresstimeformheader', 'block_progress_time'));

    $options = array(0 => 0, 5 => 5, 10 => 10, 15 => 15, 20 => 20, 25 => 25, 30 => 30, 35 => 35, 40 => 40,
        45 => 45, 50 => 50, 60 => 60, 70 => 70, 80 => 80, 90 => 90, 100 => 100);

    $mform->addElement('select', 'activityhours', get_string('activityhours', 'block_progress_time'), $options);
    $mform->setType('activityhours', PARAM_INT);

    if (!is_null($formwrapper->get_coursemodule()) && $cmid = $formwrapper->get_coursemodule()->id) {
        $cmid = $cmid;
    }
}

function block_progress_time_coursemodule_edit_post_actions($moduleinfo, $course) {
    return $moduleinfo;
}

function block_progress_time_course_has_block($courseid) {
    return true;
}
