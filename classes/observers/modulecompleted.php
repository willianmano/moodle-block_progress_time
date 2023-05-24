<?php

namespace block_progress_time\observers;

use core\event\base as baseevent;

class modulecompleted {
    public static function observer(baseevent $event) {
        global $DB;

        if (!is_enrolled($event->get_context(), $event->relateduserid)) {
            return;
        }

        // Avoid calculate progress for teachers, admins, anyone who can edit course.
        if (has_capability('moodle/course:update', $event->get_context(), $event->relateduserid)) {
            return;
        }

        $util = new \block_progress_time\util\progress();

        $util->update_user_course_progress($event->courseid, $event->relateduserid);
    }
}
