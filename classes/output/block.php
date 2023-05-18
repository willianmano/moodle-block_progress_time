<?php

namespace block_progress_time\output;

use renderable;
use renderer_base;
use templatable;

class block implements renderable, templatable {

    protected $course;

    public function __construct($course) {
        $this->course = $course;
    }

    public function export_for_template(renderer_base $output) {
        $completion = (int)(\core_completion\progress::get_course_progress_percentage($this->course));

        return [
            'progress' => $completion
        ];
    }
}
