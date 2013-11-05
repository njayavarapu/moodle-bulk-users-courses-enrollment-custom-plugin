<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
define('NO_OUTPUT_BUFFERING', true);

require('../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/coursecatlib.php');
require_once($CFG->dirroot . '/enrol/courseenrol/locallib.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/pagelib.php');
global $PAGE;
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/report/studentreports/jquery-1.9.1.js'));
require_login();

$categoryId = optional_param('categoryid', 0, PARAM_INT);
$courseenrol = new enrol_courseenrol();
$studentoptions = array();
$promoteclassoptions = array();
if ($categoryId != 0) {
    $studentslist = $courseenrol->enrol_courseenrol_get_students_list_by_class($categoryId);
    foreach ($studentslist as $student) {
        $studentoptions[] = array('studentname' => $student->firstname . ' ' . $student->lastname, 'studentid' => $student->userid);
    }
    $parent = 0;
    $classtypes = $courseenrol->enrol_courseenrol_get_course_categories($parent);
    foreach ($classtypes as $key => $classtype) {
        if ($categoryId != $key) {
            $promoteclassoptions[] = array('promoteclassid' => $key, 'promoteclassname' => $classtype->categoryname);
        }
    }
}
echo json_encode(array('studentoptions' => $studentoptions, 'promoteclassoptions' => $promoteclassoptions));
?>
