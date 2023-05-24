<?php

namespace block_progress_time\table;

require_once($CFG->libdir.'/tablelib.php');

use table_sql;

class report extends table_sql {
    protected $context;
    protected $course;

    public function __construct($uniqueid, $context, $course) {
        parent::__construct($uniqueid);

        $this->context = $context;
        $this->course = $course;

        $this->define_columns($this->get_columns());

        $this->define_headers($this->get_headers());

        $this->base_sql();

        $this->define_baseurl(new \moodle_url('/blocks/progress_time/report.php', ['id' => $this->course->id]));
    }

    private function get_columns() {
        return ['id', 'fullname', 'email', 'progress'];
    }

    private function get_headers() {
        return [
            'ID',
            'Nome completo',
            'Email',
            'Progresso'
        ];
    }

    public function col_progress($user) {
        $progress = $user->progress ?? 0;

        return "<div class='progress'>
                    <div class='progress-bar bg-success'
                         role='progressbar'
                         style='width: {$progress}%'
                         aria-valuenow=''{$progress}'
                         aria-valuemin='0'
                         aria-valuemax='100'>
                        {$progress}%
                    </div>
                </div>";
    }

    private function base_sql() {
        $fields = 'DISTINCT u.*, p.progress';

        $capjoin = get_enrolled_with_capabilities_join($this->context, '', 'moodle/course:viewparticipants');

        $from = ' {user} u ' . $capjoin->joins;

        $from .= ' LEFT JOIN {progress_time_progress} p ON p.userid = u.id AND p.courseid = :courseid';

        $where = $capjoin->wheres;

        $params = $capjoin->params;
        $params['courseid'] = $this->course->id;

        $this->set_sql($fields, $from, $where, $params);
    }
}