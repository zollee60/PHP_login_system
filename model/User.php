<?php
require "ActiveRecord.php";

class User extends ActiveRecord {

    protected $error;

    public function __construct($attributes = [])
    {
        $this->setAttributes($attributes);
    }

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


    public function validateEmail(){
        $email = $this->getAttribute('email');
        $attributes = ['email' => $email];

        if(!empty($email)){
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                $result = self::findOne($attributes);
                if(!in_array($email, $result->getAttributes())){
                    return true;
                }else{
                    $this->setError("email", "Ez az e-mail cím már foglalt.");
                    return false;
                }
            } else{
                $this->setError("email", "Kérlek adj meg valós e-mail címet!");
                return false;
            }
        } else{
            $this->setError("email", "Kérlek adj meg e-mail címet!");
            return false;
        }
    }

    public function validatePasswords(){
        $pw = $this->getAttribute('password');
        $cpw = $this->getAttribute('confPassword');

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
        $sur_name = $this->getAttribute('surName');
        $last_name = $this->getAttribute('lastName');

        if(!empty($sur_name) && !empty($last_name)){
            return true;
        } else{
            $this->setError("names", "Kérlek add meg a neved!");
            return false;
        }
    }

    public function registerUser(){
        if($this->validateEmail()){
            if($this->validatePasswords()){
                if($this->validateNames()){
                    $this->save();
                    header("location: login.php");
                }
            }
        }
    }

    public function loginUser(){
        $email = $this->getAttribute('email');
        $pw = $this->getAttribute('password');
        $attributes = ['email' => $email];
        if(!empty($email)){
            if(!empty($pw)){
                $result = self::findOne($attributes);
                if(in_array($email,$result->getAttributes())){
                    $hashed_pw = $result->getAttribute('password');
                    if(password_verify($pw,$hashed_pw)){
                        session_start();

                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $result['id'];
                        $_SESSION["user_email"] = $result['email'];
                        $_SESSION["lastName"] = $result['lastName'];
                        $_SESSION["surName"] = $result['surName'];

                        $this->setAttributes($result);

                        header("location: index.php");
                    } else{
                        $this->setError("password", "A jelszó helytelen!");
                        die;
                    }
                }
            } else{
                $this->setError("password", "Add meg a jelszavad!");
                die;
            }
        } else{
            $this->setError("email", "Add meg az e-mail címed!");
            die;
        }
    }
}

?>