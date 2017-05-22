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
<div id="param-list">
  <?php if (isset($params)):
      foreach ($params as $id => $param):
        $this->renderPartial('parameter_form', array(
            'model' => $params[$id]
        ));
    endforeach;
  endif; ?>
</div>
<br/>

<?php echo CHtml::dropDownList('Add Parameter: ', 'Select One...', $paramList, array('id' => 'param')) . CHtml::button('Add Parameter', array('id' => 'add-param')) ?>
<br/><br/>
<?php echo CHtml::submitButton('Search');?>

<?php $this->endWidget();?>

<?php
Yii::app()->clientScript->registerScript('addParam', "
$('#add-param').click(function() {
  $.ajax({
      url: '". Yii::app()->controller->createUrl('caseSearch/addParameter') . "?param=' + $('#param').val(),
    type: 'GET',
    success: function(response) {
    $('#param-list').append(response);
  }
});
});");
