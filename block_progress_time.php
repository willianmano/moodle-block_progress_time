<?php

use stdClass;

class block_progress_time extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_progress_time');
    }

    public function applicable_formats() {
        return [
            'course-view' => true,
            'site' => false,
            'mod' => false,
            'my' => false
        ];
    }

    public function get_content() {
        if (isset($this->content)) {
            return $this->content;
        }

        $this->content = new stdClass();

        $renderer = $this->page->get_renderer('block_progress_time');

        $contentrenderable = new \block_progress_time\output\block($this->page->course, $this->context);

        $this->content->text = $renderer->render($contentrenderable);

        return $this->content;
    }
}
