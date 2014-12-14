<?php
/**
 * Created by PhpStorm.
 * User: SlashMan
 * Date: 15.12.2014
 * Time: 0:46
 */
class Core {
    public static function utfEn($array)
    {
        foreach($array as $key=>$value)
        {
            $array[$key] = utf8_encode($value);
        }
        return $array;
    }
    public static function utfDe($array)
    {
        foreach($array as $key=>$value)
        {
            $array[$key] = utf8_decode($value);
        }
        return $array;
    }
}