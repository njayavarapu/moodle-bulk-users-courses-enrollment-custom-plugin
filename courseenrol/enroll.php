<?php

define('NO_OUTPUT_BUFFERING', true);

require('../../config.php');
require_once($CFG->dirroot . '/enrol/courseenrol/locallib.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();

$params['cohortid'] = optional_param('cohortid', 0, PARAM_INT);
$params['categoryid'] = optional_param('parent', 0, PARAM_INT);

$courseenrol = new enrol_courseenrol();
$courseenrol->enrol_courseenrol_users_to_category_courses($params);

$returnurl = new moodle_url('/enrol/courseenrol/index.php', array());
redirect($returnurl);
?>
