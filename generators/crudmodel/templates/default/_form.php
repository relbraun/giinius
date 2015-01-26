<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudmodelCode object
 */
/* @var $this CrudmodelCode */

$inline=$this->inline ? 'form-inline' : '';
$enctype = $this->isEnctypeForm() ? "'enctype' => 'multipart/form-data'," : '';
?>
<?php echo "<?php\n"; ?>
/* @var $this <?php echo $this->getControllerClass(); ?> */
/* @var $model <?php echo $this->getModelClass(); ?> */
/* @var $form CActiveForm */

?>

<div class="form">

<?php echo "<?php \$form=\$this->beginWidget('CActiveForm', array(
	'id'=>'".$this->class2id($this->modelClass)."-form',
	'enableAjaxValidation'=>false,
        'htmlOptions'=>array(
            'class' => '$inline',
            $enctype
         ),
)); ?>\n"; ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo "<?php echo \$form->errorSummary(\$model); ?>\n"; ?>

<?php
foreach($this->_columns as $column)
{
	if($column->autoIncrement)
		continue;
        $builder = isset($column->builder) ? $column->builder : new CBehavior();
        if(isset($builder->field_type) && $builder->field_type !='none'):     ?>
	<div class="form-group" id="<?php echo "{$this->modelClass}--{$column->name}" ?>">
            <?php if(isset($builder->field_type) && $builder->field_type !='hidden'): ?>
		<?php echo "<?php echo ".$this->generateActiveLabel($this->modelClass,$column)."; ?>\n";
                endif; ?>
		<?php echo "<?php ".$this->generateActiveField($this->modelClass,$column)."; ?>\n"; ?>
		<?php echo "<?php echo \$form->error(\$model,'{$column->name}'); ?>\n"; ?>
	</div>

<?php endif;
}
?>
	<div class="form-group">
		<?php echo "<?php echo CHtml::submitButton(\$model->isNewRecord ? 'Create' : 'Save', array('class'=>'btn btn-default')); ?>\n"; ?>
	</div>

<?php echo "<?php \$this->endWidget(); ?>\n"; ?>

</div><!-- form -->