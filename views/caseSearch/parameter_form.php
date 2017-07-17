<?php
/* @var $this CaseSearchController */
/* @var $id int*/
/* @var $model CaseSearchParameter */
/* @var $form CActiveForm */
?>

<div id="<?php echo $id;?>" class="<?php echo $model->name; ?> parameter box admin">
  <div class="row field-row">
    <?php $model->renderParameter($id); ?>
    <?php echo CHtml::activeHiddenField($model, "[$id]id"); ?>
    <div class="large-1 column end">
      <p><?php echo CHtml::link('Remove', 'javascript:void(0)', array('onclick'=> 'removeParam(this)', 'class' => 'remove-link')); ?></p>
    </div>
  </div>
</div><!-- form -->