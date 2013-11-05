<?php

$string['check_backup'] = 'Automated backup';
$string['check_backup_comment_disable'] = 'Performance may be affected during the backup process. If enabled, backups should be scheduled for off-peak times.';
$string['check_backup_comment_enable'] = 'Performance may be affected during the backup process. Backups should be scheduled for off-peak times.';
$string['check_backup_details'] = 'Enabling automated backup will automatically create archives of all the courses on the server at the time you specified.<p>During this process, it will consume more server resources and may affect courseenrol.</p>';
$string['check_cachejs_comment_disable'] = 'If enabled, page loading courseenrol is improved.';
$string['check_cachejs_comment_enable'] = 'If disabled, page might load slow.';
$string['check_cachejs_details'] = 'Javascript caching and compression greatly improves page loading courseenrol. It is strongly recommended for production sites.';
$string['check_debugmsg_comment_nodeveloper'] = 'If set to DEVELOPER, courseenrol may be affected slightly.';
$string['check_debugmsg_comment_developer'] = 'If set other then DEVELOPER, courseenrol may be improved slightly.';
$string['check_debugmsg_details'] = 'There is rarely any advantage in going to Developer level, unless you are a developer, in which case it is strongly recommended.<p>Once you have got the error message, and copied and pasted it somewhere. HIGHLY RECOMMENDED to turn Debug back to NONE. Debug messages can give clues to a hacker as to the setup of your site and may affect courseenrol.</p>';
$string['check_enablestats_comment_disable'] = 'Performance may be affected by statistics processing. If enabled, statistics settings should be set with caution.';
$string['check_enablestats_comment_enable'] = 'Performance may be affected by statistics processing. Statistics settings should be set with caution.';
$string['check_enablestats_details'] = 'Enabling this will process the logs in cronjob and gather some statistics. Depending on the amount of traffic on your site, this can take awhile.<p>During this process, it will consume more server resources and may affect courseenrol.</p>';
$string['check_themedesignermode_comment_enable'] = 'If disabled, images and style sheets are cached, resulting in significant courseenrol improvements.';
$string['check_themedesignermode_comment_disable'] = 'If enabled, images and style sheets will not be cached, resulting in significant courseenrol degradation.';
$string['check_themedesignermode_details'] = 'This is often the cause of slow Moodle sites. <p>On average it might take at least twice the amount of CPU to run a Moodle site with theme designer mode enabled.</p>';
$string['comments'] = 'Comments';
$string['edit'] = 'Edit';
$string['enabled'] = 'Enabled';
$string['disabled'] = 'Disabled';
$string['issue'] = 'Issue';
$string['morehelp'] = 'more help';
$string['courseenrol:view'] = 'View Student wise report';
$string['courseenrol:studentpromote'] = 'Student promotion';
$string['courseenroldesc'] = 'This report lists issues which may affect courseenrol of the site {$a}';
$string['enrol_categoryenrollementdescription'] = 'This method provides the way to cohort users enrolment.';
$string['pluginname'] = 'Student enrolment';
$string['enrol_fieldlocks_help'] = ' ';

$string['enrol_mainrule_fld'] = 'Main template. 1 template per line.';
$string['enrol_secondrule_fld'] = 'Empty field text';
$string['enrol_replace_arr'] = 'Replace array. 1 value per line, format: old_val|new_val';
$string['enrol_delim'] = 'Delimiter';
$string['enrol_delim_help'] = 'Different OS use different delimiters (end of line).<br>In Windows it`s usually CR+LF<br>In Linux - LF<br>etc.<br>If the module does not work, try to change this value.';

$string['enrol_donttouchusers'] = 'Ignore users';
$string['enrol_donttouchusers_help'] = 'Comma separated usernames.';
$string['enrol_enableunenrol'] = 'Enable / Disable automatic unenrol';

$string['enrol_tools_help'] = 'Unenrol function works only with cohorts associated with the module. With <a href="{$a->url}" target="_blank">this tool</a> you can convert / view / delete all cohorts you have.'; 

$string['enrol_cohorttoolcategoryenrollement'] = 'Category enrolment';
$string['enrol_cohortviewmcae'] = 'Enrolled students';

$string['enrol_selectcohort'] = 'Select cohort';

$string['enrol_student'] = 'Student(s)';
$string['enrol_link'] = 'Link';
$string['enrol_studentlink'] = 'View students';
$string['enrol_userprofile'] = 'View profile';
$string['enrol_emptycohort'] = 'Empty cohort';
$string['enrol_cohortusers'] = 'Enrolled students';
$string['enrol_total'] = 'Total';
$string['enrol_cohortname'] = 'Course cohort name';
$string['enrol_coursecategory'] = 'Class';
$string['enrol_update'] = 'Enrol';
$string['enrol_component'] = 'Component';
$string['enrol_count'] = 'Students';
$string['enrol_status'] = 'Status';
$string['enrol_cohortoper_help'] = '<p>Select cohorts you want to convert.</p><p><b>NOTE:</b> <i>You <b>unable</b> to edit converted cohorts manually!</i></p><p>Backup your database!!!</p>';

$string['enrol_profile_help'] = 'Available templates';

$string['enrol_categoryenrollement'] = 'Student enrolment';
$string['enrol_courseInfo'] = 'View course';

$string['enrol_newcourselist'] = 'New courses';

$string['enrol_studentnames'] = 'Student names';

$string['enrol_studentprofilelink'] = 'Profile';
$string['enrol_studentpromotion'] = 'Student promotion';
$string['enrol_classname'] = 'Class';
$string['enrol_studentname'] = 'Student';
$string['enrol_promoteto'] = 'Promote to';
$string['enrol_promote'] = 'Promote';
$string['enrol_statusmessage'] = 'Students and courses are upto date';
$string['enrol_nostudentsandcourses'] = 'No new students and courses are found';
$string['enrol_newstudents'] = 'New student(s) found';
$string['enrol_newcourses'] = 'New course(s) found';
$string['enrol_allstudentsenrolled'] = 'All students are enrolled';
$string['enrol_nonewstudents'] = 'No students to enrol';
$string['enrol_nonewcourses'] = 'No courses to enrol';
$string['enrol_newcourseslist'] = 'Course';
$string['enrol_newstudentslist'] = 'Student';
$string['enrol_enrolstudent'] = 'Student';
$string['enrol_newstudentlist'] = 'Unenrolled students';
$string['enrol_courselink'] = 'View';
