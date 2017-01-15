<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once('../../config.php');
require_once('form/nuovo.php');
require_once('class/SupsiWebServices.php');
require_once('class/UsiWebServices.php');


$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Course Creator");
$PAGE->set_heading("Course Creator");
require_login();

echo $OUTPUT->header();
echo('<h2>Crea un nuovo corso</h2><br><div>');

if($_GET['user_type']=='usi') $ws = new UsiWebServices();
else $ws = new SupsiWebServices();

$result=$ws->getCorsi();

$form = new FormNuovo(NULL,array('corsi'=>$result));

if ($fromform = $form->get_data()) {
    var_dump($fromform);
    //redirect($nexturl);
}

$form->display();
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
