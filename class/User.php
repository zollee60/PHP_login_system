<?php

class User{

    protected $email;
    protected $sur_name;
    protected $last_name;
    protected $password;
    protected $conf_password;
    protected $error;

    public function __construct()
    {
        $argv = func_get_args();
        switch(func_num_args()){
            case 3: self::__construct3(); break;
            case 2: self::__construct1($argv[0],$argv[1]); break;
            case 5: self::__construct2($argv[0],$argv[1],$argv[2],$argv[3],$argv[4]); break;
        }
    }

    public function __construct1($email, $password){
        $this->email = $email;
        $this->sur_name = "";
        $this->last_name = "";
        $this->password = $password;
        $this->conf_password = "";
        $this->error = array("email" => "", "names" => "", "password" => "", "conf_password" => "");
    }

    public function __construct2($email, $sur_name, $last_name, $password, $conf_password){
        $this->email = $email;
        $this->sur_name = $sur_name;
        $this->last_name = $last_name;
        $this->password = $password;
        $this->conf_password = $conf_password;
        $this->error = array("email" => "", "names" => "", "password" => "", "conf_password" => "");
    }

    public function __construct3(){
        $this->email = "";
        $this->sur_name = "";
        $this->last_name = "";
        $this->password = "";
        $this->conf_password = "";
        $this->error = array("email" => "", "names" => "", "password" => "", "conf_password" => "");
    }

    public function getSur_name() {return $this->sur_name;}
    public function setSur_name($sur_name) {$this->sur_name = $sur_name; return $this;}

    public function getLast_name() {return $this->last_name;}
    public function setLast_name($last_name) {$this->last_name = $last_name; return $this;}

    public function getEmail() {return $this->email;}
    public function setEmail($email) {$this->email = $email; return $this;}

    public function getPassword() {return $this->password;}
    public function setPassword($password) {$this->password = $password; return $this;}

    public function getConf_password() {return $this->conf_password;}
    public function setConf_password($conf_password) {$this->conf_password = $conf_password; return $this;}

    public function getError($key) {
        if(!empty($this->error[$key])) {
            return $this->error[$key];
        } else return "";
    }
    public function setError($key, $error) {$this->error[$key] = $error; return $this;}

    public function loggedIn() {
        if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true){
            return true;
        } else return false;
    }

    public function is_valid_password($password) {
        return preg_match_all('$S*(?=S{8,})(?=S*[a-z])(?=S*[A-Z])(?=S*[d])(?=S*[W])S*$', $password) ? TRUE : FALSE;
    }


    public function validateEmail(PDO $pdo){
        $email = $this->getEmail();

        if(!empty($email)){
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){

                $sql = "SELECT id FROM users WHERE user_email = :user_email";

                if ($stmt = $pdo->prepare($sql)){

                    $stmt->bindParam(":user_email",$param_user_email,PDO::PARAM_STR);
                    $param_user_email = $email;
                    
                    if($stmt->execute()){
                        if($stmt->rowCount() == 0){
                            return true;
                        } else{
                            $this->setError("email", "Ez az e-mail cím már foglalt.");
                            return false;
                        }
                    }
                }
                unset($stmt);
            } else{
                $this->setError("email", "Kérlek adj meg valós e-mail címet!");
                return false;
            }
        } else{
            $this->setError("email", "Kérlek adj meg valós e-mail címet!");
            return false;
        }
    }

    public function validatePasswords(){
        $pw = $this->getPassword();
        $cpw = $this->getConf_password();

        if(!empty($pw)){
            if(!self::is_valid_password($pw)){
                if ($cpw == $pw){
                    return true;
                } else{
                    $this->setError("password", "A két jelszó nem egyezik!");
                    return false;
                }
            } else{
                $this->setError("password", "A jelszónak tartalmaznia kell min. 1 kisbetűt, 1 nagybetűt, 1 speciális karaktert és legalább 8 karakter hosszúnak kell lennie.");
                return false;
            }
        } else{
            $this->setError("password", "Kérlek adj meg egy jelszót!");
            return false;
        }

    }

    public function validateNames(){
        $sur_name = $this->getSur_name();
        $last_name = $this->getLast_name();

        if(!empty($sur_name) && !empty($last_name)){
            return true;
        } else{
            $this->setError("names", "Kérlek add meg a neved!");
            return false;
        }
    }

    public function registerUser(PDO $pdo){
        if($this->validateEmail($pdo)){
            if($this->validatePasswords()){
                if($this->validateNames()){
                    $sql = "INSERT INTO users (user_email, password, vez_nev, ker_nev) VALUES (:user_email, :password, :sur_name, :last_name)";

                    if($stmt = $pdo->prepare($sql)){

                        $data = [
                            'user_email' => $this->getEmail(),
                            'password' => password_hash($this->getPassword(), PASSWORD_DEFAULT),
                            'sur_name' => $this->getSur_name(),
                            'last_name' => $this->getLast_name(),
                        ];

                        if($stmt->execute($data)){
                            header("location: login.php");
                        }else echo "Hiba történt.";
                    }
                    unset($stmt);
                }
            }
        }
        unset($pdo);
    }

    public function loginUser(PDO $pdo){
        $email = $this->getEmail();
        $pw = $this->getPassword();

        if(!empty($email)){
            if(!empty($pw)){

                $sql = "SELECT id, user_email, password, vez_nev, ker_nev FROM users WHERE user_email = :user_email";

                if($stmt = $pdo->prepare($sql)){

                    $stmt->bindParam(":user_email",$param_user_email,PDO::PARAM_STR);
                    $param_user_email = $email;

                    if($stmt->execute()){
                        if($stmt->rowCount() == 1){
                            if($row = $stmt->fetch()){

                                $id = $row["id"];
                                $user_email = $row["user_email"];
                                $hashed_pw = $row["password"];
                                $sur_name = $row["vez_nev"];
                                $last_name = $row["ker_nev"];

                                if(password_verify($pw, $hashed_pw)){
                                    session_start();

                                    $_SESSION["loggedin"] = true;
                                    $_SESSION["id"] = $id;
                                    $_SESSION["user_email"] = $user_email;
                                    $_SESSION["vez_nev"] = $sur_name;
                                    $_SESSION["ker_nev"] = $last_name;

                                    header("location: index.php");
                                } else{
                                    $this->setError("password", "A jelszó helytelen!");
                                    die;
                                }
                            }
                        }
                    }
                }
                unset($stmt);
            } else{
                $this->setError("password", "Add meg a jelszavad!");
                die;
            }
        } else{
            $this->setError("email", "Add meg az e-mail címed!");
            die;
        }
        unset($pdo);
    }


}

?>