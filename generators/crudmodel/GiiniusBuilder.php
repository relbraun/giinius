<?php

/**
 * This is the model class for table "giinius_builder".
 *
 * The followings are the available columns in table 'giinius_builder':
 * @property integer $id
 * @property string $model
 * @property string $attribute
 * @property string $field_type
 * @property string $css
 * @property string $options
 * @property string $label The field label
 * @property boolean $placeholder Whether show the placeholder
 * @property int $sorter The ordering of the fields
 */
class GiiniusBuilder extends CActiveRecord implements IBehavior
{

    private $_enabled;
    private $_owner;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'giinius_builder';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('model, attribute, field_type', 'required'),
            array('model, attribute', 'length', 'max' => 50),
            array('placeholder, use_numerical', 'numerical', 'integerOnly' => true),
            array('field_type', 'length', 'max' => 25),
            array('css', 'length', 'max' => 100),
            array('options, label', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, model, attribute, field_type, css, options, use_numerical', 'safe', 'on' => 'search'),
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
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'model' => 'Model',
            'attribute' => 'Attribute',
            'field_type' => 'Field Type',
            'css' => 'Css',
            'options' => 'Options',
            'label' => 'Label',
            'placeholder' => 'Placeholder',

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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('model', $this->model, true);
        $criteria->compare('attribute', $this->attribute, true);
        $criteria->compare('field_type', $this->field_type, true);
        $criteria->compare('css', $this->css, true);
        $criteria->compare('options', $this->options, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function attrOptions()
    {

    }

    public function getFieldtypeOptions()
    {
        return array(
            'text' => 'text',
            'password' => 'password',
            'textarea' => 'textarea',
            'checkbox' => 'checkbox',
            'dropdown' => 'dropdown',
            'email' => 'email',
            'hidden' => 'hidden',
            'file' => 'File',
            'multiselect' => 'multiselect',
            'date' => 'date',
            'checkBoxList' => 'checkBoxList',
            'ckeditor' => 'CKEditor',
            'filedrop' => 'DropZone',
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return GiiniusBuilder the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function findByColumnModel($model, $column)
    {
        return self::model()->findByAttributes(array('model' => $model, 'attribute' => $column));
    }

    public function attach($owner)
    {
        $this->_enabled = true;
        $this->_owner = $owner;
    }

    public function detach($owner)
    {

        $this->_owner = null;
        $this->_enabled = false;
    }

    public function getEnabled()
    {
        return $this->_enabled;
    }

    public function setEnabled($value)
    {

        $this->_enabled = $value;
    }

}
