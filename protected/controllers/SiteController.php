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
    public function actionClaims($guid = null, $render = true)
    {
        header("Access-Control-Allow-Origin: *");
        $response = array();

        if($guid === null)
            $claims = Claim::model()->findAll();
        else
            $claims = Claim::model()->findAllByAttributes(array('guid' => $guid));

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
        $claim = json_decode(@file_get_contents('php://input'), true);

        if($guid)
        {
            if($claim !== array())
            {
                $claim = new Claim();
                die('died');
                $claim->attributes = $claim['Claim'];
                $claim->created = new CDbExpression('GETDATE()');
                $claim->status = 1;
                if(!$claim->save())
                {
                    CVarDumper::dump($claim->getErrors(), 1000, false);
                }
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