<?php

namespace block_progress_time\output;

use renderable;
use renderer_base;
use templatable;

class report implements renderable, templatable {
    protected $context;
    protected $course;

    public function __construct($context, $course) {
        $this->context = $context;
        $this->course = $course;
    }

    public function export_for_template(renderer_base $output) {
        $table = new \block_progress_time\table\report('block_progress_time-progress-table', $this->context, $this->course);

        $table->collapsible(false);

        ob_start();
        $table->out(30, true);
        $progresstable = ob_get_contents();
        ob_end_clean();

        return [
            'progresstable' => $progresstable
        ];
    }
}
