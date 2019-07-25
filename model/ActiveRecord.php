<?php
require "UserModel.php";
require "IActiveRecord.php";
require "DBConnenction.php";

abstract class ActiveRecord extends UserModel implements IActiveRecord {

    const
        ERROR_INVALID_CONDITION = 'Invalid find condition!';

    public function getAttributeNames() {
        return [ $this->primaryKey(), 'email',  'password', 'confPassword', 'surName', 'lastName'];
    }

    public static function findOne( $condition ) {

        if( is_numeric( $condition ) )
            $data = self::findOneByPk( $condition );
        elseif( is_array( $condition ) )
            $data = self::findOneByAttributes( $condition );
        elseif( is_string( $condition ) )
            $data = self::findOneByQueryString( $condition );
        else
            throw new Exception( self::ERROR_INVALID_CONDITION );

        if( $data === null )
            return null;

        return new static($data);

    }

    protected static function findOneByPk($primaryKey) {
        $tableName = self::tableName();
        $sql = "SELECT * FROM usertable WHERE id = :id";

        $result = [];
        $pdo = DBConn::connect();
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam('id',$param_id,PDO::PARAM_INT);
        $param_id = $primaryKey;
        $stmt->execute();
        if($stmt->rowCount() == 1){
            $result = $stmt->fetch();
        }
        unset($stmt);
        unset($pdo);
        return $result;
    }

    protected static function findOneByAttributes( $attributes ) {
        array_key_exists('id',$attributes) ? $id = $attributes['id'] : $id = '';
        array_key_exists('email',$attributes) ? $userEmail = $attributes['email'] : $userEmail = '';

        $tableName = self::tableName();
        $result = [];
        if(!empty($id)){
            $result = self::findOneByPk($id);
        } elseif (!empty($userEmail)) {
            $sql = "SELECT * FROM usertable WHERE email = :user_email";

            $pdo = DBConn::connect();
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam('user_email',$param_email,PDO::PARAM_STR);
            $param_email = $userEmail;
            $stmt->execute();
            if($stmt->rowCount() == 1){
                $result = $stmt->fetch();
            }
            unset($stmt);
            unset($pdo);
        }
        return $result;
    }

    protected static function findOneByQueryString( $queryString ) {
        $query_array = [];
        parse_url($queryString, $query_array);

        return self::findOneByAttributes($query_array);
    }

    public static function findAll( $condition ) {

        if( is_array( $condition ) )
            $data = self::findAllByAttributes( $condition );
        elseif( is_string( $condition ) )
            $data = self::findAllByQueryString( $condition );
        else
            throw new Exception( self::ERROR_INVALID_CONDITION );

        foreach( $data as $r => $row )
            $data[ $r ] = new static( $row );

        return $data;

    }

    /**
     * Find records by attributes
     * @param array $attributes
     * @return array
     * @throws Exception
     */
    protected static function findAllByAttributes( $attributes ) {
        array_key_exists('sur_name',$attributes) ? $surName = $attributes['sur_name'] : $surName = '';
        array_key_exists('last_name',$attributes) ? $lastName = $attributes['last_name'] : $lastName = '';

        $tableName = self::tableName();
        $pdo = DBConn::connect();
        $result = [];
        if(!empty($surName)){
            if(!empty($lastName)){
                $sql = "SELECT * FROM usertable WHERE lastName = :surName AND surName = :lastName";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam('surName',$param_sname,PDO::PARAM_STR);
                $stmt->bindParam('lastName',$param_lname,PDO::PARAM_STR);
                $param_lname = $lastName;
                $param_sname = $surName;
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                unset($stmt);
            } else{
                $sql = "SELECT * FROM usertable WHERE lastName = :surName";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam('surName',$param_sname,PDO::PARAM_STR);
                $param_sname = $surName;
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                unset($stmt);
            }
        } elseif(!empty($lastName)){
            $sql = "SELECT * FROM usertable WHERE surName = :lastName";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam('lastName',$param_lname,PDO::PARAM_STR);
            $param_lname = $lastName;
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            unset($stmt);
        } else{
            throw new Exception( self::ERROR_INVALID_CONDITION );
        }
        unset($pdo);

        return $result;
    }

    /**
     * Find records by query-string
     * @param string $queryString
     * @return array
     * @throws Exception
     */
    protected static function findAllByQueryString( $queryString ) {
        $query_array = [];
        parse_url($queryString, $query_array);

        return self::findAllByAttributes($query_array);
    }

    /**
     * Save record
     * @return int|null
     */
    public function save() {
        $primaryKey = $this->getAttribute( $this->primaryKey() );
        if( !empty( $primaryKey ) )
            return $this->update();
        else
            return $this->insert();
    }

    /**
     * Update record
     * @return int|null
     */
    protected function update() {
        $attributes = $this->getAttributes();
        $id = $this->getAttribute($this->primaryKey());
        $userEmail = $attributes['email'];
        $surName = $attributes['surName'];
        $lastName = $attributes['lastName'];
        $password = password_hash($attributes['password'], PASSWORD_DEFAULT);

        $pdo = DBConn::connect();

        $sql = "UPDATE usertable
                SET email = $userEmail, password = $password, lastName = $surName, surName = $lastName
                WHERE id = $id";

        try{
            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            unset($stmt);
        }
        catch (PDOException $e){print $e->getMessage();}
        unset($pdo);

        return $this->getAttribute( $this->primaryKey() );
    }

    /**
     * Insert record
     * @return int|null
     */
    protected function insert() {
        $attributes = $this->getAttributes();
        $pdo = DBConn::connect();

        $sql = "INSERT INTO usertable(email, password, lastName, surName)
                VALUES (:email, :password, :surName, :lastName)";

        try{
            $stmt = $pdo->prepare($sql);
            $data = [
                'email' => $this->getAttribute('email'),
                'password' => password_hash($this->getAttribute('password'), PASSWORD_DEFAULT),
                'surName' => $this->getAttribute('surName'),
                'lastName' => $this->getAttribute('lastName'),
            ];
            $stmt->execute($data);
            $lastId = $pdo->lastInsertId();
            $this->setAttribute( $this->primaryKey(), $lastId);

            unset($stmt);
        }
        catch (PDOException $e){print $e->getMessage();}
        unset($pdo);

        return $this->getAttribute( $this->primaryKey() );
    }

    public static function tableName()
    {
        return "usertable";
    }

    public static function primaryKey()
    {
        return "id";
    }

}