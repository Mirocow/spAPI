<?php
/**
 * Created by PhpStorm.
 * User: SlashMan
 * Date: 23.05.2015
 * Time: 13:02
 */

class ClaimController extends Controller
{
    public function actionUpdateStatus($id, $status)
    {
        Claim::model()->updateAll(['status' => $status], 'id = :id', [':id' => $id]);
    }
}