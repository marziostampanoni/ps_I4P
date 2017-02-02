<?php
namespace local_requestmanager;

interface WebServicesInterface{
    /** Ricerca corsi per netID
     * @param $netId
     * @return Array
     */
    function getCorsiPerNetID($netId);

    /** Ricerca corsi per la stringa passata
     * @param $string
     * @return Array
     */
    function getCorsiPerString($string);

    /** Ritorna tutti i corsi
     * @return array
     */
    function getCorsi($netId=null,$string=null);
}