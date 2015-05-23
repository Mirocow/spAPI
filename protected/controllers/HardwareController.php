<?php
/**
 * Created by PhpStorm.
 * User: SlashMan
 * Date: 23.05.2015
 * Time: 12:15
 */

class HardwareController extends Controller
{
    public function actionDelete($id)
    {
        Hardware::model()->deleteByPk($id);
    }
}