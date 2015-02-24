<?php
Yii::setPathOfAlias('giin', __DIR__);


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
        if($models){
            usort($models, function($a,$b){
                if($a->sorter==$b->sorter)
                    return 0;
                return $a->sorter > $b->sorter ? 1 :-1;
            });
        }
        else {
            if($pos=strrpos($model_name,'.')!==false){
                Yii::import($model_name);
            }
            $modelClassName=substr($model_name,  $pos);
            if(!class_exists($modelClassName)){
                $this->renderPartial('error');
                return;
            }

            $m=new $modelClassName;
            if(!$m){
                 $this->renderPartial('error');
                 return;
            }
            $tbl=Yii::app()->db->schema->getTable($m->tableName());
            $columns = $tbl->columns;
            foreach($columns as $col){

                 $mod=new GiiniusBuilder;
                 $mod->attribute=$col->name;
                 if($col->autoIncrement){
                     $mod->field_type='AI';
                 }
                 $models[]=$mod;

            }
        }
        $this->renderPartial('formBuilder', array('models' => $models));
    }

    /**
     * @param $model CActiveRecord
     */
    public function actionAjaxFillColumns($model)
    {
        $tableName=$model::model()->tableName();
        /** @var CDbConnection $db */
        $db=Yii::app()->db;
        $table=$db->getSchema()->getTable($tableName);
        $s='';
        foreach($table->columnNames as $col){
            $s.="<option vlaue='$col'>$col</option>\n";
        }
        echo $s;
        Yii::app()->end();
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
            $sql =
                    "CREATE TABLE IF NOT EXISTS `$tbl_name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(255) DEFAULT NULL,
  `attribute` varchar(255) DEFAULT NULL,
  `field_type` varchar(255) DEFAULT NULL,
  `show_in_table` tinyint(1) DEFAULT 1,
  `search` tinyint(1) DEFAULT 1,
  `value_source` varchar(255) DEFAULT NULL,
  `model_source` varchar(255) DEFAULT NULL,
  `column_key` varchar(255) DEFAULT NULL,
  `column_value` varchar(255) DEFAULT NULL,
  `const_value` varchar(255) DEFAULT NULL,
  `update` tinyint(1) DEFAULT NULL,
  `css` varchar(255) DEFAULT NULL,
  `options` text,
  `use_numerical` tinyint(1) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `placeholder` tinyint(1) DEFAULT NULL,
  `use_map` tinyint(1) DEFAULT NULL,
  `sorter` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `KEY_giinius_builder` (`model`,`attribute`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"
                ;

            $command = $db->createCommand($sql);
            if (!$command->execute())
                Yii::trace('The system didn\'t succeded to build the new table: ' . $tbl_name);

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
                $counter=0;
		if(isset($_POST[$modelClass]))
		{
			$model->attributes=$_POST[$modelClass];
			$model->status=CCodeModel::STATUS_PREVIEW;

                        if(isset($_POST['GiiniusBuilder']) ){
                            $post=$_POST['GiiniusBuilder'];
                            $tabModel=new $model->model;
                            $table=Yii::app()->db->schema->getTable($tabModel->tableName());
                            $columns=$table->columns;
                            foreach($post as $i=>$builder){
                                $giinius = GiiniusBuilder::model()->findByColumnModel($model->model, $builder['attribute']);
                                if($giinius){
                                    $giinius->attributes=$builder;
                                    $giinius->model=$model->model;
                                }
                                else{
                                    $giinius=new GiiniusBuilder;
                                    $giinius->attributes=$builder;
                                    $giinius->model=$model->model;
                                }
                                $giinius->sorter=$counter;
                                $counter++;
                                $saved = $giinius->save();
                                if($saved){
                                    unset($columns[$giinius->attribute]);
                                }
                                else{
                                    Yii::log(CVarDumper::dumpAsString($giinius->errors,10,false),CLogger::LEVEL_ERROR,'giinius');
                                }
                            }
                            foreach ($columns as $colName => $column) {
                                $giinius=new GiiniusBuilder;
                                $giinius->autoFillFieldType($model->model,$column);
                                $giinius->sorter=$counter;
                                if(!$giinius->save()){
                                    Yii::log(CVarDumper::dumpAsString($giinius->errors,10,false),CLogger::LEVEL_ERROR,'giinius');
                                }

                                $counter++;
                            }
                        }
            $giiniusBuilder= GiiniusBuilder::model()->findAllByAttributes(array('model' => $model->model));
            $model->builder=$giiniusBuilder;
			if($model->validate())
			{
				$model->saveStickyAttributes();
				$model->prepare();
			}
		}
		return $model;
	}

}