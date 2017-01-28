<?php
defined('MOODLE_INTERNAL') || die();

define("TIPO_RELAZIONE_DOCENTE", 1);
define("TIPO_RELAZIONE_ASSISTENTE", 2);

define("TIPO_RICHIESTA_INSERIRE", 1);
define("TIPO_RICHIESTA_CLONARE", 2);
define("TIPO_RICHIESTA_CANCELLARE", 3);

define("STATO_RICHIESTA_FATTO", 1);
define("STATO_RICHIESTA_SOSPESO", 2);
define("STATO_RICHIESTA_DA_GESTIRE", 3);


class local_requestmanager_testcase extends advanced_testcase {
    /**
     * Test the savetodb of the requests
     */
    public function test_request_creation(){
        global $DB;
        $this->resetAfterTest(true);
        $user_from_db = $DB->get_record('user', array('id' => 2));

        $num_record_richiesta=$DB->count_records('requestmanager_richiesta', array());
        $num_record_in_corso=$DB->count_records('requestmanager_corso', array());
        $num_record_in_user_corso=$DB->count_records('requestmanager_corso_user', array());

        $r= new local_requestmanager\Richiesta();
        $r->setIdMdlUser($user_from_db->id);
        $r->setNote('Nota richiesta');


        $c = new local_requestmanager\Corso();
        $c->setIdMdlCourseCategories(3);
        $c->setTitolo('Corso per test');
        $c->setShortname('SHORTTEST');
        $c->setNote('NOTE TEST');
        $c->setIdLclCourseseditorRichiesta(2);
        $c->setTipoRichiesta(TIPO_RICHIESTA_INSERIRE);

        $user = new local_requestmanager\UserCorso();
        $user->setNome($user_from_db->firstname);
        $user->setCognome($user_from_db->lastname);
        $user->setIdMdlUser(2);
        $user->setTipoRelazione(TIPO_RELAZIONE_DOCENTE);

        $c->addUser($user);

        $r->addCorso($c);

        $this->assertequals(true,$r->saveToDB());
        self::assertEquals($num_record_richiesta+1,$DB->count_records('requestmanager_richiesta', array()));
        self::assertEquals($num_record_in_corso+1,$DB->count_records('requestmanager_corso', array()));
        self::assertEquals($num_record_in_user_corso+1,$DB->count_records('requestmanager_corso_user', array()));
        //$this->resetAllData();
    }

    public function test_supsi_ws(){
        $ws = new local_requestmanager\SupsiWebServices();
        $result = $ws->getCorsi();
        self::assertGreaterThan(0,count($result));
        self::assertObjectHasAttribute('titolo',$result[0]);
        self::assertObjectHasAttribute('modulo',$result[0]);
        self::assertObjectHasAttribute('dipartimento',$result[0]);
        self::assertObjectHasAttribute('corso_laurea',$result[0]);
        self::assertObjectHasAttribute('docenti',$result[0]);
        $ws->resetUrl();
        $result = $ws->getCorsi('admin');
        self::assertGreaterThan(0,count($result));
        self::assertObjectHasAttribute('titolo',$result[0]);
        self::assertObjectHasAttribute('modulo',$result[0]);
        self::assertObjectHasAttribute('dipartimento',$result[0]);
        self::assertObjectHasAttribute('corso_laurea',$result[0]);
        self::assertObjectHasAttribute('docenti',$result[0]);
        $ws->resetUrl();
        $result = $ws->getCorsi(null,'algoritmi');
        self::assertGreaterThan(0,count($result));
        self::assertObjectHasAttribute('titolo',$result[0]);
        self::assertObjectHasAttribute('modulo',$result[0]);
        self::assertObjectHasAttribute('dipartimento',$result[0]);
        self::assertObjectHasAttribute('corso_laurea',$result[0]);
        self::assertObjectHasAttribute('docenti',$result[0]);

    }

    public function test_usi_ws(){
        $ws = new local_requestmanager\UsiWebServices();
        $result = $ws->getCorsi();
        self::assertGreaterThan(0,count($result));
        self::assertObjectHasAttribute('titolo',$result[0]);
        self::assertObjectHasAttribute('corso_laurea',$result[0]);
        self::assertObjectHasAttribute('facolta',$result[0]);
        self::assertObjectHasAttribute('corso_laurea',$result[0]);
        self::assertObjectHasAttribute('docenti',$result[0]);
        self::assertObjectHasAttribute('assistenti',$result[0]);
        $ws->resetUrl();
        $result = $ws->getCorsi('admin');
        self::assertGreaterThan(0,count($result));
        self::assertObjectHasAttribute('titolo',$result[0]);
        self::assertObjectHasAttribute('corso_laurea',$result[0]);
        self::assertObjectHasAttribute('facolta',$result[0]);
        self::assertObjectHasAttribute('corso_laurea',$result[0]);
        self::assertObjectHasAttribute('docenti',$result[0]);
        self::assertObjectHasAttribute('assistenti',$result[0]);
        $ws->resetUrl();
        $result = $ws->getCorsi(null,'alg');
        self::assertGreaterThan(0,count($result));
        self::assertObjectHasAttribute('titolo',$result[0]);
        self::assertObjectHasAttribute('corso_laurea',$result[0]);
        self::assertObjectHasAttribute('facolta',$result[0]);
        self::assertObjectHasAttribute('corso_laurea',$result[0]);
        self::assertObjectHasAttribute('docenti',$result[0]);
        self::assertObjectHasAttribute('assistenti',$result[0]);


    }

}
