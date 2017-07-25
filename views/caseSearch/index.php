<?php
/* @var $this CaseSearchController
 * @var $fixedParams array
 * @var $params array
 * @var $paramList array
 * @var $form CActiveForm
 */
$this->pageTitle = 'OpenEyes - Case Search'
?>
<h1 class="badge">Case Search</h1>

<div class="row">
  <div class="large-10 column">
    <div class="form">
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'search-form',
        )); ?>
        <?php if (!empty($form->errorSummary($params))): ?>
          <div class="box admin">
              <?php echo $form->errorSummary($params); ?>
          </div>
        <?php endif; ?>

      <div id="param-list">
          <?php if (isset($params)):
              foreach ($params as $id => $param):
                  $this->renderPartial('parameter_form', array(
                      'model' => $param,
                      'id' => $id,
                  ));
              endforeach;
          endif; ?>
      </div>
      <div class="box generic">
          <?php foreach ($fixedParams as $id => $param):
              $this->renderPartial('fixed_parameter_form', array(
                  'model' => $param,
                  'id' => $id,
              ));
          endforeach; ?>
      </div>

      <div class="box generic">
        <div class="new-param">
            <?php echo CHtml::dropDownList('Add Parameter: ', 'Select One...', $paramList, array('id' => 'param')); ?>
            <?php echo CHtml::button('Add Parameter',
                array('id' => 'add-param', 'class' => 'button secondary small')) ?>
        </div>
        <div class="search-actions">
            <?php echo CHtml::submitButton('Search'); ?>
            <?php echo CHtml::button('Clear', array('id' => 'clear-search', 'class' => 'button event-action cancel')) ?>
        </div>

          <?php $this->endWidget(); ?>
      </div>

      <div id="results">
          <?php if (isset($patients)) {
              $this->widget('zii.widgets.CListView', array(
                      'dataProvider' => $patients,
                      'itemView' => 'search_results',
                      'emptyText' => '<div class="box generic">No patients found</div>',
                      'summaryCssClass' => 'box generic',
                      'pager' => $this->widget('LinkPager', array(
                          'pages' => $patients->getPagination(),
                          'maxButtonCount' => 15,
                          'cssFile' => false,
                          'selectedPageCssClass' => 'current',
                          'hiddenPageCssClass' => 'unavailable',
                          'htmlOptions' => array(
                              'class' => 'pagination',
                          ),
                      )),
                  )
              );
          }
          ?>
      </div>
    </div>
  </div>

    <?php
    Yii::app()->clientScript->registerScript('addParam', "
$('#add-param').click(function() {
  var id = $('.parameter').last().attr('id') ? ($('.parameter').last().attr('id')) : -1;
  $.ajax({
    url: '" . Yii::app()->controller->createUrl('caseSearch/addParameter') . "?param=' + $('#param').val() + '&id=' + ++id,
    type: 'GET',
    success: function(response) {
      $('#param-list').append(response);
    }
  });
});
$('#clear-search').click(function() {
  $.ajax({
    url: '" . Yii::app()->controller->createUrl('caseSearch/clear') . "',
    type: 'GET',
    success: function() {
      $('#results').children().remove();
      $('#param-list').children().remove();
    }
  });
});");
    Yii::app()->clientScript->registerScriptFile($this->module->getAssetsUrl() . '/js/QueryBuilder.js');
    Yii::app()->clientScript->registerCssFile($this->module->getAssetsUrl() . '/css/QueryBuilder.css');

    $assetPath = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'), false, -1);
    Yii::app()->clientScript->registerScriptFile($assetPath . '/js/toggle-section.js');
    ?>

