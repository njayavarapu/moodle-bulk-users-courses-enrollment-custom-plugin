<?php

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/coursecatlib.php');

/**
 * This Class contains functions to get list of enrol users and to enrol users.
 *
 * @package   enrol_courseenrol
 * @copyright 2013 Brahmam
 */
class enrol_courseenrol {

    public $data = array();

    /**
     * This method returns high level course categories
     * @global type $CFG
     * @global type $DB
     * @param type $param
     * @return type array $coursecategories
     * 
     */
    public function enrol_courseenrol_get_course_categories($parent) {
        global $CFG, $DB;

        $sql = 'SELECT cc.id AS categoryid, cc.name AS categoryname FROM {course_categories} AS cc where cc.parent = ? order by sortorder ASC';
        $coursecategories = $DB->get_records_sql($sql, array($parent, $parent));
        return $coursecategories;
    }

    /**
     * This method returns cohort users list by cohortId 
     * @global type $CFG
     * @global type $DB
     * @param type $cid
     * @return type $usersEnrollInfo
     */
    public function enrol_courseenrol_get_cohort_users_list_by_cohort_id($cohortid) {
        global $CFG, $DB;

        $usersEnrollInfo = $DB->count_records('cohort_members', array('cohortid' => $cohortid));
        return $usersEnrollInfo;
    }

    /**
     * This method returns cohort by category Id
     * @global type $CFG
     * @global type $DB
     * @param type $categoryid
     * @return type array $cohorts
     */
    public function enrol_courseenrol_get_cohort_by_category_id($categoryid) {
        global $CFG, $DB;
        $categorycontext = context_coursecat::instance($categoryid);
        $contextId = $categorycontext->id;
        $cohorts = $DB->get_records('cohort', array('contextid' => $contextId));
        return $cohorts;
    }

    /**
     * This method returns category enrolled users list by category Id
     * @global type $CFG
     * @global type $DB
     * @param type $categoryid
     * @return type array by $usersEnrollInfo
     */
    public function enrol_courseenrol_get_enrolled_users_by_category_id($categoryids) {
        global $CFG, $DB;

        $sql = "SELECT mc.id AS courseId, mue.userid AS userId, mcc.parent AS categoryId, mcm.cohortid AS cohortid, COUNT(mc.id) AS coursecount, COUNT(mue.userid) AS userscount 
                FROM mdl_course_categories AS mcc INNER JOIN mdl_course AS mc ON mc.category = mcc.id INNER JOIN mdl_enrol AS me ON me.courseid = mc.id 
                AND me.enrol = 'manual' INNER JOIN mdl_user_enrolments AS mue ON mue.enrolid = me.id INNER JOIN mdl_cohort_members AS mcm 
                ON mcm.userid = mue.userid WHERE mcc.id = ? AND mue.timeend =0 GROUP BY mc.id ";

        $usersEnrollInfo = $DB->get_records_sql($sql, array($categoryids, $categoryids));
        return $usersEnrollInfo;
    }

    /**
     * This method returns end level couces count and count for categories by category Id
     * @global type $CFG
     * @global type $DB
     * @param type $categoryid
     * @return type array $coursesInfo
     */
    public function enrol_courseenrol_get_category_courses_count_by_category_id($categoryid) {
        global $CFG, $DB;

        $courseSql = "SELECT mc.id, COUNT(mc.id) AS coursecount FROM  {course_categories} AS mcc INNER JOIN {course} AS mc ON mc.category = mcc.id 
                      INNER JOIN {enrol} AS me ON me.courseid = mc.id AND me.enrol = 'manual' WHERE mcc.parent = ? OR mcc.id = ? GROUP BY mcc.id";
        $coursesInfo = $DB->get_records_sql($courseSql, array($categoryid, $categoryid));

        return $coursesInfo;
    }

    /**
     * This method returns the cohort members by cohortId
     * @global type $CFG
     * @global type $DB
     * @param type $cohortid
     * @return type
     * 
     */
    public function enrol_courseenrol_get_cohort_members_by_cohort_id($cohortid) {
        global $CFG, $DB;

        $fullname = $DB->sql_fullname($first = 'firstname', $last = 'lastname');
        $sql = "SELECT u.id AS uid, $fullname AS usrname FROM {cohort_members} AS cm JOIN {user} AS u ON u.id = cm.userid WHERE cm.cohortid = ? ORDER BY usrname";
        $userlist = $DB->get_records_sql($sql, array($cohortid));
        return $userlist;
    }

