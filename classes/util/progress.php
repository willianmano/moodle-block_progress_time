<?php

namespace block_progress_time\util;

class progress {
    protected $course;
    protected $userid;

    public function __construct($courseid, $userid) {
        $this->course = $this->get_course($courseid);
        $this->userid = $userid;
    }

    public function get_course_total_hours() {
        global $DB;

        $sql = 'SELECT SUM(tm.value) as coursehours
                FROM {progress_time_modules} tm
                INNER JOIN {course_modules} cm ON cm.id = tm.cmid
                WHERE cm.course = :courseid';

        $record = $DB->get_record_sql($sql, ['courseid' => $this->course->id]);

        if (!$record) {
            return false;
        }

        return $record->coursehours;
    }

    public function get_courses_activities_with_hours() {
        global $DB;

        $sql = 'SELECT cm.*, tm.value as activityhours
                FROM {course_modules} cm
                INNER JOIN {progress_time_modules} tm ON tm.cmid = cm.id
                WHERE cm.course = :courseid';

        $records = $DB->get_records_sql($sql, ['courseid' => $this->course->id]);

        if (!$records) {
            return false;
        }

        return $records;
    }

    public function get_user_course_activities_progress() {
        $coursemodules = $this->get_courses_activities_with_hours();

        if (!$coursemodules) {
            return false;
        }

        $totalhours = (int) $this->get_course_total_hours();
        $usercompletedhours = 0;

        $completion = new \completion_info($this->course);

        foreach ($coursemodules as $module) {
            $data = $completion->get_data($module, false, $this->userid);

            if ($data->completionstate == COMPLETION_INCOMPLETE || $data->completionstate == COMPLETION_COMPLETE_FAIL) {
                continue;
            }

            $usercompletedhours += $module->activityhours;
        }

        if ($usercompletedhours == 0) {
            return 0;
        }

        return (int) ceil(($usercompletedhours * 100 / $totalhours));
    }

    public function update_user_course_progress() {
        global $DB;

        $usercourseprogress = $this->get_user_course_activities_progress();

        if (!$usercourseprogress) {
            return false;
        }

        $record = $DB->get_record('progress_time_progress',
            ['courseid' => $this->course->id, 'userid' => $this->userid]
        );

        if ($record) {
            $record->progress = $usercourseprogress;
            $record->timemodified = time();

            return $DB->update_record('progress_time_progress', $record);
        }

        $record = new \stdClass();
        $record->courseid = $this->course->id;
        $record->userid = $this->userid;
        $record->progress = $usercourseprogress;
        $record->timecreated = time();
        $record->timemodified = time();

        return $DB->insert_record('progress_time_progress', $record);
    }

    public function get_course($courseid) {
        global $DB;

        return $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
    }

    public function get_user_course_progress() {
        global $DB;

        $record = $DB->get_record('progress_time_progress',
            ['courseid' => $this->course->id, 'userid' => $this->userid]
        );

        if (!$record) {
            return 0;
        }

        return $record->progress;
    }
}