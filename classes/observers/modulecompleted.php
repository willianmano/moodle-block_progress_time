<?php

namespace block_progress_time\observers;

use block_progress_time\util\progress;

class modulecompleted {
    public static function observer(\core\event\base $event) {
        if (!is_enrolled($event->get_context(), $event->relateduserid)) {
            return;
        }

        if (has_capability('moodle/course:update', $event->get_context(), $event->relateduserid)) {
            return;
        }

        $util = new progress($event->courseid, $event->relateduserid);

        $util->update_user_course_progress();
    }
}