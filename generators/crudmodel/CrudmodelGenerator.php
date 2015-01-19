<?php
Yii::setPathOfAlias('giin', __DIR__);
function app()
{
    return Yii::app();
}

require_once 'GiiniusBuilder.php';
/**
 *
 * @return CWebApplication
 */


class CrudmodelGenerator extends CCodeGenerator
{
    private $_assetsUrl;

    public $codeModel = 'giin.CrudmodelCode';

    public function actionFormBuilder($model_name)
    {
        $models = GiiniusBuilder::model()->findAllByAttributes(array('model' => $model_name));
        if (!$models) {
            if(!Yii::autoload($model_name))
                return;
            $m=new $model_name;
            if(!$m){
                 $this->render('error');
                 return;
            }
            $tbl=Yii::app()->db->schema->getTable($m->tableName());
            foreach($tbl->columns as $col){
                if(!$col->autoIncrement){
                 $mod=new GiiniusBuilder;
                 $mod->attribute=$col->name;
                 $models[]=$mod;
                }
            }
        }
        $this->renderPartial('formBuilder', array('models' => $models));
    }

    public function actionIndex()
	{
		$model=$this->prepare();
		if($model->files!=array() && isset($_POST['generate'], $_POST['answers']))
		{
			$model->answers=$_POST['answers'];
			$model->status=$model->save() ? CCodeModel::STATUS_SUCCESS : CCodeModel::STATUS_ERROR;
		}

		$this->render('index',array(
			'model'=>$model,
		));
	}

        public function getAssetsUrl()
	{
		if($this->_assetsUrl===null)
			$this->_assetsUrl=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('giin.assets'));
		return $this->_assetsUrl;
	}

	/**
	 * @param string $value the base URL that contains all published asset files of gii.
	 */
	public function setAssetsUrl($value)
	{
		$this->_assetsUrl=$value;
	}

    protected function buildDbTbl()
    {
        /** @var CDbConnection $db */
        $db = Yii::app()->db;
        $tbl_name = 'giinius_builder';

        if (!in_array($tbl_name, $db->getSchema()->getTableNames())) {
            $schema = $db->getSchema();
            $sql = $schema->createTable($tbl_name, array(
                'id' => 'pk',
                'model' => 'string',
                'attribute' => 'string',
                'field_type' => 'string',
                'css' => 'string',
                'options' => 'text',
                'label' => 'string',
                'placeholder' => 'boolean',
            ));
            $index = $schema->createIndex('KEY_' . $tbl_name, $tbl_name, 'model,attribute', true);
            $command = $db->createCommand($sql);
            if (!$command->execute())
                throw new CDbException('The system didn\'t succeded to build the new table: ' . $tbl_name);
            if (!$db->createCommand($index)->execute())
                throw new CException('The system didn\'t succeded to build the new table: ' . $tbl_name);
            return true;
        }
        return true;
    }

    protected function beforeAction($action)
    {
        if ($this->buildDbTbl())
            return parent::beforeAction($action);
    }

    /**
	 * Prepares the code model.
	 */
	protected function prepare()
	{
		if($this->codeModel===null)
			throw new CException(get_class($this).'.codeModel property must be specified.');
		$modelClass=Yii::import($this->codeModel,true);
		$model=new $modelClass;
		$model->loadStickyAttributes();
		if(isset($_POST[$modelClass]))
		{
			$model->attributes=$_POST[$modelClass];
			$model->status=CCodeModel::STATUS_PREVIEW;
                        if(isset($_POST['GiiniusBuilder'])){
                            $model->builder=$_POST['GiiniusBuilder'];
                            foreach($model->builder as $i=>$builder){
                                $giinius=  GiiniusBuilder::model()->findByPk($i);
                                if($giinius){
                                    $giinius->attributes=$builder;
                                    $giinius->model=$model->model;
                                }
                                else{
                                    $giinius=new GiiniusBuilder;
                                    $giinius->attributes=$builder;
                                    $giinius->model=$model->model;
                                }
                                if(!$giinius->save()){
                                    Yii::log('giinius.giiniusBuilder',$giinius->model.":{$giinius->attribute}".' did not save');
                                }
                            }
                        }
			if($model->validate())
			{
				$model->saveStickyAttributes();
				$model->prepare();
			}
		}
		return $model;
	}

}