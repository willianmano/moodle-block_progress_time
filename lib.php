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
    global $DB;

    if (!block_progress_time_course_has_block($formwrapper->get_current()->course)) {
        return;
    }

    $mform->addElement('header', 'progresstimeformheader', get_string('progresstimeformheader', 'block_progress_time'));

    $options = array(0 => 0, 5 => 5, 10 => 10, 15 => 15, 20 => 20, 25 => 25, 30 => 30, 35 => 35, 40 => 40,
        45 => 45, 50 => 50, 60 => 60, 70 => 70, 80 => 80, 90 => 90, 100 => 100);

    $mform->addElement('select', 'activityhours', get_string('activityhours', 'block_progress_time'), $options);
    $mform->addHelpButton('activityhours', 'activityhours', 'block_progress_time');
    $mform->setType('activityhours', PARAM_INT);
    // Se a conclusao de atividade estiver desabilitado, desabilitar o campo.
    $mform->disabledIf('activityhours', 'completion', 'eq', 0);

    if (!is_null($formwrapper->get_coursemodule()) && $cmid = $formwrapper->get_coursemodule()->id) {
        if ($activityhours = $DB->get_record('progress_time_modules', ['cmid' => $cmid], 'id, value')) {
            $mform->setDefault('activityhours', $activityhours->value);
        }
    }
}

function block_progress_time_coursemodule_edit_post_actions($moduleinfo, $course) {
    if (isset($moduleinfo->activityhours)) {
        block_progress_time_sync_activityhours($moduleinfo->coursemodule, (int) $moduleinfo->activityhours);
    }

    return $moduleinfo;
}

function block_progress_time_sync_activityhours($cmid, $activityhours) {
    global $DB;

    $data = $DB->get_record('progress_time_modules', ['cmid' => $cmid]);

    // Atualiza valor no banco.
    if ($data && $activityhours > 0) {
        $data->value = $activityhours;
        $data->timemodified = time();

        return $DB->update_record('progress_time_modules', $data);
    }

    // Insere novo registro no banco.
    if (!$data && $activityhours > 0) {
        $data = new \stdClass();
        $data->value = $activityhours;
        $data->cmid = $cmid;
        $data->timecreated = time();
        $data->timemodified = time();

        return $DB->insert_record('progress_time_modules', $data);
    }

    // Exclui registro do banco.
    if ($data && $activityhours == 0) {
        return $DB->delete_records('progress_time_modules', ['cmid' => $cmid]);
    }

    return true;
}

function block_progress_time_course_has_block($courseid) {
    global $DB;

    $context = \context_course::instance($courseid);

    return $DB->record_exists('block_instances', ['blockname' => 'progress_time', 'parentcontextid' => $context->id]);
}
