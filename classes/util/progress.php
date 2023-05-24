<?php

namespace block_progress_time\util;

class progress {
    public function get_course_total_hours($courseid) {
        global $DB;

        $sql = 'SELECT SUM(tm.value) as coursehours
                FROM {progress_time_modules} tm
                INNER JOIN {course_modules} cm ON cm.id = tm.cmid
                WHERE cm.course = :courseid';

        $record = $DB->get_record_sql($sql, ['courseid' => $courseid]);

        if (!$record) {
            return false;
        }

        return $record->coursehours;
    }

    public function get_user_course_activities_progress($courseid) {
        $coursemodules = $this->get_course_activities_with_progress($courseid);

        if (!$coursemodules) {
            return false;
        }

        $totalhours = (int) $this->get_course_total_hours($courseid);
        $usercompletedhours = 0;

        $completion = new \completion_info($this->get_course($courseid));
        foreach ($coursemodules as $module) {
            $data = $completion->get_data($module);

            if (($data->completionstate == COMPLETION_INCOMPLETE) || ($data->completionstate == COMPLETION_COMPLETE_FAIL)) {
                continue;
            }

            $usercompletedhours += $module->activityhours;
        }

        if ($usercompletedhours == 0) {
            return 0;
        }

        return (int) ceil(($usercompletedhours * 100 / $totalhours));
    }

    public function get_course_activities_with_progress($courseid) {
        global $DB;

        $sql = 'SELECT cm.*, tm.value as activityhours
                FROM {course_modules} cm
                INNER JOIN {progress_time_modules} tm ON tm.cmid = cm.id
                WHERE cm.course = :courseid';

        $records = $DB->get_records_sql($sql, ['courseid' => $courseid]);

        if (!$records) {
            return false;
        }

        return $records;
    }

    public function get_course($courseid) {
        global $DB;

        return $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
    }

    public function update_user_course_progress($courseid, $userid = null) {
        global $DB, $USER;

        if (!$userid) {
            $userid = $USER->id;
        }

        $record = $DB->get_record('progress_time_progress', ['userid' => $userid, 'courseid' => $courseid]);

        $usercouseprogress = $this->get_user_course_activities_progress($courseid);

        if ($record) {
            $record->progress = $usercouseprogress;
            $record->timemodified = time();

            return $DB->update_record('progress_time_progress', $record);
        }

        $record = new \stdClass();
        $record->courseid = $courseid;
        $record->userid = $userid;
        $record->progress = $usercouseprogress;
        $record->timecreated = time();
        $record->timemodified = time();

        return $DB->insert_record('progress_time_progress', $record);
    }

    public function get_user_course_progress($courseid, $userid = null) {
        global $DB, $USER;

        if (!$userid) {
            $userid = $USER->id;
        }

        $record = $DB->get_record('progress_time_progress', ['userid' => $userid, 'courseid' => $courseid], 'progress');

        if (!$record) {
            return 0;
        }

        return $record->progress;
    }
}
