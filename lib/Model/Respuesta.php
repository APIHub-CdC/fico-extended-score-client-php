<?php

namespace FicoEXTScored\Client\Model;

use \ArrayAccess;
use \FicoEXTScored\Client\ObjectSerializer;

class Respuesta implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;
    
    protected static $FicoEXTScoredModelName = 'Respuesta';
    
    protected static $FicoEXTScoredTypes = [
        'folio_consulta' => 'string',
        'folio' => 'string',
        'score' => '\FicoEXTScored\Client\Model\Score'
    ];
    
    protected static $FicoEXTScoredFormats = [
        'folio_consulta' => null,
        'folio' => null,
        'score' => null
    ];
    
    public static function FicoEXTScoredTypes()
    {
        return self::$FicoEXTScoredTypes;
    }
    
    public static function FicoEXTScoredFormats()
    {
        return self::$FicoEXTScoredFormats;
    }
    
    protected static $attributeMap = [
        'folio_consulta' => 'folioConsulta',
        'folio' => 'folio',
        'score' => 'score'
    ];
    
    protected static $setters = [
        'folio_consulta' => 'setFolioConsulta',
        'folio' => 'setFolio',
        'score' => 'setScore'
    ];
    
    protected static $getters = [
        'folio_consulta' => 'getFolioConsulta',
        'folio' => 'getFolio',
        'score' => 'getScore'
    ];
    
    public static function attributeMap()
    {
        return self::$attributeMap;
    }
    
    public static function setters()
    {
        return self::$setters;
    }
    
    public static function getters()
    {
        return self::$getters;
    }
    
    public function getModelName()
    {
        return self::$FicoEXTScoredModelName;
    }
    
    
    
    protected $container = [];
    
    public function __construct(array $data = null)
    {
        $this->container['folio_consulta'] = isset($data['folio_consulta']) ? $data['folio_consulta'] : null;
        $this->container['folio'] = isset($data['folio']) ? $data['folio'] : null;
        $this->container['score'] = isset($data['score']) ? $data['score'] : null;
    }
    
    public function listInvalidProperties()
    {
        $invalidProperties = [];
        return $invalidProperties;
    }
    
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }
    
    public function getFolioConsulta()
    {
        return $this->container['folio_consulta'];
    }
    
    public function setFolioConsulta($folio_consulta)
    {
        $this->container['folio_consulta'] = $folio_consulta;
        return $this;
    }
    
    public function getFolio()
    {
        return $this->container['folio'];
    }
    
    public function setFolio($folio)
    {
        $this->container['folio'] = $folio;
        return $this;
    }
    
    public function getScore()
    {
        return $this->container['score'];
    }
    
    public function setScore($score)
    {
        $this->container['score'] = $score;
        return $this;
    }
    
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }
    
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
    
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }
    
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }
    
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) {
            return json_encode(
                ObjectSerializer::sanitizeForSerialization($this),
                JSON_PRETTY_PRINT
            );
        }
        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}
