<?php

class RegForm{

    protected $labela;
    protected $error;
    protected $fname;
    protected $itype;

    public function __construct(){
        $this->labela = array("Vezetéknév", "Keresztnév", "E-mail cím", "Jelszó", "Jelszó megerősítés");
        $this->error = array("names","names","email","password","conf_password");
        $this->fname = array('surName','lastName','email','password','confPassword');
        $this->itype = array("text","text","text","password","password");
    }

    public function getLabel($i) {return $this->labela[$i];}
    public function getError($i) {return $this->error[$i];}
    public function getFname($i) {return $this->fname[$i];}
    public function getItype($i) {return $this->itype[$i];}

}

?>