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
        $item = iconv('windows-1251', 'UTF-8', $item);
    }
    public static function utfDe(&$item, $key)
    {
        $item = iconv('UTF-8', 'windows-1251', $item);
    }
}