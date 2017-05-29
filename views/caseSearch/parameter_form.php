<?php
/* @var $this ParameterController */
/* @var $model CaseSearchParameter */
/* @var $form CActiveForm */
?>

<div id="<?php echo $id;?>" class="parameter">
    <?php $model->renderParameter($id); ?>
    <?php echo CHtml::link('Remove', '#', array('onclick'=> 'removeParam(this)', 'class' => 'remove-link')); ?>
</div><!-- form -->