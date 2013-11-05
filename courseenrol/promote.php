<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/enrol/courseenrol/locallib.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();

admin_externalpage_setup('courseenrol');

$classesid = optional_param('classid', 0, PARAM_INT);
$studentids = $_POST['studentid'];
$promoteclassid = optional_param('promoteclassid', 0, PARAM_INT);

//print_r($studentids);
//print_r($_POST);
//exit;
$courseenrol = new enrol_courseenrol();


if (($classesid != 0) && (count($studentids) > 0 ) && ($promoteclassid != 0) ) {
    $classcohorts = $courseenrol->enrol_courseenrol_get_cohort_by_category_id($classesid);
    foreach ($classcohorts as $key => $cohort) {
        $primaryclasscohortid = $key;
    }
    $promoteclasscohorts = $courseenrol->enrol_courseenrol_get_cohort_by_category_id($promoteclassid);
    foreach ($promoteclasscohorts as $key => $cohort) {
        $promoteclasscohortid = $key;
    }
    $params = array();
    $params['cohortid'] = $primaryclasscohortid;
    $params['newcohortid'] = $promoteclasscohortid;
    $params['categoryid'] = $classesid;
    $params['studentids'] = $studentids;
    $disableStudentAccess = $courseenrol->enrol_courseenrol_disable_student_access_to_class($params);


    if ($disableStudentAccess == true) {

        $params = array();
        $params['cohortid'] = $promoteclasscohortid;
        $params['categoryid'] = $promoteclassid;
        $params['studentids'] = $studentids;
        $disableStudentAccess = $courseenrol->enrol_courseenrol_promote_student_to_class($params);
    }
    $returnurl = new moodle_url('/enrol/courseenrol/studentpromote.php', array('msg'=>'success'));
    redirect($returnurl);
}
$returnurl = new moodle_url('/enrol/courseenrol/studentpromote.php', array('msg'=>'error'));
    redirect($returnurl);
?>
