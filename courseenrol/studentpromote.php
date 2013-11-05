<?php
require_once('../../config.php');
require_once($CFG->dirroot . '/enrol/courseenrol/locallib.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/pagelib.php');
global $PAGE;
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/report/studentreports/jquery-1.9.1.js'));
require_login();

admin_externalpage_setup('courseenrol');

$classesid = optional_param('classid', 0, PARAM_INT);
$studentid = optional_param('studentid', 0, PARAM_INT);
$promoteclassid = optional_param('promoteclassid', 0, PARAM_INT);
$msg = optional_param('msg', null, PARAM_TEXT);
$optionHtml = '';
$courseenrol = new enrol_courseenrol();
$classoptions = array();
$promoteclassoptions = array();
$studentoptions = array();
$parent = 0;
$classtypes = $courseenrol->enrol_courseenrol_get_course_categories($parent);

foreach ($classtypes as $key => $classtype) {
    $classoptions[$key] = $classtype->categoryname;
    if ($classesid != $key) {
        $promoteclassoptions[$key] = $classtype->categoryname;
    }
}

//  if ($classesid != 0) {
//    $studentslist = $courseenrol->enrol_courseenrol_get_students_list_by_class($classesid);
//    
//    foreach ($studentslist as $student) {
//        $studentoptions[$student->userid] = $student->firstname . ' ' . $student->lastname;
//        $optionHtml .= '<option value="'.$student->userid.'">'.$student->firstname . ' ' . $student->lastname.'</option>';
//    }
//} else if ($classesid == 0) {
//    $promoteclassoptions = array();
//}  
if ($classesid == 0) {
    $promoteclassoptions = array();
}


$url = new moodle_url('/enrol/courseenrol/studentpromote.php');

$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_title('Student Promotion');
$PAGE->set_heading('Student Promotion');
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('enrol_studentpromotion', 'enrol_courseenrol'));

//$attributes = array('onchange' => 'this.form.submit();');
$formpost = new moodle_url('/enrol/courseenrol/studentpromote.php');
if (($classesid != 0) && ($studentid != 0 ) && ($promoteclassid != 0)) {
    $formpost = new moodle_url('/enrol/courseenrol/promote.php');
}
$message = '';
if ($msg == 'success') {
    $message = 'Students promoted successfully.';
} else if ($msg == 'error') {
    $message = 'Students not promoted, Please try again.';
}

$html = '';
if ($message != '') {
    $html .= '<span style="color:red;margin-left:140px;font-weight:bold;">' . $message . '</span><br>';
}
$html .= '<form class="studentexamsselectform" id="promote-form" action="" method="post">' . "\t\t\t\t";
$html .= '<table cellpadding="10" cellspacing="0" width="30%" >';
$html .= '<tr><td align="right">';
$html .= '<label for="menuinstanceid">' . get_string('enrol_classname', 'enrol_courseenrol') . ':</label>' . "\t\t\t\t\t\t\t\t";
$html .= '</td><td align="left">';
$html .= html_writer::select($classoptions, 'classid', $classesid, array('' => 'Choose'), $attributes) . "\t\t\t\t";
$html .= '</td></tr><tr><td  align="right">';
$html .= '<label for="menuinstanceid">' . get_string('enrol_studentname', 'enrol_courseenrol') . ':</label>' . "\t\t\t\t\t\t\t\t";
$html .= '</td><td align="left">';

$html .= '<div id="LoadingImage" style="display: none"><img src="' . $CFG->wwwroot . '/enrol/courseenrol/bx_loader.gif" alt="pageloading"></div>';
$html .= '<select name="studentid[]" id="studentid" multiple="multiple" size="10" style="width:150px;"><option value="">Select student</option>';
$html .= $optionHtml;
$html .= '</select>';
//$html .= html_writer::select($studentoptions, 'studentid', $studentid, array('' => 'Choose...', ), $attributes) . "\t\t\t\t";
$html .= '</td></tr><tr><td  align="right">';
$html .= '<label for="menuinstanceid">' . get_string('enrol_promoteto', 'enrol_courseenrol') . ':</label>' . "\t\t\t\t";
$html .= '</td><td align="left">';
$html .= html_writer::select($promoteclassoptions, 'promoteclassid', $promoteclassid, array('' => 'Choose'), $attributes) . "\t\t\t\t\t\t\t\t";

