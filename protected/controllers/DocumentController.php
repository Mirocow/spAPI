<?php
/**
 * Created by PhpStorm.
 * User: SlashMan
 * Date: 13.05.2015
 * Time: 20:41
 */

class DocumentController extends Controller
{
    public function actionNew($guid)
    {
        header("Access-Control-Allow-Origin: *");

        $data = json_decode(@file_get_contents('php://input'), true);
        $dir = 'documents/';

        $filename = $data['Document']['fileName'];

        file_put_contents($dir.$filename, '');

        $this->base64_to_file($data['Document']['content'], $dir.$filename);

        if($guid)
        {
            if($data !== array())
            {
                array_walk_recursive($data, 'Core::utfDe');
                $document = new Document();
                $document->file = $filename;
                $document->name = $filename;
                $document->guid = $guid; //@todo Проверка на гуид
                $document->save();
                var_dump($document->getErrors());
            }
        }
    }
}