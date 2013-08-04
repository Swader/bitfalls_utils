<?php

namespace Bitfalls\Utilities;
use Phalcon\DI;

/**
 * Class Stringer
 * @package Bitfalls\Utilities
 */
class Stringer
{
    /**
     * @param $sString
     * @param bool $bIgnoreExtension
     * @return string
     */
    public static function cleanString($sString, $bIgnoreExtension = false)
    {
        $info = array();

        if ($bIgnoreExtension) {
            $info = pathinfo($sString);
            $sString = str_replace('.'.$info['extension'], '', $sString);
        }
        $sString = preg_replace("/[^A-Za-z0-9_]/", " ", $sString);
        $sString = preg_replace("/\s+/", " ", $sString);
        $sString = str_replace(" ", "_", $sString);
        if ($bIgnoreExtension) {
            $sString .= '.'.$info['extension'];
        }
        return strtolower($sString);
    }

    /**
     * Explodes string by underscore and cleans it if necessary, then returns a ucwords version of it
     * @param $string
     * @param bool $bCleanFirst
     * @return string
     */
    public static function toUcFirst($string, $bCleanFirst = false) {
        if ($bCleanFirst) {
            $string = self::cleanString($string);
        }
        $string = str_replace('_', ' ', $string);
        return ucwords($string);
    }

    /**
     * Converts any_string into CamelCase
     * @param $s
     * @param string $sDelimiter
     * @return string
     */
    public static function toCamelCase($s, $sDelimiter = '_')
    {
        return implode('', array_map(function ($el) {
            return ucfirst($el);
        }, explode($sDelimiter, $s)));
    }

    /**
     * @param int $length
     * @return string
     */
    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    /**
     * Formats currency and attaches symbol. Depends on countries list in config.php
     * @param $number
     * @return string
     */
    public static function formatCurrency($number) {
        $aCountryList = DI::getDefault()->get('config')->countryList;
        $sSymbol = $aCountryList[$aCountryList['currentCountry']]['currency'];
        $sType = $aCountryList[$aCountryList['currentCountry']]['currencyType'];
        switch ($sType) {
            case 'prefix':
                return $sSymbol.' '.number_format($number, 0, '.', '.');
                break;
            case 'suffix':
                return number_format($number, 0, '.', '.').' '.$sSymbol;
                break;
            default: break;
        }
        return "";
    }
}