$html .= '</td></tr>';
$html .= '<tr><td colspan="2"  align="center">';
$html .= '<input type="submit" id="studentpromote" value="' . get_string('enrol_promote', 'enrol_courseenrol') . '" />' . "\t\n";
$html .= '</td></tr>';
$html .= '</table></form>';

$return = new moodle_url('/enrol/courseenrol/index.php');
//$html .= '<div ><a href="' . $return . '"><button>Back</button></a></div> ';
//$html .= '<div id="chart_div" style="width: 900px; height: 500px;"></div> ';
echo $html;

//echo '<a href="' . $return . '"><button><< Back</button></a>';
echo $OUTPUT->footer();
$subjecturl = new moodle_url($CFG->wwwroot . '/enrol/courseenrol/studentslist.php');
?>
<script type="text/javascript">
    $(function() {
        //$('#LoadingImage').hide();
        $('select[id="menuclassid"]').on("change", function() {
            var categoryid = $('select[id="menuclassid"] option:selected').val();
            if (categoryid == "") {
                return false;
            }
            $('#LoadingImage').show();
            $('#studentid').find('option').remove();
            $('#menupromoteclassid').find('option').remove();

            $.post("<?php echo $subjecturl; ?>",
                    {
                        categoryid: categoryid,
                    }, function(data, status) {
                $('#LoadingImage').hide();
                if (data.length > 0) {
                    var obj = $.parseJSON(data);
                    var shtml = '';
                    if (obj.studentoptions.length > 0) {
                        $.each(obj.studentoptions, function(index, student) {
                            shtml += '<option value="' + student.studentid + '">' + student.studentname + '</option>';
                        });
                        $('#studentid').find('option').remove().end().append(shtml);
                    }else{
                        shtml += '<option value="">Select student</option>';
                        $('#studentid').find('option').remove().end().append(shtml);
                    }
                    var chtml
                    if (obj.promoteclassoptions.length > 0) {
                        chtml += '<option value="">Choose</option>';
                        $.each(obj.promoteclassoptions, function(index, promoteclass) {
                            chtml += '<option value="' + promoteclass.promoteclassid + '">' + promoteclass.promoteclassname + '</option>';
                        });
                        $('#menupromoteclassid').html(chtml);
                    }
                }

            });

        });

//        $('select[id="menuclassid"] option:selected').change(function() {
//
//            var action = '<?php echo $formpost = new moodle_url('/enrol/courseenrol/studentpromote.php'); ?>';
//            $("#promote-form").attr("action", action);
//        });
//        $('select[id="menustudentid"] option:selected').change(function() {
//            var action = '<?php echo $formpost = new moodle_url('/enrol/courseenrol/studentpromote.php'); ?>';
//            $("#promote-form").attr("action", action);
//        });
//        $('select[id="menupromoteclassid"] option:selected').change(function() {
//            var action = '<?php echo $formpost = new moodle_url('/enrol/courseenrol/studentpromote.php'); ?>';
//            $("#promote-form").attr("action", action);
//        });


        $('#studentpromote').click(function() {

            var classid = $('select[id="menuclassid"] option:selected').val();
            if (classid == "") {
                alert('Please select class');
                return false;
            }
            var studentid = $('select[name="studentid[]"] option:selected');
            if (studentid.length == 0 || studentid.val() == '') {
                alert('Please select student(s)');
                return false;
            }
            var promoteclassid = $('select[id="menupromoteclassid"] option:selected').val();
            if (promoteclassid == "") {
                alert('Please select promote class');
                return false;
            }
            if (studentid.val() == 1) {
                var r = confirm("Are you sure to promote this student?");
            } else {
                var r = confirm("Are you sure to promote these students?");
            }
            if (r == true) {
                var action = '<?php echo $formpost = new moodle_url('/enrol/courseenrol/promote.php'); ?>';
                $("#promote-form").attr("action", action);
                return true;
            } else {
                return false;
            }
            return true;
        });
    });
</script>