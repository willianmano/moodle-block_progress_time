<?php

namespace block_progress_time\output;

use plugin_renderer_base;
use renderable;

class renderer extends plugin_renderer_base {
    public function render_block(renderable $page) {
        $data = $page->export_for_template($this);

        return parent::render_from_template('block_progress_time/block', $data);
    }
}
