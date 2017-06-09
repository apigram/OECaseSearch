<?php
/* @var $this ParameterController */
/* @var $model CaseSearchParameter */
/* @var $form CActiveForm */
// Fixed parameters use their alias as their ID.
?>

<div id="<?php echo $id; ?>" class="row field-row">
    <?php $model->renderParameter($id); ?>
</div>