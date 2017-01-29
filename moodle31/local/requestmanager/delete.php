<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once('include_all.php');

global $PAGE, $DB, $USER;

$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Course Creator");
$PAGE->set_heading("Course Creator");
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/local/requestmanager/js/main.js'));


echo $OUTPUT->header();
echo('<h2>' . get_string('delete_page_title', 'local_requestmanager') . '</h2><hr><br><div>');


$query = "
SELECT *  FROM
mdl_user u
JOIN mdl_role_assignments ra ON ra.userid = u.id
JOIN mdl_role r ON ra.roleid = r.id
JOIN mdl_context con ON ra.contextid = con.id
JOIN mdl_course c ON c.id = con.instanceid
WHERE u.id=? AND (r.shortname = 'teacher' OR r.shortname = 'editingteacher' OR r.shortname = 'manager')";

// get all courses related to $USER
$courses = $DB->get_records_sql($query, array($USER->id));

if($courses && count($courses)>0) {
    // hide courses that already sent a request
    $req = $DB->get_records('requestmanager_richiesta', array('id_mdl_user' => $USER->id));
    foreach ($req as $idReq => $request) {
        $cond = array('id_requestmanager_richiesta' => $idReq, 'tipo_richiesta' => TIPO_RICHIESTA_CANCELLARE, 'stato_richiesta' => STATO_RICHIESTA_DA_GESTIRE);
        $alredyRequested = $DB->get_records('requestmanager_corso', $cond);
        if (count($alredyRequested)>0) {
            $c=array_shift($alredyRequested);
            unset($courses[$c->id_mdl_course]);
        }
    }

    //categories where the user has courses
    $cat_with_courses = array();
    foreach ($courses as $course) {
        if (!in_array($course->category, $cat_with_courses)) {
            $cat_with_courses[] = $course->category;
        }
    }


    // create Category filter select and apply filter selection by trim courses to display - it looks also for all subtree
    $filter = new FormSelectCat(null, array('cats' => $cat_with_courses));
    if ($fromform = $filter->get_data()) {
        if (isset($fromform->cat) && $fromform->cat > 0) {
            $subCat = array();
            foreach (coursecat::get($fromform->cat)->get_courses(array('recursive' => true)) as $c) {
                $subCat[] = $c->category;
            }
            $subCat = array_unique($subCat);
            foreach ($courses as $id => $course) {
                if (!in_array($course->category, $subCat)) {
                    unset($courses[$id]);
                }
            }
        }
    }


    $cat_with_courses = array();
    foreach ($courses as $course) {
        if (!in_array($course->category, $cat_with_courses)) {
            $cat_with_courses[] = $course->category;
        }
    }

    // create checkbox form form courses to delete
    $form = new FormCancella(new moodle_url($CFG->wwwroot . '/local/requestmanager/delete.php'), array('courses' => $courses, 'cats' => $cat_with_courses), 'post', '', array('id' => 'deleteForm'));

    // if result create new request in $DB and redirect to start
    if ($fromform = $form->get_data()) {
        if($_SESSION['just_saved']){
            redirect(new moodle_url($CFG->wwwroot . '/local/requestmanager/start.php'));
        }
        $request = new local_requestmanager\Richiesta();
        $request->setIdMdlUser($USER->id);
        $request->setDataRichiesta(date('Y-m-d H:i:s'));
        foreach ($fromform as $name => $post) {
            $prefix = substr($name, 0, 4);
            if ($prefix == 'name') {
                $id = substr($name, strpos($name, "-") + 1);
                $datasel = 'data-' . $id;
                $data = json_decode($fromform->$datasel);
                $corso = new local_requestmanager\Corso();
                $corso->setIdMdlCourseCategories($data->cat);
                $corso->setShortname('SOMSHRTNM');
                $corso->setIdMdlCourse($data->id_mdl_course);
                $corso->setStatoRichiesta(STATO_RICHIESTA_DA_GESTIRE);
                $corso->setTipoRichiesta(TIPO_RICHIESTA_CANCELLARE);
                $corso->setTitolo($data->title);
                if (count($data->teachers > 0)) {
                    foreach ($data->teachers as $teacher) {
                        $user = new local_requestmanager\UserCorso();
                        $user->setTipoRelazione(TIPO_RELAZIONE_ASSISTENTE);
                        $user->setCognome($teacher->lastname);
                        $user->setNome($teacher->firstname);
                        $user->setIdMdlUser($teacher->id);
                        $corso->addUser($user);
                    }
                }
                if (count($data->editingteacher) > 0) {
                    foreach ($data->editingteacher as $idTeacher => $editingteacher) {
                        $user = new local_requestmanager\UserCorso();
                        $user->setTipoRelazione(TIPO_RELAZIONE_DOCENTE);
                        $user->setCognome($editingteacher->lastname);
                        $user->setNome($editingteacher->firstname);
                        $user->setIdMdlUser($editingteacher->id);
                        $corso->addUser($user);
                    }
                }
                $request->addCorso($corso);
            }
        }

        if ($request->saveToDB()) {
            $_SESSION['just_saved']=true;

//            if(get_config('local_requestmanager','notify_manager_by_mail')==1){
//                foreach ($id_cats_to_notify as $id_cat){
//                    if(local_requestmanager\CEUtil::mailNotificationToManager($id_cat));
//                }
//            }
            echo '<div class="alert alert-success">
                    ' . get_string('delete_page_success', 'local_requestmanager') . '
                </div>';
        } else {
            echo '<div class="alert alert-danger">
                    ' . get_string('delete_page_error', 'local_requestmanager') . '
                </div>';
        }
        echo '<br><a type="button" class="btn btn-primary btn-lg" href="manage.php">' . get_string('manage_courses', 'local_requestmanager') . '</a>';
        echo '  <a type="button" class="btn btn-info btn-lg" href="start.php">' . get_string('pluginname', 'local_requestmanager') . '</a>';
    }else{
        $_SESSION['just_saved']=false;
        $filter->display();
        $form->display();
    }
}else {
    echo '<div class="alert alert-warning">
                    ' . get_string('no_courses_to_delete', 'local_requestmanager') . '
                </div>';

}
?>

<!-- Modal for ask confirmation on deletion -->
<div id="deleteModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo(get_string('delete_courses', 'local_requestmanager')); ?></h4>
            </div>
            <div class="modal-body" id="modal-body">
                <p><?php echo(get_string('delete_modal_body', 'local_requestmanager')); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo(get_string('delete_modal_cancel', 'local_requestmanager')); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"
                        onclick="document.getElementById('deleteForm').submit();"><?php echo(get_string('delete_modal_confirm', 'local_requestmanager')); ?></button>
            </div>
        </div>
    </div>
</div>

<?php
echo('</div>');
echo $OUTPUT->footer();
?>

<script>

</script>
