<?php

require_once('../../config.php');

define("TIPO_RELAZIONE_DOCENTE", 1);
define("TIPO_RELAZIONE_ASSISTENTE", 2);

define("TIPO_RICHIESTA_INSERIRE", 1);
define("TIPO_RICHIESTA_CLONARE", 2);
define("TIPO_RICHIESTA_CANCELLARE", 3);

define("STATO_RICHIESTA_FATTO", 1);
define("STATO_RICHIESTA_SOSPESO", 2);
define("STATO_RICHIESTA_DA_GESTIRE", 3);

//form
require_once('form/delete.php');
require_once('form/selectcat.php');
require_once('form/clone.php');
require_once('form/manage.php');
require_once('form/selectuser.php');
require_once('form/addnewcourse.php');
require_once('form/searchcoursesws.php');
require_once('form/selectcorses.php');
require_once('form/resume.php');
require_once('form/start.php');

