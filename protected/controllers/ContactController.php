<?php
/**
 * Created by PhpStorm.
 * User: SlashMan
 * Date: 21.05.2015
 * Time: 19:11
 */


class ContactController extends Controller {

    public function actionNew($guid)
    {
        header("Access-Control-Allow-Origin: *");
        $data = json_decode(@file_get_contents('php://input'), true);

        if($guid)
        {
            if($data !== array())
            {
                array_walk_recursive($data, 'Core::utfDe');
                $contact = new Contact();
                $contact->attributes = $data['Contact'];
                $contact->guid = $guid; //@todo Проверка на гуид
                $contact->save();
            }
        }
    }

    public function actionEdit($id)
    {
        header("Access-Control-Allow-Origin: *");
        $data = json_decode(@file_get_contents('php://input'), true);
        $contact = Contact::model()->findByPk($id);

        if($id)
        {
            if($data !== array())
            {
                array_walk_recursive($data, 'Core::utfDe');
                $contact->attributes = $data['Claim'];
                $contact->save();
            }
        }
    }

    public function actionDelete($id)
    {
        Contact::model()->deleteByPk($id);
    }
}