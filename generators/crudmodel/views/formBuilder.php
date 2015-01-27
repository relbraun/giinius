<?php
/* @var $this CrudmodelGenerator */
/* @var $form CActiveForm */
/* @var $field GiiniusBuilder */
?>
<div class="giinius-form-wrapper form">
    <ul>
        <?php
        $id = 1;
        foreach ($models as $field) {
            $id = $field->id ? $field->id : $id;
            $columns = array();
            if ($field->value_source == 'from_table') {
                $modelSource = $field->model_source;
                if ($modelSource) {
                    $tn = $modelSource::model()->tableName();
                    $table = Yii::app()->db->getSchema()->getTable($tn);
                    $columns = array_combine($table->columnNames, $table->columnNames);
                }
            }
            $this->renderPartial('_form', array('field' => $field, 'id' => $id, 'columns' => $columns));
            $id++;
        }
        ?>
    </ul>
</div><!-- form -->
<?php $columnsAjax = $this->createUrl('ajaxFillColumns'); ?>
<script>
    (function($) {
        $('.x-remover').click(function() {
            $(this).parent().remove();
        });
        $('.giinius-form-wrapper ul').sortable({
            placeholder: "ui-state-highlight"
        });
        $('.field-type-selector').change(function() {
            if ($(this).val() == 'dropdown') {
                var $section = $(this).parents('.attribute-wrapper').find('.dropdown-section');
                $section.addClass('active');
//                $section.slideDown(300);
            }
            else {
                var $section = $(this).parents('.attribute-wrapper').find('.dropdown-section');
                $section.removeClass('active');
//                $section.slideUp(300);
//                $tableSection.slideUp(300);
//                $tableSection.slideUp(300);
            }
        });
        $('.value-source-radio').change(function() {
            if ($(this).is(':checked')) {
                var $tableSection = $(this).parents('.attribute-wrapper').find('.from-table-section');
                var $listSection = $(this).parents('.attribute-wrapper').find('.from-list-section');
                if ($(this).val() == 'from_table') {
//                    $listSection.slideUp(300);
                    $listSection.removeClass('active');
//                    $tableSection.slideDown(300);
                    $tableSection.addClass('active');
                }
                if ($(this).val() == 'from_list') {
//                    $listSection.slideDown(300);
//                    $tableSection.slideUp(300);
                    $listSection.addClass('active');
//                    $tableSection.slideDown(300);
                    $tableSection.removeClass('active');
                }
            }
        });
        $('.model-source-text').change(function() {
            var $self = $(this);
            $keyDropdown = $(this).parents('.from-table-section').find('.column-key-dropdown');
            $valDropdown = $(this).parents('.from-table-section').find('.column-value-dropdown');
            $.ajax({url: '<?php echo $columnsAjax; ?>', data: {model: $self.val()}, method: 'get', success: function(html) {
                    $keyDropdown.html(html);
                    $valDropdown.html(html);
                }});
        });
    })(jQuery);
</script>
