function updateURL(a) {
    var form = document.getElementById('mform1');
    form.setAttribute('action', 'http://localhost:8888/moodle31/local/courseseditor/' + a + '.php');
    form.submit();
}



