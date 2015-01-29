<?php
/**
 * Created by PhpStorm.
 * User: Arielb
 * Date: 1/28/2015
 * Time: 5:06 PM
 */

class Geocomplete extends CInputWidget{

    public function init()
    {
        /** @var CClientScript $cs */
        $cs = Yii::app()->clientScript;
        /** @var CAssetManager $asset */
        $asset = Yii::app()->assetManager;
        $cs->registerScriptFile('http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places');
        $cs->registerCoreScript('jquery');
        $cs->registerCssFile($asset->publish(__DIR__).'/css/style.css');
        $cs->registerScriptFile($asset->publish(__DIR__).'/js/jquery.geocomplete.js',CClientScript::POS_END);
    }

    public function run()
    {
        /** @var CClientScript $cs */
        $cs = Yii::app()->clientScript;
        $result = "<div class='geocomplete-wrapper'>";
        $result .= "<div class='geocomplete-input'>";
        
        $result .= CHtml::activeTextField($this->model, $this->attribute, array('class'=>'geocomplete-textfield form-control'));
        $result .= "</div><div class='geocomplete-map'><div class='geocomplete-map-canvas'> </div>";
        $result .= '</div>';
        $attribute = $this->attribute;
        $value = @$this->model->$attribute;
        $id = CHtml::getIdByName(get_class($this->model).'['.$this->attribute.']');
        $script = "$('#{$id}').geocomplete({
            map: '.geocomplete-map-canvas',
            location: '{$value}'
        });";
        $cs->registerScript(__FILE__, $script);
        echo $result;
    }
}