<?php

namespace block_progress_time\output;

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
//        $completion = (int)(\core_completion\progress::get_course_progress_percentage($this->course));

        $util = new \block_progress_time\util\progress();

        return [
            'progress' => $util->get_user_course_progress($this->course->id),
            'canviewreport' => has_capability('moodle/course:update', $this->context)
        ];
    }
}
