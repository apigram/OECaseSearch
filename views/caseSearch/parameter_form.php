<?php
/* @var $this ParameterController */
/* @var $model CaseSearchParameter */
/* @var $form CActiveForm */
?>

<div id="<?php echo $id;?>" class="parameter box admin">
  <div class="row data-row">
    <?php $model->renderParameter($id); ?>
    <?php echo CHtml::activeHiddenField($model, "[$id]id"); ?>
    <div class="large-1 column">
      <?php echo CHtml::link('Remove', '#', array('onclick'=> 'removeParam(this)', 'class' => 'remove-link')); ?>
    </div>
  </div>
</div><!-- form -->