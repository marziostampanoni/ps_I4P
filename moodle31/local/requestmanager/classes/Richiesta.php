<?php

namespace local_requestmanager;

class Richiesta
{
    /**
     * @var Id della richiesta sul DB
     */
    var $id;
    /**
     * @var Id del user moodle sul DB
     */
    var $id_mdl_user;
    /**
     * @var timestamp della richiesta
     */
    var $data_richiesta;
    /**
     * @var note sulla richiesta
     */
    var $note;
    /**
     * @var Corso[] con gli oggetti Corso che fanno parte di questa richiesta
     */
    var $corsi_richiesti;

    /**
     * Richiesta constructor.
     * @param null $id
     */
    function __construct($id=null)
    {
        $this->id=$id;
    }

    /**
     * Load from Database the object searching by id
     * @return bool
     */
    public function loadFromDB(){
        global $DB;
        if($this->id>0){
            $res = $DB->get_record('requestmanager_richiesta', array('id'=>$this->id));
            if($res){
                $this->setDataRichiesta($res->data_richiesta);
                $this->setIdMdlUser($res->id_mdl_user);
                $this->setNote($res->note);
                // carico i corsi richiesti per questa richiesta da DB
                $corsi = $DB->get_records('requestmanager_corso', array('id_requestmanager_richiesta'=>$this->id));

                foreach ($corsi as $corso){
                    $c = new Corso();
                    $c->setId($corso->id);
                    $c->loadFromDB();
                    $this->addCorso($c);
                }
                return true;
            }
        }else return false;
    }

    /**
     * Save to DataBase this object
     */
    public function saveToDB()
    {
        global $DB;
        if (count($this->corsi_richiesti) > 0) {// inserisco solo se almeno un corso richiesto
            $r = new \stdClass();
            $r->id_mdl_user = $this->id_mdl_user;
            $r->data_richiesta = time();
            $r->note = $this->note;

            if($this->id>0){// update
                $r->id=$this->id;
                if($DB->update_record('requestmanager_corso', $r, false)){
                    foreach ($this->corsi_richiesti as $corso) {
                        $corso->saveToDB();
                    }
                }
            }else {
                // inserisco la richiesta
                $lastinsertid = $DB->insert_record('requestmanager_richiesta', $r, true);

                if ($lastinsertid) {// se inserimento andato bene allora inserisco i corsi
                    foreach ($this->corsi_richiesti as $corso) {

                        $corso->setIdLclCourseseditorRichiesta($lastinsertid);
                        $corso->saveToDB();
                    }
                }
            }
            return true;
        } else return false;
    }

    /**
     * @param Corso $corso
     */
    public function addCorso(Corso $corso){
        $this->corsi_richiesti[]=$corso;
    }

    /**
     * @return Id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Id $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Id
     */
    public function getIdMdlUser()
    {
        return $this->id_mdl_user;
    }

    /**
     * @param Id $id_mdl_user
     */
    public function setIdMdlUser($id_mdl_user)
    {
        $this->id_mdl_user = $id_mdl_user;
    }

    /**
     * @return timestamp
     */
    public function getDataRichiesta()
    {
        return $this->data_richiesta;
    }

    /**
     * @param timestamp $data_richiesta
     */
    public function setDataRichiesta($data_richiesta)
    {
        $this->data_richiesta = $data_richiesta;
    }

    /**
     * @return note
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param note $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return Corso[]
     */
    public function getCorsiRichiesti()
    {
        return $this->corsi_richiesti;
    }

    /**
     * @param Corso[] $corsi_richiesti
     */
    public function setCorsiRichiesti($corsi_richiesti)
    {
        $this->corsi_richiesti = $corsi_richiesti;
    }
}