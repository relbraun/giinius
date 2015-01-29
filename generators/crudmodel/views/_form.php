<?php
/* @var $this CrudmodelGenerator */
/* @var $form CActiveForm */
/* @var $field GiiniusBuilder */

$form = new CActiveForm($this);
?>
<li>
    <div class="attribute-wrapper">
        <a class="x-remover" href="javascript:;">x</a>


            <h4><?php echo $field->attribute ?></h4>
            <?php echo $form->hiddenField($field, "[$id]attribute"); ?>



        <div class="row">
            <?php echo $form->labelEx($field, 'label'); ?>
            <?php echo $form->textField($field, "[$id]label"); ?>
            <div class="tooltip">

            </div>
            <?php echo $form->error($field, 'label'); ?>
        </div>
            <?php if($field->field_type !='AI'): ?>

        <div class="row" data-id="<?php echo $id ?>">
            <?php echo $form->labelEx($field, 'field_type'); ?>
            <?php echo $form->dropDownList($field, "[$id]field_type", $field->getFieldtypeOptions(), array('class' => 'field-type-selector')); ?>
            <div class="tooltip">

            </div>
            <?php $selectorActive = $field->field_type=='dropdown' ? ' active' :''; ?>
            <?php echo $form->error($field, 'field_type'); ?>
            <div id="dropdown-section-<?php echo $id ?>" data-id="<?php echo $id ?>" class="dropdown-section<?php echo $selectorActive ?>">
                <div class="dropdown-selector-section">
                    <?php echo $form->labelEx($field, 'value_source'); ?>
                    <?php echo $form->radioButtonList($field, "[$id]value_source", $field->valueSourceOptions, array('class' => 'value-source-radio', 'labelOptions' => array('class' => 'inline'))); ?>
                </div>
                <?php $listActive = $field->field_type=='dropdown' && $field->value_source=='from_list' ? ' active' :'';
                      $tableActive= $field->field_type=='dropdown' && $field->value_source=='from_table' ? ' active' :''; ?>
                <div class="from-list-section<?php echo $listActive ?>">
                    <div class="container">
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

                <div class="from-table-section<?php echo $tableActive ?>">
                    <div class="container">
                        <div class="span-5">
                            <?php echo $form->labelEx($field, 'model_source'); ?>
                            <?php echo $form->textField($field, "[$id]model_source",array('class'=>'model-source-text')); ?>
                            <div class="tooltip">

                            </div>
                            <?php echo $form->error($field, 'model_source'); ?>
                        </div>
                        <div class="span-5">
                            <?php echo $form->labelEx($field, 'column_key'); ?>
                            <?php echo $form->dropDownList($field, "[$id]column_key",$columns,array('class'=>'column-key-dropdown')); ?>
                            <div class="tooltip">

                            </div>
                            <?php echo $form->error($field, 'column_key'); ?>
                        </div>
                        <div class="span-5">
                            <?php echo $form->labelEx($field, 'column_value'); ?>
                            <?php echo $form->dropDownList($field, "[$id]column_value",$columns,array('class'=>'column-value-dropdown')); ?>
                            <div class="tooltip">

                            </div>
                            <?php echo $form->error($field, 'column_value'); ?>
                        </div>
                    </div>
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
            <?php endif; ?>

        <?php echo $form->hiddenField($field, "[$id]sorter"); ?>
    </div>
</li>