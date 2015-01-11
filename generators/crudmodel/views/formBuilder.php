<?php
/* @var $this CrudmodelGenerator */
/* @var $form CActiveForm */

?>
<div class="giinius-form-wrapper form">
    <?php
        $form =  $this->beginWidget('CActiveForm', array(
            'id' => 'giinius_form_builder',
        ));
    ?>


    <div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>

    <?php $this->endWidget(); ?>

</div><!-- form -->
