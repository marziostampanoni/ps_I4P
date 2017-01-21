<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once('../../config.php');
require_once('form/cancella.php');
require_once('form/selectcat.php');
require_once('class/Richiesta.php');
require_once('class/Corso.php');
require_once('class/UserCorso.php');

global $PAGE, $DB, $USER;

$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Course Creator");
$PAGE->set_heading("Course Creator");
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/local/courseseditor/js/main.js'));


echo $OUTPUT->header();
echo('<h2>' . get_string('delete_page_title', 'local_courseseditor') . '</h2><hr><br><div>');


$query = "
SELECT *  FROM
mdl_user u
JOIN mdl_role_assignments ra ON ra.userid = u.id
JOIN mdl_role r ON ra.roleid = r.id
JOIN mdl_context con ON ra.contextid = con.id
JOIN mdl_course c ON c.id = con.instanceid AND con.contextlevel = 50
WHERE u.id=? AND (r.shortname = 'teacher' OR r.shortname = 'editingteacher' OR r.shortname = 'manager')";

// get all courses related to $USER
$courses = $DB->get_records_sql($query, array($USER->id));

$categories = $DB->get_records('course_categories', array());


// create Category filter select and apply filter selection by trim courses to display - it looks also for all subtree
$filter = new FormSelectCat();
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


$req = $DB->get_records('lcl_courseseditor_richiesta', array('id_mdl_user' => $USER->id));
foreach ($req as $idReq => $request) {
    $cond = array('id_lcl_courseseditor_richiesta' => $idReq, 'tipo_richiesta' => 'Cancellare', 'stato_richiesta' => STATO_RICHIESTA_DA_GESTIRE);
    $alredyRequested = $DB->get_records('lcl_courseseditor_corso', $cond);
    if (isset($alredyRequested)) {
        unset($courses[$alredyRequested[$idReq]->id_mdl_course]);
    }
}


// create checkbox form form courses to delete
$form = new FormCancella(new moodle_url($CFG->wwwroot . '/local/courseseditor/cancella.php'), array('courses' => $courses), 'post', '', array('id' => 'deleteForm'));

// if result create new request in $DB and redirect to start
if ($fromform = $form->get_data()) {
    $request = new Richiesta();
    $request->setIdMdlUser($USER->id);
    $request->setDataRichiesta(date('Y-m-d H:i:s'));
    foreach ($fromform as $name => $post) {
        $prefix = substr($name, 0, 4);
        if ($prefix == 'name') {
            $id = substr($name, strpos($name, "-") + 1);
            $datasel = 'data-' . $id;
            $data = json_decode($fromform->$datasel);
            $corso = new Corso();
            $corso->setIdMdlCourseCategories($data->cat);
            $corso->setShortname('SOMSHRTNM');
            $corso->setIdMdlCourse($data->id_mdl_course);
            $corso->setStatoRichiesta(STATO_RICHIESTA_DA_GESTIRE);
            $corso->setTipoRichiesta(TIPO_RICHIESTA_CANCELLARE);
            $corso->setTitolo($data->title);
            if (count($data->teachers > 0)) {
                foreach ($data->teachers as $teacher) {
                    $user = new UserCorso();
                    $user->setTipoRelazione(TIPO_RELAZIONE_ASSISTENTE);
                    $user->setCognome($teacher->lastname);
                    $user->setNome($teacher->firstname);
                    $user->setIdMdlUser($teacher->id);
                    $corso->addUser($user);
                }
            }
            if (count($data->editingteacher) > 0) {
                foreach ($data->editingteacher as $idTeacher => $editingteacher) {
                    $user = new UserCorso();
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
    $request->saveToDB();
    redirect(new moodle_url($CFG->wwwroot . '/local/courseseditor/manage.php'));
}
$filter->display();
$form->display();

?>

<!-- Modal for ask confirmation on deletion -->
<div id="deleteModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo(get_string('delete_courses', 'local_courseseditor')); ?></h4>
            </div>
            <div class="modal-body" id="modal-body">
                <p><?php echo(get_string('delete_modal_body', 'local_courseseditor')); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo(get_string('delete_modal_cancel', 'local_courseseditor')); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"
                        onclick="document.getElementById('deleteForm').submit();"><?php echo(get_string('delete_modal_confirm', 'local_courseseditor')); ?></button>
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
