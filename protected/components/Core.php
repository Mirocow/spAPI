<?php
/**
 * Created by PhpStorm.
 * User: SlashMan
 * Date: 15.12.2014
 * Time: 0:46
 */
class Core {
    public static function utfEn($key, $item)
    {
        $item = utf8_encode($item);
    }
    public static function utfDe($array)
    {
        $item = utf8_decode($item);
    }
}