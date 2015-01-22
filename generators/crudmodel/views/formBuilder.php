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
    })(jQuery);
</script>
