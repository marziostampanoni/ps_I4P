<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once('../../config.php');
require_once('form/cancella.php');
require_once('class/Richiesta.php');
require_once('class/Corso.php');
require_once('class/UserCorso.php');


$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Course Creator");
$PAGE->set_heading("Course Creator");
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/local/courseseditor/js/main.js'));

//$PAGE->set_url($CFG->wwwroot.'/local/courseseditor/clona.php');
require_login();

echo $OUTPUT->header();
echo('<h2>' . get_string('delete_page_title', 'local_courseseditor') . '</h2><br><div>');

$form = new FormCancella(new moodle_url($CFG->wwwroot . '/local/courseseditor/cancella.php'));
$nexturl = new moodle_url($CFG->wwwroot.'/local/courseseditor/start.php');
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
            $corso->setStatoRichiesta(STATO_RICHIESTA_DA_GESTIRE);
            $corso->setTipoRichiesta(TIPO_RICHIESTA_CANCELLARE);
            $corso->setTitolo($data->title);
            if(count($data->teacher>0)){
                foreach ($data->teacher as $teacher){
                    $user = new UserCorso();
                    $user->setTipoRelazione(TIPO_RELAZIONE_ASSISTENTE);
                    $user->setCognome($teacher->lastname);
                    $user->setNome($teacher->firstname);
                    $user->setIdMdlUser($teacher->id);
                    $corso->addUser($user);
                }
            }
            if(count($data->editingteacher)>0){
                foreach ($data->editingteacher as $idTeacher => $editingteacher){
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
    redirect($nexturl);
}


$form->display();

?>
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
                        onclick="document.getElementById('mform1').submit();"><?php echo(get_string('delete_modal_confirm', 'local_courseseditor')); ?></button>
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
