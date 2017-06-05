<?php
/* @var $this ParameterController */
/* @var $model CaseSearchParameter */
/* @var $form CActiveForm */
?>

<div id="<?php echo $id;?>" class="parameter box admin">
  <div class="row field-row">
    <?php $model->renderParameter($id); ?>
    <?php echo CHtml::activeHiddenField($model, "[$id]id"); ?>
    <div class="large-1 column end">
      <?php echo CHtml::link('Remove', '#', array('onclick'=> 'removeParam(this)', 'class' => 'remove-link')); ?>
    </div>
  </div>
</div><!-- form -->