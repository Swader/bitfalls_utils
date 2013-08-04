<?php

namespace Bitfalls\Phalcon;

use \Bitfalls\Utilities\Stringer;

/**
 * Class Model
 * @package Bitfalls\Phalcon
 */
class Model extends \Phalcon\Mvc\Model
{

    /** @var bool */
    protected $bDirty = false;

    /** @var string */
    protected $entity_type;

    public function initialize()
    {
        $this->keepSnapshots(false);
        $this->useDynamicUpdate(true);
    }

    /**
     * @param null $bBool
     * @return $this|bool
     */
    public function isDirty($bBool = null)
    {
        if ($bBool === null) {
            return $this->bDirty;
        } else {
            $this->bDirty = (bool)$bBool;
            return $this;
        }
    }

    /**
     * @return \stdClass
     */
    public function getDummy()
    {
        $oR = new \ReflectionClass($this);
        $oDummy = new \stdClass();
        foreach ($oR->getProperties() as $property) {
            if (strpos($property->getName(), '_') !== 0) {
                /** @var \ReflectionProperty $property */
                $sName = $property->getName();
                $sMethodName = 'get' . Stringer::toCamelCase($sName);
                if (method_exists($this, $sMethodName)) {
                    $oDummy->$sName = $this->$sMethodName();
                }
            }
        }
        return $oDummy;
    }

    /**
     * @param bool $bAsString
     * @return \Phalcon\Mvc\Model\MessageInterface[]|string
     */
    public function getMessages($bAsString = false) {
        if (!$bAsString) {
            return parent::getMessages();
        } else {
            return implode(', ', parent::getMessages());
        }
    }

    /**
     * @param $aArray
     * @param bool $bParens
     * @return string
     */
    public static function arrayToInString($aArray, $bParens = true) {
        foreach ($aArray as $i => &$val) {
            if (is_scalar($val)) {
                if (!is_numeric($val)) {
                    $val = '"'.$val.'"';
                }
            } else {
                unset($aArray[$i]);
            }
        }
        $imploded = implode(',', $aArray);
        if ($bParens) {
            return '('.$imploded.')';
        }
        return $imploded;
    }

}