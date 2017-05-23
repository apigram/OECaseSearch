<?php
/* @var $this CaseSearchController */

$this->breadcrumbs = array(
    $this->module->id,
);
?>
<h1>Case Search</h1>

<?php $form = $this->beginWidget('CActiveForm', array(
    'action' => Yii::app()->createUrl($this->route),
    'method' => 'post',
    'id' => 'case-search-form',
));?>

<?php echo $form->errorSummary($params); ?>
<div id="param-list">
  <?php if (isset($params)):
      foreach ($params as $id => $param):
        $this->renderPartial('parameter_form', array(
            'model' => $params[$id],
            'id' => $id
        ));
    endforeach;
  endif; ?>
</div>
<br/>

<div class="new-param">
  <?php echo CHtml::dropDownList('Add Parameter: ', 'Select One...', $paramList, array('id' => 'param')); ?>
  <?php echo CHtml::button('Add Parameter', array('id' => 'add-param')) ?>
</div>
<div class="search-actions">
  <?php echo CHtml::submitButton('Search');?>
</div>

<?php $this->endWidget();?>

<?php if (isset($patients))
{
  foreach ($patients as $id => $patient)
  {
    $this->renderPartial('search_results', array(
        'model' => $patient
    ));
  }
}
?>

<?php
Yii::app()->clientScript->registerScript('addParam', "
$('#add-param').click(function() {
  var id = $('.parameter').last().attr('id') ? $('.parameter').last().attr('id') : 0;
  $.ajax({
    url: '". Yii::app()->controller->createUrl('caseSearch/addParameter') . "?param=' + $('#param').val() + '&id=' + id,
    type: 'GET',
    success: function(response) {
      $('#param-list').append(response);
      id++;
  }
});
});");
