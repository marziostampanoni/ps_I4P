<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once('../../config.php');
require_once('form/nuovo.php');


$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Course Creator");
$PAGE->set_heading("Course Creator");
require_login();

echo $OUTPUT->header();
echo('<h2>Crea un nuovo corso</h2><br><div>');

if($_GET['user_type']=='usi') $url = get_config('local_courseseditor','usi_host');
else $url = get_config('local_courseseditor','supsi_host');

//  Initiate curl
$ch = curl_init();
// Disable SSL verification
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// Will return the response, if false it print the response
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Set the url
curl_setopt($ch, CURLOPT_URL,$url);
// Execute
$result=curl_exec($ch);
// Closing
curl_close($ch);

// Will dump a beauty json :3
var_dump($result);


$form = new FormNuovo();
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
