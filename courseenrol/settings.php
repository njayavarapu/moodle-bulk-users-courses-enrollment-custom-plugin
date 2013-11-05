<?php


defined('MOODLE_INTERNAL') || die;

$ADMIN->add('courses', new admin_externalpage('courseenrol', get_string('pluginname', 'enrol_courseenrol'),
        $CFG->wwwroot."/enrol/courseenrol/index.php", 'enrol/courseenrol:view'));

$ADMIN->add('courses', new admin_externalpage('courseenrol', get_string('enrol_studentpromotion', 'enrol_courseenrol'),
        $CFG->wwwroot."/enrol/courseenrol/studentpromote.php", 'enrol/courseenrol:view'));


// No report settings.
$settings = null;
