<?php
/**
 *
 * @return CWebApplication
 */
function app(){
    return Yii::app();
}
Yii::import('giin.generators.crudmodel.GiiniusBuilder');

class CrudmodelGenerator extends CodeGenerator
{
	public $codeModel='giin.generators.crudmodel.CrudmodelCode';

        public function actionFormBuilder($model_name)
        {
            $models= GiiniusBuilder::model()->findAllByAttributes(array('model'=>$model_name));
            if(!$models){
                $this->renderPartial('error');
            }
            foreach($models as $field){
                $this->render('formBuilder', array('field' => $field));
            }
            $script=<<<EOD
   $('')
EOD;
            $cs=app()->getClientScript();
            $cs->registerScript(__FILE__, $script);
        }
}