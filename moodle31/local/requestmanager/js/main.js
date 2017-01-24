function updateURL(a,form = 'mform1') {
    var form = document.getElementById(form);
    form.setAttribute('action', 'http://localhost:8888/moodle31/local/requestmanager/' + a + '.php');
    form.submit();
}



