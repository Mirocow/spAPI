<?php
/**
 * Created by PhpStorm.
 * User: SlashMan
 * Date: 13.05.2015
 * Time: 19:03
 */

class EntityController extends Controller
{
    public function actionComment($id)
    {
        header("Access-Control-Allow-Origin: *");
        $data = json_decode(@file_get_contents('php://input'), true);
        array_walk_recursive($data, 'Core::utfDe');

        $entity = Entity::model()->findByPk($id);
        $entity->comment = $data['comment'];
        $entity->save();
    }
}