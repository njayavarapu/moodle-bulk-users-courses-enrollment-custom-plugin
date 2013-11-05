<?php
require_once('../../config.php');
require_once($CFG->dirroot . '/enrol/courseenrol/locallib.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();

admin_externalpage_setup('courseenrol');

$context = get_context_instance(CONTEXT_SYSTEM);

require_capability('moodle/cohort:view', $context, $USER->id);

$cohortid = optional_param('cohortid', 0, PARAM_INT);
$categoryId = optional_param('catId', 0, PARAM_INT);
$courseenrol = new enrol_courseenrol();

$userlist = $courseenrol->enrol_courseenrol_get_enroled_students($cohortid, $categoryId);
$total = 0;

$head = array(get_string('enrol_enrolstudent', 'enrol_courseenrol'),get_string('enrol_studentprofilelink', 'enrol_courseenrol'));
$data = array();

if (empty($userlist)) {
    $data[] = array(get_string('enrol_emptycohort', 'enrol_courseenrol'),'');
} else {
    foreach ($userlist as $user) {
        $link = new moodle_url('/user/profile.php', array('id' => $user->uid));
        $data[] = array($user->usrname, '<a href="'.$link.'" >'.get_string('enrol_userprofile', 'enrol_courseenrol').'</a>');
        $total++;
    }
    $data[] = array(get_string('enrol_total', 'enrol_courseenrol'), $total);
}
$class = $courseenrol->enrol_courseenrol_get_class_by_category_id($categoryId);
$table = new html_table();
$table->head = $head;
$table->width = '60%';
$table->data = $data;

$return = new moodle_url('/enrol/courseenrol/index.php');

echo $OUTPUT->header();
echo $OUTPUT->heading($class->categoryname.' '.get_string('enrol_cohortusers', 'enrol_courseenrol'));


echo html_writer::table($table);

echo '<a href="'.$return.'"><button>Back</button></a>';
echo $OUTPUT->footer();
?>