    /**
     * This method returns the category courses by category Id
     * @global type $CFG
     * @global type $DB
     * @param type $categoryid
     * @return type
     */
    public function enrol_courseenrol_get_category_courses_by_category_id($categoryid) {
        global $CFG, $DB;

        $sql = "SELECT me.id AS courseEnrolId, mcc.id AS couseCategoryId, mcc.name AS courseCategoryName, mc.fullname AS coursename, mc.id AS courseid  FROM
                {course_categories} AS mcc JOIN {course} AS mc ON mc.category = mcc.id JOIN {enrol} AS me ON me.courseid = mc.id AND me.enrol = 'manual' 
                WHERE mcc.id = ?";
        $cousesEnrollList = $DB->get_records_sql($sql, array($categoryid, $categoryid));
        return $cousesEnrollList;
    }

    /**
     * This method to enrol the cohort users to category cources
     * @global type $CFG
     * @global type $DB
     * @param type $params
     */
    public function enrol_courseenrol_users_to_category_courses($params) {
        global $CFG, $DB;
        $this->data = array();
        $courseslist = array();
        $courseslist = $this->enrol_courseenrol_get_courses_by_class($params);
        $userlist = $this->enrol_courseenrol_get_cohort_members_by_cohort_id($params['cohortid']);
        $assignstudentroll = $this->enrol_courseenrol_assign_student_role_to_class_enrolled_members($params, $userlist);
        if (count($courseslist) > 0) {
            foreach ($courseslist AS $key => $cousesEnrolList) {
                foreach ($cousesEnrolList AS $key => $cousesEnrol) {
                    foreach ($userlist AS $key => $user) {
                        $sql = "SELECT 'x' FROM {user_enrolments} ue JOIN {enrol} e ON (e.id = ue.enrolid) WHERE ue.userid = :userid AND e.courseid = :courseid AND e.enrol ='manual'";
                        $userid = $user->uid;
                        $courseid = $cousesEnrol->courseid;

                        if (!$DB->record_exists_sql($sql, array('userid' => $userid, 'courseid' => $courseid))) {
                            $user_enrolment_data['status'] = 0;
                            $user_enrolment_data['enrolid'] = $cousesEnrol->courseenrolid;
                            $user_enrolment_data['userid'] = $user->uid;
                            $user_enrolment_data['timestart'] = time();
                            $user_enrolment_data['timeend'] = 0;
                            $user_enrolment_data['timecreated'] = time();
                            $user_enrolment_data['timemodified'] = time();
                            $DB->insert_record('user_enrolments', $user_enrolment_data);
                        }
                    }
                }
            }
        }
    }

    /**
     * This metod used to assiging the student role to cohort members.
     * @global type $CFG
     * @global type $DB
     * @global type $USER
     * @param type $params
     * @param type $userlist
     */
    public function enrol_courseenrol_assign_student_role_to_class_enrolled_members($params, $userlist) {
        global $CFG, $DB, $USER;
        $categorycontext = context_coursecat::instance($params['categoryid']);
        $contextId = $categorycontext->id;
        $roleid = $this->enrol_courseenrol_get_student_role_id();
        foreach ($userlist AS $key => $student) {
            if (!$DB->get_record('role_assignments', array('contextid' => $contextId, 'userid' => $student->uid))) {
                $roleassignment = new stdClass;
                $roleassignment->roleid = $roleid;
                $roleassignment->contextid = $contextId;
                $roleassignment->userid = $student->uid;
                $roleassignment->timemodified = time();
                $roleassignment->modifierid = $USER->id;
                $roleassignment->itemid = 0;
                $DB->insert_record('role_assignments', $roleassignment);
            }
        }
    }

