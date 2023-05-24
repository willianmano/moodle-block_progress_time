<?php

namespace block_progress_time\output;

use block_progress_time\util\progress;
use renderable;
use renderer_base;
use templatable;

class block implements renderable, templatable {

    protected $course;
    protected $context;

    public function __construct($course, $context) {
        $this->course = $course;
        $this->context = $context;
    }

    public function export_for_template(renderer_base $output) {
        global $USER;

        $util = new progress($this->course->id, $USER->id);

        $progress = $util->get_user_course_progress();

        return [
            'courseid' => $this->course->id,
            'progress' => $progress,
            'canviewreport' => has_capability('moodle/course:update', $this->context)
        ];
    }
}
