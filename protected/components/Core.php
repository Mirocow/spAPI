<?php
/**
 * Created by PhpStorm.
 * User: SlashMan
 * Date: 15.12.2014
 * Time: 0:46
 */
class Core
{
    public static function utfEn(&$item, $key)
    {
        $item = $item;//mb_convert_encoding($item, "utf-8", "windows-1251");
    }

    public static function utfDe(&$item, $key)
    {
        $item = $item;//mb_convert_encoding($item, "windows-1251", "utf-8");
    }

    public static function utfVarEn($item)
    {
        return mb_convert_encoding($item, "utf-8", "windows-1251");
    }
    public static function utfVarDe($item)
    {
        return mb_convert_encoding($item, "windows-1251", "utf-8");
    }
}