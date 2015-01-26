<?php
/* @var $this CrudmodelGenerator */
/* @var $form CActiveForm */
/* @var $field GiiniusBuilder */

?>
<div class="giinius-form-wrapper form">
    <ul>
    <?php
    $id=1;
    foreach ($models as $field) {
            $id = $field->id ? $field->id : $id;
            $this->renderPartial('_form', array('field' => $field, 'id' => $id));
            $id++;
        }
    ?>
    </ul>
</div><!-- form -->
<script>
    (function($){
        $('.x-remover').click(function(){
            $(this).parent().remove();
        });
        $('.giinius-form-wrapper ul').sortable({
            placeholder: "ui-state-highlight"
        });
        $('.field-type-selector').change(function(){
            if($(this).val()=='dropdown'){
                var $section=$(this).parents('.attribute-wrapper').find('.dropdown-selector-section');
                $section.addClass('active');
                $section.slideDown(300);
            }
            else{
                var $section=$(this).parents('.attribute-wrapper').find('.dropdown-selector-section');
                $section.removeClass('active');
                $section.slideUp(300);
            }
        });
        $('.value-source-radio').change(function(){
            if($(this).is(':checked')){
                var $tableSection=$(this).parents('.attribute-wrapper').find('.from-table-section');
                var $listSection=$(this).parents('.attribute-wrapper').find('.from-list-section');
                if($(this).val()=='from_table'){
                    $listSection.slideUp(300);
                    $tableSection.slideDown(300);
                }
                if($(this).val()=='from_list'){
                    $listSection.slideDown(300);
                    $tableSection.slideUp(300);
                }
            }
        });
    })(jQuery);
</script>
