<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once('../../config.php');
require_once('form/cancella.php');


$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Course Creator");
$PAGE->set_heading("Course Creator");
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/local/courseseditor/js/main.js'));

//$PAGE->set_url($CFG->wwwroot.'/local/courseseditor/clona.php');
require_login();

echo $OUTPUT->header();
echo('<h2>' . get_string('delete_page_title', 'local_courseseditor') . '</h2><br><div>');

$form = new FormCancella(new moodle_url($CFG->wwwroot . '/local/courseseditor/resume.php'));
if ($fromform = $form->get_data()) {
    //redirect($nexturl);
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
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="document.getElementById('mform1').submit();"><?php echo(get_string('delete_modal_confirm', 'local_courseseditor')); ?></button>
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
