function updateURL(a,form = 'mform1') {
    var form = document.getElementById(form);
    form.setAttribute('action', 'http://localhost:8888/moodle31/local/requestmanager/' + a + '.php');
    form.submit();
}

function actionOnCourse(id, action) {
    if (action === 'save' || action === 'cancel' || action === 'savereq' || action === 'cancelreq' || action === 'reject') {
        var form = document.getElementById('mform2');
        var action = '?' + action + '=' + id;
        form.action += action;
        form.submit();
    } else {
        return;
    }
}

function enroll(idCorso, role) {
    var coursetitle = document.getElementById('coursetitle_' + idCorso).value;
    var usersArray = new Array();
    if (role == 0) {
        var users = document.getElementsByClassName('editingteacher_' + idCorso);
    } else if (role == 1) {
        var users = document.getElementsByClassName('teacher_' + idCorso);
    }
    for (var i = 0; i < users.length; i++) {
        usersArray += users[i].value;
    }
    setupModal(coursetitle, usersArray, idCorso, role);
}

function setupModal(title, users, idCorso, role) {
    document.getElementById('modal_detail_course').value = idCorso;
    document.getElementById('modal_detail_role').value = role;
    document.getElementById('coursetitle').innerHTML = title;
    var enrolled = document.getElementById('enrolled');
    for (var i = 0; i < users.length; i++) {
        swap(users[i], '1')
    }
}

function swap(user, prev='-1') {
    var element = document.getElementById('user_' + user);
    var flag = document.getElementById('flag_' + user);
    var althere = document.getElementById('prev_' + user);
    var btn = document.getElementById('btn_' + user);
    var table;
    switch (flag.value) {
        case '1':
            table = document.getElementById('notenrolled');
            btn.innerHTML = "Enroll";
            flag.value = '0';
            break;
        case '0':
            table = document.getElementById('enrolled');
            btn.innerHTML = "Unenroll";
            flag.value = '1';
            break;
    }
    if (prev == '-1') {
        prev = althere.value;
    }
    althere.value = prev;
    element.parentNode.removeChild(element);
    table.append(element);
}

function updateField() {
    var enrolled = document.getElementById('enrolled');
    var btns = enrolled.getElementsByClassName('button-choice');
    var idCourse = document.getElementById('modal_detail_course').value;
    var role = document.getElementById('modal_detail_role').value;
    var ul, cl, lis;
    switch (role) {
        case '0':
            cl = 'editingteacher_' + idCourse;
            ul = document.getElementById('editingteachers_list_' + idCourse);
            lis = ul.getElementsByClassName('editingteacher_' + idCourse);
            break;
        case '1':
            cl = 'teacher_' + idCourse;
            ul = document.getElementById('teachers_list_' + idCourse);
            lis = ul.getElementsByClassName('teacher_' + idCourse);
            break;
    }
    var ids = [];
    for (var i = 0; i < btns.length; i++) {
        var str = btns[i].value;
        var id = str.substring(0, str.indexOf("_"));
        var name = str.substring(str.indexOf("_") + 1);
        var add = true;
        for (var j = 0; j < lis.length; j++) {
            if (lis[j].value == id) {
                add = false;
            }
        }
        if (add) {
            ids.push(id);
        }
    }


    //TODO FIXARE IL DELETE DEGLI UTENTI!
    var todelete = [];
    var not = document.getElementById('notenrolled');
    var althere = not.getElementsByClassName('althere');
    for (var k = 0; k < althere.length; k++) {
        if (althere[k].value == 1) {
            todelete.push(althere[k].getAttribute('data-id'))
        }
    }
    document.getElementById('modal_detail_course').value = '';
    document.getElementById('modal_detail_role').value = '';
    var form = document.getElementById('mform2');
    var toDel = todelete.join('_');
    var toCreate = ids.join('_');
    var data = '&c=' + toCreate + '&d=' + toDel;
    form.action += '?updateEnroll=' + role + '&id=' + idCourse + data;
    form.submit();
}

