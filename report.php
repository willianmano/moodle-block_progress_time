<?php

require_once('../../config.php');

$id = required_param('id', PARAM_INT);

$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);

$url = new \moodle_url('/blocks/progress_time/report.php', ['id' => $id]);

$context = \context_course::instance($id);

if (!has_capability('moodle/course:update', $context)) {
    redirect(
        new \moodle_url('/course/view.php', ['id' => $id]),
        \core\notification::error('Você não tem permissão para acessar essa página.')
    );
}

$title = 'Relatório de progresso no curso: ' . $course->shortname;

$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading($title);

echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('block_progress_time');

$content = new \block_progress_time\output\report($context, $course);

echo $renderer->render($content);

echo $OUTPUT->footer();