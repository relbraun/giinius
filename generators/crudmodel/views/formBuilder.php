<?php
/* @var $this CrudmodelGenerator */
/* @var $form CActiveForm */
/* @var $field GiiniusBuilder */

?>
<div class="giinius-form-wrapper form">

    <?php
    foreach ($models as $field) {
            $this->renderPartial('_form', array('field' => $field));
        }
    ?>

</div><!-- form -->
<script>
    (function($){
        $('.x-remover').click(function(){
            $(this).parent().remove();
        });
    })(jQuery);
</script>
