<?php

require_once('../../config.php');
require_once($CFG->dirroot . '/enrol/courseenrol/locallib.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();

admin_externalpage_setup('courseenrol');

$context = get_context_instance(CONTEXT_SYSTEM);

require_capability('moodle/cohort:view', $context, $USER->id);

$categoryId = optional_param('catId', 0, PARAM_INT);
$cohortId = optional_param('cohortId', 0, PARAM_INT);
$courseenrol = new enrol_courseenrol();
$coursesinfo = $courseenrol->enrol_courseenrol_get_new_courses($cohortId, $categoryId);
$newcourses = $coursesinfo['newcourses'];
$head = array(get_string('enrol_newcourseslist', 'enrol_courseenrol'), get_string('enrol_courselink', 'enrol_courseenrol'));
//$subcategories = $courseenrol->enrol_courseenrol_get_subjects_by_class($categoryId);
$data = array();
$total = 0;

foreach ($newcourses AS $key => $newcourse) {
    $link = new moodle_url('/course/view.php', array('id' => $key));
    $data[] = array($newcourse, '<a href="' . $link . '" >' . get_string('enrol_courseInfo', 'enrol_courseenrol') . '</a>');
    $total++;
}
$data[] = array(get_string('enrol_total', 'enrol_courseenrol'), $total);
$class = $courseenrol->enrol_courseenrol_get_class_by_category_id($categoryId);
$table = new html_table();
$table->head = $head;
$table->width = '60%';
$table->data = $data;



echo $OUTPUT->header();
echo $OUTPUT->heading($class->categoryname . ' ' . get_string('enrol_newcourselist', 'enrol_courseenrol'));

$return = new moodle_url('/enrol/courseenrol/index.php');

echo html_writer::table($table);

echo '<a href="' . $return . '"><button>Back</button></a>';
echo $OUTPUT->footer();
?>
