<?php

require_once('../../config.php');
require_once($CFG->dirroot . '/enrol/courseenrol/locallib.php');
require_once($CFG->libdir . '/adminlib.php');
//@ini_set('display_errors', '1'); // NOT FOR PRODUCTION SERVERS!
//$CFG->debug = 32767;         // DEBUG_DEVELOPER // NOT FOR PRODUCTION SERVERS!
//// for Moodle 2.0 - 2.2, use:  $CFG->debug = 38911;  
//$CFG->debugdisplay = true;   // NOT FOR PRODUCTION SERVERS!
require_login();

admin_externalpage_setup('courseenrol');

$context = get_context_instance(CONTEXT_SYSTEM);

require_capability('moodle/cohort:view', $context, $USER->id);

$cohortId = optional_param('cohortId', 0, PARAM_INT);
$categoryId = optional_param('catId', 0, PARAM_INT);

$courseenrol = new enrol_courseenrol();

$newStudentsInfo = $courseenrol->enrol_courseenrol_get_new_students_by_cohort_id($cohortId, $categoryId);
$total = 0;

$head = array(get_string('enrol_newstudentslist', 'enrol_courseenrol'), get_string('enrol_studentprofilelink', 'enrol_courseenrol'));
$data = array();
if (!empty($newStudentsInfo)) {
    foreach ($newStudentsInfo as $student) {
            $link = new moodle_url('/user/profile.php', array('id' => $student->uid));
            $data[] = array($student->usrname, '<a href="' . $link . '" >' . get_string('enrol_userprofile', 'enrol_courseenrol') . '</a>');
            $total++;
    }
    
    $data[] = array(get_string('enrol_total', 'enrol_courseenrol'), $total);
}
$class = $courseenrol->enrol_courseenrol_get_class_by_category_id($categoryId);
$table = new html_table();
$table->head = $head;
$table->width = '60%';
$table->data = $data;

echo $classname;

echo $OUTPUT->header();
echo $OUTPUT->heading($class->categoryname.' '.get_string('enrol_newstudentlist', 'enrol_courseenrol'));

$return = new moodle_url('/enrol/courseenrol/index.php');

echo html_writer::table($table);

echo '<a href="'.$return.'"><button>Back</button></a>';
echo $OUTPUT->footer();
?>
