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
        $response = array(
            'claims' => $this->actionClaims($guid, false),
            'hardware' => $this->actionHardware($guid, false),
            'photos' => $this->actionPhotos($guid, false),
        );
        print json_encode($response);
	}
    public function actionEntities()
    {
        header("Access-Control-Allow-Origin: *");
        $response = array();
        $entities = Entity::model()->findAll();
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
        if($guid)
        {
            $criteria->condition .= 'guid = '.$guid;
            if(isset($data['search']))
                $criteria->condition .= " AND name LIKE '%".$data['search']."%'";
            else
                echo $data['search'];
        }

        $claims = Claim::model()->findAll($criteria);

        foreach($claims as $claim)
            $response[] = array('id' => $claim->id, 'name' => $claim->name, 'error' => $claim->error, 'guid' => $claim->guid, 'status' => $claim->status, 'created' => $claim->created, 'closed' => $claim->closed, 'comment' => $claim->comment);

        array_walk_recursive($response, 'Core::utfEn');

        if($render)
            echo json_encode($response);
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

        $this->base64_to_jpeg($data['Photo']['content'], $dir.$filename);

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
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-Type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}


    public function base64_to_jpeg($base64_string, $output_file)
    {
        $ifp = fopen($output_file, "wb");

        $data = explode(',', $base64_string);

        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp);

        return $output_file;
    }
	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}