<?php
/**
 * Created by PhpStorm.
 * User: Rezart Lohja
 * Date: 15.01.17
 * Time: 09:47
 */

require_once 'UserCorso.php';

define("TIPO_RICHIESTA_INSERIRE", "Inserire");
define("TIPO_RICHIESTA_CLONARE", "Clonare");
define("TIPO_RICHIESTA_CANCELLARE", "Cancellare");

define("STATO_RICHIESTA_FATTO", "Fatto");
define("STATO_RICHIESTA_SOSPESO", "Sospeso");
define("STATO_RICHIESTA_DA_GESTIRE", "Da gestire");
class Corso
{
    var $id;
    var $id_lcl_courseseditor_richiesta;
    var $titolo;
    var $shortname;
    var $id_mdl_course_categories;
    var $note;
    var $tipo_richiesta;
    var $stato_richiesta;
    /**
     * @var UserCorso[]
     */
    var $user_assegnati;

    function __construct($id=null)
    {
        $this->id=$id;
    }

    public function loadFromDB(){
        global $DB;
        if($this->id>0){
            $res = $DB->get_record('lcl_courseseditor_corso', array('id'=>$this->id));
            if($res){
                $arr_c = get_object_vars($res);
                foreach($arr_c as $param => $valore){
                    $this->$param=$valore;
                }

                // carico i corsi richiesti per questa richiesta da DB
                $users = $DB->get_records('lcl_courseseditor_corso_user', array('id_lcl_courseseditor_corso'=>$this->id));

                foreach ($users as $user){
                    $arr_c = get_object_vars($user);
                    $c = new UserCorso();
                    //creo oggetti UserCorso da oggetto stdClass
                    foreach($arr_c as $param => $valore){
                        $c->$param=$valore;
                    }
                    $this->addUser($c);
                }

            }
        }else return false;
    }
    /**
     * Salva su DB il corso
     */
    public function saveToDB()
    {
        global $DB;
        $r = new stdClass();
        $r->id_lcl_courseseditor_richiesta=$this->id_lcl_courseseditor_richiesta;
        $r->titolo=$this->titolo;
        $r->shortname=$this->shortname;
        $r->id_mdl_course_categories=$this->id_mdl_course_categories;
        $r->note=$this->note;
        $r->tipo_richiesta=$this->tipo_richiesta;

        if($this->id>0){// update
            $r->id==$this->id;
            $r->stato_richiesta=$this->stato_richiesta;
            if($DB->update_record('lcl_courseseditor_corso', $r, false)){
                foreach ($this->user_assegnati as $user) {
                    $user->saveToDB();
                }
            }
        }else{// insert
            // inserisco solo se almeno un user assegnato e c'è la relazione con la richiesta
            if (count($this->user_assegnati) > 0 && $this->id_lcl_courseseditor_richiesta>0) {

                $r->stato_richiesta = STATO_RICHIESTA_DA_GESTIRE;

                $lastinsertid = $DB->insert_record('lcl_courseseditor_corso', $r, true);
                echo $DB->get_last_error();
                if ($lastinsertid) {// se inserimento andato bene allora inserisco i corsi
                    foreach ($this->user_assegnati as $user) {
                        $user->setIdLclCourseseditorCorso($lastinsertid);
                        $user->saveToDB();
                    }
                }
                return true;
            }else return false;
        }
    }
    /**
     * @param UserCorso $user
     */
    public function addUser(UserCorso $user){
        $this->user_assegnati[]=$user;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getIdLclCourseseditorRichiesta()
    {
        return $this->id_lcl_courseseditor_richiesta;
    }

    /**
     * @param mixed $id_lcl_courseseditor_richiesta
     */
    public function setIdLclCourseseditorRichiesta($id_lcl_courseseditor_richiesta)
    {
        $this->id_lcl_courseseditor_richiesta = $id_lcl_courseseditor_richiesta;
    }

    /**
     * @return mixed
     */
    public function getTitolo()
    {
        return $this->titolo;
    }

    /**
     * @param mixed $titolo
     */
    public function setTitolo($titolo)
    {
        $this->titolo = $titolo;
    }

    /**
     * @return mixed
     */
    public function getShortname()
    {
        return $this->shortname;
    }

    /**
     * @param mixed $shortname
     */
    public function setShortname($shortname)
    {
        $this->shortname = $shortname;
    }

    /**
     * @return mixed
     */
    public function getIdMdlCourseCategories()
    {
        return $this->id_mdl_course_categories;
    }

    /**
     * @param mixed $id_mdl_course_categories
     */
    public function setIdMdlCourseCategories($id_mdl_course_categories)
    {
        $this->id_mdl_course_categories = $id_mdl_course_categories;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return mixed
     */
    public function getTipoRichiesta()
    {
        return $this->tipo_richiesta;
    }

    /**
     * @param mixed $tipo_richiesta
     */
    public function setTipoRichiesta($tipo_richiesta)
    {
        $this->tipo_richiesta = $tipo_richiesta;
    }

    /**
     * @return mixed
     */
    public function getStatoRichiesta()
    {
        return $this->stato_richiesta;
    }

    /**
     * @param mixed $stato_richiesta
     */
    public function setStatoRichiesta($stato_richiesta)
    {
        $this->stato_richiesta = $stato_richiesta;
    }


}