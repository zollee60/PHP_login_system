<?php

abstract class ActiveRecord extends UserModel implements IActiveRecord {

    const
        ERROR_INVALID_CONDITION = 'Invalid find condition!';

    public function getAttributeNames() {
        return [ $this->primaryKey() ];
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

        return new static( $data );

    }

    protected static function findOneByPk( $primaryKey ) {
        // TODO: Find in database by primary key
        return [ 'id' => $primaryKey ];
    }

    protected static function findOneByAttributes( $attributes ) {
        // TODO: Find in database by attributes
        return $attributes;
    }

    protected static function findOneByQueryString( $queryString ) {
        // TODO: Find in database by attributes
        return [];
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
     */
    protected static function findAllByAttributes( $attributes ) {
        // TODO: Find in database by attributes
        return [];
    }

    /**
     * Find records by query-string
     * @param string $queryString
     * @return array
     */
    protected static function findAllByQueryString( $queryString ) {
        // TODO: Find in database by attributes
        return [];
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
        // TODO: Update record in the database
        return $this->getAttribute( $this->primaryKey() );
    }

    /**
     * Insert record
     * @return int|null
     */
    protected function insert() {
        // TODO: Insert record into the database
        // TODO: Set record's primary key to the last inserted ID
        $this->setAttribute( $this->primaryKey(), time() ); // Fake id
        return $this->getAttribute( $this->primaryKey() );
    }

}