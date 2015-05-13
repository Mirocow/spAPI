<?php
/**
 * Created by PhpStorm.
 * User: SlashMan
 * Date: 13.05.2015
 * Time: 18:33
 */

class PhotoController extends Controller
{
    public function actionDelete($id)
    {
        Photo::model()->deleteByPk($id);
    }
}