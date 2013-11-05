<?php

define('NO_OUTPUT_BUFFERING', true);

require('../../config.php');
require_once($CFG->dirroot . '/enrol/courseenrol/locallib.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();

$param['parent'] = 0;
$courseenrol = new enrol_courseenrol();

$classesInfo = $courseenrol->enrol_courseenrol_get_category_users_enrolment_info($param);
//echo '<pre>';
//print_r($classesInfo);
//echo '</pre>';
$row = array();
$cell = array();
$rownum = 0;
//$totalEnrollments = $DB->count_records('user_enrolments', array('userid'=>$userid, 'enrolid'=>$enrolid));
foreach ($classesInfo as $key => $val) {
    $catid = $val['categoryId'];
    $color = ($val['component'] == 'enrol_courseenrol') ? '#fffff !important' : '#fffff !important';
    $viewurl = new moodle_url('/enrol/courseenrol/view.php', array('cohortid' => $key, 'catId' => $catid));
    $coursesViewurl = new moodle_url('/enrol/courseenrol/newcourses.php', array('cohortId' => $key, 'catId' => $catid));
    $studentViewurl = new moodle_url('/enrol/courseenrol/newstudents.php', array('cohortId' => $key, 'catId' => $catid));
    $enrollurl = new moodle_url('/enrol/courseenrol/enroll.php', array('cohortid' => $key, 'parent' => $catid));

    $newstudents = $courseenrol->enrol_courseenrol_get_new_students_by_cohort_id($key, $catid);
    $coursesinfo = $courseenrol->enrol_courseenrol_get_new_courses($key, $catid);
    $newcourses = $coursesinfo['newcourses'];
    $totalcourses = $coursesinfo['totalcourses'];
//    if($catid == 23){
//    echo '<pre>';
//    
//    print_r($newcourses);
//    echo '</pre>';
//    }
    $row[$rownum] = new html_table_row();
    $cell[1] = new html_table_cell();
    $cell[2] = new html_table_cell();
    $cell[3] = new html_table_cell();
    $cell[4] = new html_table_cell();

    $cell[1]->text = $val['categoryName'];
    //.$val['studentEnrolledTotalCourcesCount'] . '!=' . $val['totalCourses'] . ' ==== ' . $val['enrollCourseCount'] . '!=' . $val['courseCount'] . ' ==== ' . $val['count'] . '>' . $val['enrollCount'];
    $studentcount = $val['studentscount'] - count($newstudents);
    if ($studentcount > 0) {
        $cell[2]->text = '<a href="' . $viewurl . '">' . $studentcount . '</a>';
    } else {
        $cell[2]->text = $studentcount;
    }

    //. $val['courseCount'] . '!=' . $val['enrollCourseCount'] . ' ==== ' . $val['studentscount'] . '>' . $val['enrollCount'];
    ////////////////////////////////////////////////////////////////////////
    $enrollmentStatus = get_string('enrol_statusmessage', 'enrol_courseenrol');
    if ((count($newcourses) == 0) && (count($newstudents) == 0)) {
        $enrollmentStatus = get_string('enrol_nostudentsandcourses', 'enrol_courseenrol');
    } else if (count($newstudents) > 0) {
        $enrollmentStatus = '<a href="' . $studentViewurl . '" style="color:red !important;">' . count($newstudents) . ' ' . get_string('enrol_newstudents', 'enrol_courseenrol') . ' </a>';
    } else if (count($newcourses) > 0) {
        $enrollmentStatus = '<a href="' . $coursesViewurl . '" style="color:red !important;">' . count($newcourses) . ' ' . get_string('enrol_newcourses', 'enrol_courseenrol') . ' </a>';
    } else if ((count($newcourses) > 0) && (count($newstudents) > 0)) {
        
    }

    $cell[3]->text = $enrollmentStatus;
    ////////////////////////////////////////////////////////////////////////
    //. $val['studentEnrolledTotalCourcesCount'] .'!='. $val['totalCourses'].' ==== '. $val['count'] .'>'. $val['enrollCount'];
    //.$val['enrollCourseCount'] .'!='. $val['courseCount'].' ==== '. $val['count'] .'>'. $val['enrollCount'];

    if ($totalcourses > 0) {
        if ((count($newstudents) > 0) && (count($newcourses) > 0)) {
            $cell[4]->text = '<a href="' . $enrollurl . '"><button onclick="this.disabled=\'true\'";>Update enrolments</button></a>';
        } else if (($val['studentscount'] > 0) && (count($newcourses) > 0)) {
            $cell[4]->text = '<a href="' . $enrollurl . '"><button onclick="this.disabled=\'true\'";>Update enrolments</button></a>';
        } else if ((count($newstudents) == 0) && (count($newcourses) == 0)) {
            $cell[4]->text = get_string('enrol_allstudentsenrolled', 'enrol_courseenrol');
        } else if ($val['studentscount'] == 0) {
            $cell[4]->text = get_string('enrol_nonewstudents', 'enrol_courseenrol');
        }
    } else {
        $cell[4]->text = get_string('enrol_nonewcourses', 'enrol_courseenrol');
    }

    $cell[1]->style = 'font-weight: bold; background-color: ' . $color . ';';
    $cell[2]->style = 'font-weight: bold; background-color: ' . $color . ';padding-left:20px;align:left;';
    $cell[3]->style = 'font-style: italic; background-color: ' . $color . ';';
    $cell[4]->style = 'background-color: ' . $color . ';';

    $row[$rownum]->cells = $cell;
    $rownum++;
}
$table = new html_table();
$table->head = array(get_string('enrol_coursecategory', 'enrol_courseenrol'), get_string('enrol_count', 'enrol_courseenrol'), get_string('enrol_status', 'enrol_courseenrol'), get_string('enrol_update', 'enrol_courseenrol'));
$table->width = '80%';
$table->data = $row;

admin_externalpage_setup('courseenrol', '', null, '', array('pagelayout' => 'report'));
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('pluginname', 'enrol_courseenrol'));

echo html_writer::table($table);


echo $OUTPUT->footer();
?>
