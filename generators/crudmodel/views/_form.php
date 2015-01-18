<?php

/* @var $this CrudmodelGenerator */
/* @var $form CActiveForm */
/* @var $field GiiniusBuilder */

$form = new CActiveForm($this);
?>
<div class="attribute-wraper">
    <a class="x-remover" href="javascript:;">x</a>
    <div class="row">
		<?php echo $form->labelEx($field,'attribute'); ?>
		<?php echo $form->textField($field,'[]attribute'); ?>
		<div class="tooltip">

		</div>
		<?php echo $form->error($field,'attribute'); ?>
	</div>
</div>