    /**
     * This metod used to assiging the student role to promote_student.
     * @global type $CFG
     * @global type $DB
     * @global type $USER
     */
    public function enrol_courseenrol_assign_student_role_to_class_promote_student($params) {
        global $CFG, $DB, $USER;
        $categorycontext = context_coursecat::instance($params['categoryid']);
        $contextId = $categorycontext->id;
        $userids = $params['studentids'];
        $roleid = $this->enrol_courseenrol_get_student_role_id();
        foreach ($userids AS $userid) {
            if (!$DB->get_record('role_assignments', array('contextid' => $contextId, 'userid' => $userid))) {
                $roleassignment = new stdClass;
                $roleassignment->roleid = $roleid;
                $roleassignment->contextid = $contextId;
                $roleassignment->userid = $userid;
                $roleassignment->timemodified = time();
                $roleassignment->modifierid = $USER->id;
                $roleassignment->itemid = 0;
                $DB->insert_record('role_assignments', $roleassignment);
            }
        }
    }

    public function enrol_courseenrol_get_student_role_id() {
        global $CFG, $DB, $USER;
        $roles = $DB->get_record('role', array('shortname' => 'student'));
        return $roles->id;
    }

    /**
     * This method usid to get the courses list by classid
     * @global type $CFG
     * @global type $DB
     * @param type $params
     * @return type
     */
    public function enrol_courseenrol_get_courses_by_class($params) {
        global $CFG, $DB;
        $courseslist = array();
        $catgorylist = array();
        $dataarray = array();
        $this->data = array();
        $catgorylist = $this->enrol_courseenrol_get_subjects_by_class($params['categoryid']);
        foreach ($catgorylist AS $key => $subcategory) {
            $dataarray = coursecat::make_categories_list('moodle/category:manage', 0);
            if ($key != $params['categoryid']) {
                $catgorylist[$key] = $dataarray[$key];
                $courseslist[$key] = $this->enrol_courseenrol_get_category_courses_by_category_id($key);
            }
        }
        return $courseslist;
    }

    /**
     * This method returns sucategory list by parent category id
     * @global type $CFG
     * @global type $DB
     * @param type $categoryid
     * @return type
     * 
     */
    public function enrol_courseenrol_get_subjects_by_class($categoryid) {
        global $CFG, $DB;
        $catgorylist = array();
        $coursecat = coursecat::get($categoryid);
        $categorylist = $this->enrol_courseenrol_get_class_subject_list_by_class_id($coursecat);
        return $this->data;
    }

    /**
     * This method returns the category wise users course enrolled information 
     * @global type $CFG
     * @global type $DB
     * @param type $param
     * @return type
     */
    public function enrol_courseenrol_get_category_users_enrolment_info($param) {
        global $CFG, $DB;

        $coursecategories = $this->enrol_courseenrol_get_course_categories($param);

        $category = array();
        $cohorts_list = array();

        foreach ($coursecategories AS $coursecategory) {
            $this->data = array();
            $categorycontext = context_coursecat::instance($coursecategory->categoryid);
            $categoryid = $coursecategory->categoryid;
            $contextId = $categorycontext->id;
            $cohorts = $this->enrol_courseenrol_get_cohort_by_category_id($categoryid);
            foreach ($cohorts as $cohort) {
                $cid = $cohort->id;
                $cname = format_string($cohort->name);
                $cohorts_list[$cid]['name'] = $cname;
                $cohorts_list[$cid]['component'] = $cohort->component;
                $cohorts_list[$cid]['categoryName'] = $coursecategory->categoryname;
                $cohorts_list[$cid]['categoryId'] = $categoryid;
                $cohorts_list[$cid]['studentscount'] = $this->enrol_courseenrol_get_cohort_users_list_by_cohort_id($cid);
                /* $subcategories = $this->enrol_courseenrol_get_subjects_by_class($categoryid);
                  $usersEnrollInfo = array();
                  $enrolledUsersCount = 0;
                  $usersEnrolledCourseCount = 0;
                  $studentEnrolledCourcesCount = 0;

                  foreach ($subcategories AS $key => $subcategory) {
                  if ($key != $categoryid) {
                  $usersEnrollInfo = $this->enrol_courseenrol_get_enrolled_users_by_category_id($key);
                  foreach ($usersEnrollInfo as $usersEnroll) {

                  $enrolledUsersCount = $enrolledUsersCount + $usersEnroll->userscount;
                  $usersEnrolledCourseCount = $usersEnrolledCourseCount + $usersEnroll->coursecount;
                  $studentEnrolledCourcesCount = $usersEnroll->coursecount;
                  }
                  }
                  }

                  $cohorts_list[$cid]['studentEnrolledTotalCourcesCount'] = $studentEnrolledCourcesCount;
                  $courseCount = 0;
                  $courseslist = $this->enrol_courseenrol_get_courses_by_class(array('categoryid' => $categoryid));
                  foreach ($courseslist as $courses) {
                  if (count($courses) > 0) {
                  $courseCount = $courseCount + count($courses);
                  }
                  }
                  $cohorts_list[$cid]['enrollCount'] = $enrolledUsersCount / $courseCount;
                  $cohorts_list[$cid]['enrollCourseCount'] = $usersEnrolledCourseCount;
                  $cohorts_list[$cid]['courseCount'] = $courseCount * $cohorts_list[$cid]['count'];
                  $cohorts_list[$cid]['totalCourses'] = $courseCount;
                  $cohorts_list[$cid]['totaluserscousewise'] = ($courseCount != 0) ? ($courseCount * $cohorts_list[$cid]['count']) : $cohorts_list[$cid]['count'];
                 */
            }
        }

        return $cohorts_list;
    }

