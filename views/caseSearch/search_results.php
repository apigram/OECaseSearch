<?php
/* @var $data Patient
 * @var $this CaseSearchController
 */

?>

<div class="result box generic">
  <h3 class="box-title"><?php echo CHtml::link($data->contact->last_name
          . ', ' . $data->contact->first_name
          . ($data->is_deceased ? ' (Deceased)' : '')
          . ($data->hasUnconfirmedDiagnoses() ? ' (Unconfirmed)' : ''),
          array('/patient/view', 'id' => $data->id), array('target' => '_blank')); ?></h3>
  <div class="row data-row">
    <div class="large-12 column">
        <?php echo "{$data->getGenderString()} ({$data->getAge()})"; ?>
    </div>
  </div>

    <?php $this->widget('PatientDiagnosesAndMedicationsWidget',
        array(
            'patient' => $data,
        )
    ); ?>

</div>

