<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex($guid = null)
	{
        $entity = Entity::model()->findByPk($guid);
        $response = array(
            'guid' => $entity->guid,
            'comment' => $entity->comment,
            'claims' => $this->actionClaims($guid, false),
            'hardware' => $this->actionHardware($guid, false),
            'contacts' => $this->actionContacts($guid, false),
            'photos' => $this->actionPhotos($guid, false),
            'documents' => $this->actionDocuments($guid, false),
        );
        print json_encode($response);
	}
    public function actionDocuments($guid = null, $render = true)
    {
        header("Access-Control-Allow-Origin: *");
        $response = array();

        if($guid === null)
            $documents = Document::model()->findAll();
        else
            $documents = Document::model()->findAllByAttributes(array('entity_id' => $guid));

        foreach($documents as $document)
            $response[] = array('id' => $document->id, 'name' => $document->name, 'file' => $document->file);

        array_walk_recursive($response, 'Core::utfEn');

        if($render)
            echo json_encode($response);
        else
            return $response;
    }
    public function actionEntities()
    {
        header("Access-Control-Allow-Origin: *");
        $response = array();
        $entities = Entity::model()->findAll(['order' => 'name ASC']);
        foreach($entities as $entity)
            $response[] = array('id' => $entity->guid, 'name' => $entity->name);

        array_walk_recursive($response, 'Core::utfEn');
        echo json_encode($response);
    }
    public function actionHardware($guid = null, $render = true)
    {
        header("Access-Control-Allow-Origin: *");
        $response = array();

        if($guid === null)
            $hardwares = Hardware::model()->findAll();
        else
            $hardwares = Hardware::model()->findAllByAttributes(array('guid' => $guid));

        foreach($hardwares as $hardware)
            $response[] = array('id' => $hardware->id, 'name' => $hardware->name, 'guid' => $hardware->guid);

        array_walk_recursive($response, 'Core::utfEn');

        if($render)
            echo json_encode($response);
        else
            return $response;
    }
    public function actionContacts($guid = null, $render = true)
    {
        header("Access-Control-Allow-Origin: *");
        $response = array();

        if($guid === null)
            $contacts = Contact::model()->findAll();
        else
            $contacts = Contact::model()->findAllByAttributes(array('guid' => $guid));

        foreach($contacts as $contact)
            $response[] = array('id' => $contact->id, 'name' => $contact->name, 'phone' => $contact->phone, 'email' => $contact->email, 'guid' => $contact->guid);

        array_walk_recursive($response, 'Core::utfEn');

        if($render)
            echo json_encode($response);
        else
            return $response;
    }
    public function actionPhotos($guid = null, $render = true)
    {
        header("Access-Control-Allow-Origin: *");
        $response = array();

        if($guid === null)
            $photos = Photo::model()->findAll();
        else
            $photos = Photo::model()->findAllByAttributes(array('guid' => $guid));

        foreach($photos as $photo)
            $response[] = array('id' => $photo->id, 'name' => $photo->name, 'filename' => $photo->filename);

        array_walk_recursive($response, 'Core::utfEn');

        if($render)
            echo json_encode($response);
        else
            return $response;
    }
    public function actionClaims($guid = null, $render = true)
    {
        header("Access-Control-Allow-Origin: *");
        $response = array();
        $criteria = new CDbCriteria();
        $criteria->order = 'id DESC';


        $data = json_decode(@file_get_contents('php://input'), true);
        if(is_array($data))
            array_walk_recursive($data, 'Core::utfDe');

        if($guid)
        {
            $criteria->condition .= 'guid = '.$guid;
            if(isset($data['search']))
                $criteria->condition .= " AND name LIKE '%".$data['search']."%' ";
        }

        $claims = Claim::model()->findAll($criteria);

        foreach($claims as $claim)
            $response[] = array('id' => $claim->id, 'name' => $claim->name, 'error' => $claim->error, 'guid' => $claim->guid, 'status' => $claim->status, 'class' => $claim->getClass(), 'statusText' => $claim->getStatusText(), 'created' => $claim->created, 'closed' => $claim->closed, 'comment' => $claim->comment);

        array_walk_recursive($response, 'Core::utfEn');

        if($render)
            echo json_encode(['claims' => $response]);
        else
            return $response;
    }
    public function actionNewClaim($guid)
    {
        header("Access-Control-Allow-Origin: *");
        $data = json_decode(@file_get_contents('php://input'), true);

        if($guid)
        {
            if($data !== array())
            {
                array_walk_recursive($data, 'Core::utfDe');
                $claim = new Claim();
                $claim->attributes = $data['Claim'];
                $claim->created = new CDbExpression('GETDATE()');
                $claim->guid = $guid; //@todo Проверка на гуид
                $claim->status = 1;
                $claim->save();
            }
        }
    }
    public function actionNewHardware($guid)
    {
        header("Access-Control-Allow-Origin: *");
        $data = json_decode(@file_get_contents('php://input'), true);

        if($guid)
        {
            if($data !== array())
            {
                array_walk_recursive($data, 'Core::utfDe');
                $hardware = new Hardware();
                $hardware->attributes = $data['Hardware'];
                $hardware->guid = $guid; //@todo Проверка на гуид
                $hardware->save();
            }
        }
    }
    public function actionNewPhoto($guid)
    {
        header("Access-Control-Allow-Origin: *");

        $data = json_decode(@file_get_contents('php://input'), true);
        $dir = 'photos/';

        $filename = md5($data['Photo']['content']).'.jpg';

        file_put_contents($dir.$filename, '');

        $this->base64_to_file($data['Photo']['content'], $dir.$filename);

        if($guid)
        {
            if($data !== array())
            {
                array_walk_recursive($data, 'Core::utfDe');
                $photo = new Photo();
                $photo->filename = $filename;
                $photo->guid = $guid; //@todo Проверка на гуид
                $photo->save();
                var_dump($photo->getErrors());
            }
        }
    }
    public function actionEditClaim($id)
    {
        header("Access-Control-Allow-Origin: *");
        $data = json_decode(@file_get_contents('php://input'), true);
        $claim = Claim::model()->findByPk($id);

        if($id)
        {
            if($data !== array())
            {
                array_walk_recursive($data, 'Core::utfDe');
                $claim->attributes = $data['Claim'];
                $claim->save();
                CVarDumper::dump($claim->getErrors(), 100, false);
            }
        }
    }
    public function actionEditHardware($id)
    {
        header("Access-Control-Allow-Origin: *");
        $data = json_decode(@file_get_contents('php://input'), true);
        $hardware = Hardware::model()->findByPk($id);

        if($id)
        {
            if($data !== array())
            {
                array_walk_recursive($data, 'Core::utfDe');
                $hardware->attributes = $data['Hardware'];
                $hardware->save();
            }
        }
    }
}