    /**
     * This method returns the new courses created in category.
     * @global type $CFG
     * @global type $DB
     * @param type $categoryid
     * @return type
     */
//    public function enrol_courseenrol_get_new_courses_by_category_id($categoryid) {
//        global $CFG, $DB;
//
//        $sql = "SELECT mc.id AS courseid,mc.fullname AS cousename, mue.userid AS userId, mcc.parent AS categoryId, mcm.cohortid AS cohortid FROM  mdl_course_categories AS mcc 
//                INNER JOIN mdl_course AS mc ON mc.category = mcc.id INNER JOIN mdl_enrol AS me ON me.courseid = mc.id AND me.enrol = 'manual' 
//                LEFT JOIN mdl_user_enrolments AS mue ON mue.enrolid = me.id LEFT JOIN mdl_cohort_members AS mcm ON mcm.userid = mue.userid 
//                WHERE mcc.id = ?  GROUP BY mc.id";
//
//        $newCoursesList = $DB->get_records_sql($sql, array($categoryid, $categoryid));
//        return $newCoursesList;
//    }

    /**
     * This method returns the new students in cohort
     * @global type $CFG
     * @global type $DB
     * @param type $cohortId
     * @return type
     */
    public function enrol_courseenrol_get_new_students_by_cohort_id($cohortId, $categoryId) {
        global $CFG, $DB;
        $params['cohortid'] = $cohortId;
        $params['categoryid'] = $categoryId;
        $enrolids = array();
        $newstudentslist = array();
        $subjectslist = $this->enrol_courseenrol_get_courses_by_class($params);
        foreach ($subjectslist as $key => $courses) {
            foreach ($courses AS $key => $course) {
                $enrolids[] = $key;
            }
        }

        $enrolidlist = implode(",", $enrolids);
        $userlist = $this->enrol_courseenrol_get_cohort_members_by_cohort_id($cohortId);
        if (!empty($enrolidlist) && (strlen($enrolidlist) > 0)) {
            foreach ($userlist AS $key => $user) {
                $userid = $user->uid;
                $sql = "SELECT mue.userid FROM  mdl_user_enrolments AS mue WHERE mue.userid = ?";

                if (!empty($enrolidlist) && strlen($enrolidlist) > 0) {
                    $sql .= "AND mue.enrolid IN($enrolidlist)";
                }

                if (!$DB->get_record_sql($sql, array($userid))) {
                    $newstudentslist[] = $user;
                }
            }
        }else {
            foreach ($userlist AS $key => $user) {
                 $newstudentslist[] = $user;
            }
        }
        return $newstudentslist;
    }

