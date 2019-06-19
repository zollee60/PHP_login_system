<?php

abstract class ErrorModel implements IUserModel
{
    // == constants ==
    const
    ERROR_UNDEFINED_SET = 'Trying to set undefined attribute: %s',
    ERROR_UNDEFINED_GET = 'Trying to get undefined attribute: %s';

    // == fields ==
    private $_attributeValues = [];

    // == constructor ==
    public function __construct( $attributes = [] ) {
        $this->setAttributes( $attributes );
    }

    // == public methods ==
    public function setAttributes( $attributes ) {
        foreach( $attributes as $name => $value )
            $this->setAttribute( $name, $value );
        return $this;
    }

    public function setAttribute( $name, $value ) {
        if( $this->hasAttribute( $name ) )
            $this->_attributeValues[ $name ] = trim( $value );
        return $this;
    }

    public function hasAttribute( $name ) {
        return in_array( $name, $this->getAttributeNames() );
    }

    public function getAttributes() {
        $attributes = [];
        foreach( $this->getAttributeNames() as $name )
            $attributes[ $name ] = $this->getAttribute( $name );
        return $attributes;
    }

    public function getAttribute( $name ) {
        if( isset( $this->_attributeValues[ $name ] ) )
            return $this->_attributeValues[ $name ];
        return null;
    }

    // == magic methods ==
    public function __set( $name, $value ) {
        if( $this->hasAttribute( $name ) )
            $this->setAttribute( $name, $value );
        else
            throw new Exception( sprintf( self::ERROR_UNDEFINED_SET, $name ) );
    }

    public function __get( $name ) {
        if( $this->hasAttribute( $name ) )
            return $this->getAttribute( $name );
        else
            throw new Exception( sprintf( self::ERROR_UNDEFINED_GET, $name ) );
    }

    public function __isset( $name ) {
        return $this->hasAttribute( $name ) || property_exists( $this, $name );
    }


}