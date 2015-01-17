<?php

/**
 *
 * @return CWebApplication
 */
function app()
{
    return Yii::app();
}

Yii::import('giin.generators.crudmodel.GiiniusBuilder');

class CrudmodelGenerator extends CCodeGenerator
{

    public $codeModel = 'giin.generators.crudmodel.CrudmodelCode';

    public function actionFormBuilder($model_name)
    {
        $models = GiiniusBuilder::model()->findAllByAttributes(array('model' => $model_name));
        if (!$models) {
            $this->renderPartial('error');
        }
        foreach ($models as $field) {
            $this->render('formBuilder', array('field' => $field));
        }
        $script = <<<EOD
   $('')
EOD;
        $cs = app()->getClientScript();
        $cs->registerScript(__FILE__, $script);
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

}