    /**
     * This method returns the enrolled students list
     * @global type $CFG
     * @global type $DB
     * @param type $cohortId
     * @param type $categoryId
     * @return type
     */
    public function enrol_courseenrol_get_enroled_students($cohortId, $categoryId) {
        global $CFG, $DB;
        $params['cohortid'] = $cohortId;
        $params['categoryid'] = $categoryId;
        $enrolids = array();
        $enroledstudentslist = array();
        $subjectslist = $this->enrol_courseenrol_get_courses_by_class($params);
        foreach ($subjectslist as $key => $courses) {
            foreach ($courses AS $key => $course) {
                $enrolids[] = $key;
            }
        }

        $enrolidlist = implode(",", $enrolids);
        if (!empty($enrolidlist) && strlen($enrolidlist) > 0) {
            $userlist = $this->enrol_courseenrol_get_cohort_members_by_cohort_id($cohortId);
            foreach ($userlist AS $key => $user) {
                $userid = $user->uid;
                $sql = "SELECT mue.userid FROM  mdl_user_enrolments AS mue WHERE mue.userid = ? AND mue.enrolid IN($enrolidlist)";

                if ($DB->get_record_sql($sql, array($userid))) {
                    $enroledstudentslist[] = $user;
                }
            }
        }
        return $enroledstudentslist;
    }

    /**
     * This method returns the new Courses in class
     * @global type $CFG
     * @global type $DB
     * @param type $cohortId
     * @return type
     */
    public function enrol_courseenrol_get_new_courses($cohortId, $categoryId) {
        global $CFG, $DB;
        $this->data = array();
        $params['cohortid'] = $cohortId;
        $params['categoryid'] = $categoryId;
        $enrolids = array();
        $newcourseslist = array();
        $subjectslist = $this->enrol_courseenrol_get_courses_by_class($params);
        foreach ($subjectslist as $key => $courses) {
            foreach ($courses AS $key => $course) {
                $enrolids[] = $key;
                $enrolid = $key;
                $userlist = $this->enrol_courseenrol_get_cohort_members_by_cohort_id($cohortId);
                if (count($userlist) > 0) {
                    foreach ($userlist AS $key => $user) {
                        $userid = $user->uid;
                        $sql = "SELECT mue.userid FROM  mdl_user_enrolments AS mue WHERE mue.userid = ? AND mue.enrolid = $enrolid";
                        if (!$DB->get_record_sql($sql, array($userid))) {
                            // $newcourseslist[$course->courseid] = array('classname'=>$course->coursename, 'userid'=>$userid, 'enrolid'=>$enrolid);
                            $newcourseslist[$course->courseid] = $course->coursename;
                        }
                    }
                } else {
                    $newcourseslist[$course->courseid] = $course->coursename;
                }
            }
        }

        return arraY('newcourses' => $newcourseslist, 'totalcourses' => count($enrolids));
    }

    /**
     * 
     * @global type $CFG
     * @global type $DB
     * @param type $categoryid
     * @return type
     * 
     */
    public function enrol_courseenrol_get_class_by_category_id($categoryid) {
        global $CFG, $DB;

        $sql = 'SELECT cc.id AS categoryid, cc.name AS categoryname FROM {course_categories} AS cc where cc.id = ?';
        $class = $DB->get_record_sql($sql, array($categoryid));
        return $class;
    }

    /**
     * This method returns the nth level category list
     * @global type $OUTPUT
     * @param coursecat $category
     * @param type $depth
     * @param type $up
     * @param type $down
     */
    public function enrol_courseenrol_get_class_subject_list_by_class_id(coursecat $category, $depth = -1, $up = false, $down = false) {
        global $OUTPUT;
        $cat = array();
        if ($category->id) {
            $categoryname = $category->get_formatted_name();
            $this->data[$category->id] = array('categoryname' => $categoryname, 'parent' => $category->parent);
        }
        if ($categories = $category->get_children()) {
            // Print all the children recursively.
            $countcats = count($categories);
            $count = 0;
            $first = true;
            $last = false;
            foreach ($categories as $cat) {
                $count++;
                if ($count == $countcats) {
                    $last = true;
                }
                $up = $first ? false : true;
                $down = $last ? false : true;
                $first = false;

                $this->enrol_courseenrol_get_class_subject_list_by_class_id($cat, $depth + 1, $up, $down);
            }
        }
    }

