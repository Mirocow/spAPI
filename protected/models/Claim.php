<?php

/**
 * This is the model class for table "claim".
 *
 * The followings are the available columns in table 'claim':
 * @property integer $id
 * @property string $name
 * @property string $error
 * @property integer $guid
 * @property integer $status
 * @property string $created
 * @property string $closed
 * @property string $comment
 * @property integer $type
 * @property integer $ride
 * @property integer $verified
 *
 * The followings are the available model relations:
 * @property Entity $gu
 */
class Claim extends CActiveRecord
{
    const  STATUS_OPEN = 1;
    const  STATUS_IN_PROCESS = 2;
    const  STATUS_ON_CONTROL = 3;
    const  STATUS_CLOSED = 4;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'claim';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, error, guid, status, created', 'required'),
			array('guid, status, type, ride, verified', 'numerical', 'integerOnly'=>true),
			array('name, error, comment', 'length', 'max'=>255),
			array('closed', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, error, guid, status, created, closed, comment, type, ride, verified', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'gu' => array(self::BELONGS_TO, 'Entity', 'guid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'error' => 'Error',
			'guid' => 'Guid',
			'status' => 'Status',
			'created' => 'Created',
			'closed' => 'Closed',
			'comment' => 'Comment',
			'type' => 'Type',
			'ride' => 'Ride',
			'verified' => 'Verified',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('error',$this->error,true);
		$criteria->compare('guid',$this->guid);
		$criteria->compare('status',$this->status);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('closed',$this->closed,true);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('ride',$this->ride);
		$criteria->compare('verified',$this->verified);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Claim the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function beforeSave() {
        if(!parent::beforeSave()) return false;
        $oldClaim = Claim::model()->findByPk($this->id);

        if($oldClaim->status != self::STATUS_CLOSED && $this->status == self::STATUS_CLOSED)
            $this->closed = new CDbExpression('GetDate()');

        return true;
    }

    public function getStatusText()
    {
        switch (intval($this->status))
        {
            case 1: return "�������";
            case 2: return "� ��������";
            case 3: return "�� ��������";
            case 4: return "�������";
            default: return "����������";
        }
    }
    public function getErrorText()
    {
        switch (intval($this->error))
        {
            case 1: return "�������� ������";
            case 2: return "�� �������� �������� ������";
            case 3: return "��������� ������� � ���������";
            default: return "-";
        }
    }
    public function getClass()
    {
        switch (intval($this->status))
        {
            case 1: return "danger";
            case 2: return "warning";
            case 3: return "info";
            case 4: return "success";
            default: return "";
        }
    }
}
