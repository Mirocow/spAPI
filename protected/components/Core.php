<?php
/**
 * Created by PhpStorm.
 * User: SlashMan
 * Date: 15.12.2014
 * Time: 0:46
 */
class Core {
    public static function utfEn(&$item, $key)
    {
        $item = utf8_encode($item);
    }
    public static function utfDe(&$item, $key)
    {
        $item = utf8_decode($item);
    }
}