    /**
     * 
     * @global type $CFG
     * @global type $DB
     * @param type $categoryId
     * @return type
     */
    public function enrol_courseenrol_get_students_list_by_class($categoryId) {
        global $CFG, $DB;
        $categorycontext = context_coursecat::instance($categoryId);
        $contextId = $categorycontext->id;

        $sql = 'SELECT mu.id AS userid, mu.firstname as firstname, mu.lastname as lastname, mu.id as cohortid FROM mdl_cohort AS mc INNER JOIN mdl_cohort_members AS mcm ON mcm.cohortid = mc.id 
                INNER JOIN mdl_user AS mu ON mu.id = mcm.userid WHERE mc.contextid = ? ';
        $studentslist = $DB->get_records_sql($sql, array($contextId));
        return $studentslist;
    }

    /**
     * This method to enrol the cohort users to category cources
     * @global type $CFG
     * @global type $DB
     * @param type $params
     */
    public function enrol_courseenrol_promote_student_to_class($params) {
        global $CFG, $DB, $USER;
        $this->data = array();
        $courseslist = array();
        $courseslist = $this->enrol_courseenrol_get_courses_by_class($params);
        $aasignstudentrole = $this->enrol_courseenrol_assign_student_role_to_class_promote_student($params);
        if (count($courseslist) > 0) {
            foreach ($courseslist AS $key => $cousesEnrolList) {
                foreach ($cousesEnrolList AS $key => $cousesEnrol) {

                    $userids = $params['studentids'];
                    $courseid = $cousesEnrol->courseid;
                    foreach ($userids AS $userid) {
                        $sql = "SELECT 'x' FROM {user_enrolments} ue JOIN {enrol} e ON (e.id = ue.enrolid) WHERE ue.userid = :userid AND e.courseid = :courseid AND e.enrol ='manual'";
                        if (!$DB->record_exists_sql($sql, array('userid' => $userid, 'courseid' => $courseid))) {
                            $user_enrolment_data['status'] = 0;
                            $user_enrolment_data['enrolid'] = $cousesEnrol->courseenrolid;
                            $user_enrolment_data['userid'] = $userid;
                            $user_enrolment_data['timestart'] = time();
                            $user_enrolment_data['timeend'] = 0;
                            $user_enrolment_data['timecreated'] = time();
                            $user_enrolment_data['timemodified'] = time();
                            $DB->insert_record('user_enrolments', $user_enrolment_data);
                        } else {
                            $updated = $this->enrol_courseenrol_enable_student_access_to_class($cousesEnrol->courseenrolid, $userid);
                        }
                    }
                }
            }
        }
    }

    /**
     * This method to enrol the cohort users to category cources
     * @global type $CFG
     * @global type $DB
     * @param type $params
     */
    public function enrol_courseenrol_disable_student_access_to_class($params) {
        global $CFG, $DB, $USER;
        $courseslist = array();
        $updated = false;
        $userids = $params['studentids'];
        $courseslist = $this->enrol_courseenrol_get_courses_by_class($params);
        foreach ($userids AS $userid) {

            $cohortmember = $DB->get_record('cohort_members', array('cohortid' => $params['cohortid'], 'userid' => $userid));
            $cohortmember->cohortid = $params['newcohortid'];
            $cohortmember->userid = $userid;
            $cohortmember->timeadded = time();
            $DB->update_record('cohort_members', $cohortmember);
            if (count($courseslist) > 0) {
                foreach ($courseslist AS $key => $cousesEnrolList) {
                    foreach ($cousesEnrolList AS $key => $cousesEnrol) {
                        $enrolid = $cousesEnrol->courseenrolid;
                        $timemodified = time();
                        $updated = $this->enrol_courseenrol_enable_student_access_to_class($enrolid, $userid, $timemodified);
                    }
                }
            }
        }

        return $updated;
    }

    public function enrol_courseenrol_enable_student_access_to_class($enrolid, $userid, $timemodified = 0) {
        global $CFG, $DB, $USER;
        $updated = false;
        if ($ue = $DB->get_record('user_enrolments', array('enrolid' => $enrolid, 'userid' => $userid))) {
            $ue->timeend = $timemodified;
            $ue->modifierid = $USER->id;
            $ue->timemodified = $timemodified;
            $DB->update_record('user_enrolments', $ue);
            $updated = true;
        }

        return $updated;
    }

}
