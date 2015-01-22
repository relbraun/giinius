<?php

/* @var $this CrudmodelGenerator */
/* @var $form CActiveForm */
/* @var $field GiiniusBuilder */

$form = new CActiveForm($this);
?>
<li>
<div class="attribute-wraper">
    <a class="x-remover" href="javascript:;">x</a>
    <div class="row">
		<?php echo $form->labelEx($field,'attribute'); ?>
		<?php echo $form->textField($field,"[$id]attribute"); ?>
		<div class="tooltip">

		</div>
		<?php echo $form->error($field,'attribute'); ?>
	</div>



    <div class="row">
		<?php echo $form->labelEx($field,'field_type'); ?>
		<?php echo $form->dropDownList($field,"[$id]field_type", $field->getFieldtypeOptions()); ?>
		<div class="tooltip">

		</div>
		<?php echo $form->error($field,'field_type'); ?>
	</div>

    <div class="row">
		<?php echo $form->labelEx($field,'css'); ?>
		<?php echo $form->textField($field,"[$id]css"); ?>
		<div class="tooltip">

		</div>
		<?php echo $form->error($field,'css'); ?>
	</div>



    <div class="row">
		<?php echo $form->labelEx($field,'options'); ?>
		<?php echo $form->textArea($field,"[$id]options"); ?>
		<div class="tooltip">

		</div>
		<?php echo $form->error($field,'options'); ?>
	</div>



    <div class="row">
		<?php echo $form->labelEx($field,'label'); ?>
		<?php echo $form->textField($field,"[$id]label"); ?>
		<div class="tooltip">

		</div>
		<?php echo $form->error($field,'label'); ?>
	</div>
    <?php echo $form->hiddenField($field,"[$id]sorter"); ?>
</div>
</li>