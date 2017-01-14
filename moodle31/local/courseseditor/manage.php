<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once('../../config.php');
require_once('mainchoiceform.php');


$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Course manager");
$PAGE->set_heading("Course manager");
//$PAGE->set_url($CFG->wwwroot.'/local/courseseditor/manage.php');
require_login();

if(!has_capability('local/courseseditor:manage',context_user::instance($USER->id),$USER->id,TRUE)){
    echo "Non hai i diritti per vedere questa pagina";
    return;
}

echo $OUTPUT->header();
echo('<h2>Manage requests</h2><br><div>');
$form = new mainchoiceform(); //puoi passare l'action del form come parametro in costruzione.ai
if ($fromform = $form->get_data()) {
    // This branch is where you process validated data.
    // Do stuff ...

    // Typically you finish up by redirecting to somewhere where the user
    // can see what they did.
    redirect($nexturl);
}
?>


<?php
echo('</div>');
echo $OUTPUT->footer();
?>

<script>
    function updateURL(a){
        var form = document.getElementById('mform1');
        form.setAttribute('action', 'http://localhost:8888/moodle31/local/courseseditor/'+a+'.php');
        form.submit();
    }
</script>