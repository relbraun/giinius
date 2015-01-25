<?php
/* @var $this CrudmodelGenerator */
/* @var $form CActiveForm */
/* @var $field GiiniusBuilder */

$form = new CActiveForm($this);
?>
<li>
    <div class="attribute-wrapper">
        <a class="x-remover" href="javascript:;">x</a>
        <div class="row">
            <h4><?php echo $field->attribute ?></h4>
            <?php echo $form->hiddenField($field, "[$id]attribute"); ?>


        </div>



        <div class="row" data-id="<?php echo $id ?>">
            <?php echo $form->labelEx($field, 'field_type'); ?>
            <?php echo $form->dropDownList($field, "[$id]field_type", $field->getFieldtypeOptions(),array('class'=>'field-type-selector')); ?>
            <div class="tooltip">

            </div>
            <?php echo $form->error($field, 'field_type'); ?>
            <div id="dropdown-section-<?php echo $id ?>" data-id="<?php echo $id ?>" class="container dropdown-section">
                <div class="span-6">
                    <?php echo $form->labelEx($field, 'options'); ?>
                    <?php echo $form->textArea($field, "[$id]options"); ?>
                    <div class="tooltip">

                    </div>
                    <?php echo $form->error($field, 'options'); ?>
                </div>
                <div class="span-6">
                    <?php echo $form->labelEx($field, 'use_numerical'); ?>
                    <?php echo $form->checkBox($field, "[$id]use_numerical"); ?>
                    <div class="tooltip">

                    </div>
                    <?php echo $form->error($field, 'use_numerical'); ?>
                </div>
            </div>
        </div>

        <div class="row">
            <?php echo $form->labelEx($field, 'css'); ?>
            <?php echo $form->textField($field, "[$id]css"); ?>
            <div class="tooltip">

            </div>
            <?php echo $form->error($field, 'css'); ?>
        </div>



        <div class="row">

        </div>



        <div class="row">
            <?php echo $form->labelEx($field, 'label'); ?>
            <?php echo $form->textField($field, "[$id]label"); ?>
            <div class="tooltip">

            </div>
            <?php echo $form->error($field, 'label'); ?>
        </div>
        <?php echo $form->hiddenField($field, "[$id]sorter"); ?>
    </div>
</li>