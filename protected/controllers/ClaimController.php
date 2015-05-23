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
        $claim = Claim::model()->findByPk($id);
        if($claim)
        {
            $claim->status = $status;
            $claim->save();
        }
    }
    public function actionDelete($id)
    {
        $claim = Claim::model()->deleteByPk($id);
    }
}