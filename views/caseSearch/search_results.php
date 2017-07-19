<?php
/* @var $data Patient
 * @var $this CaseSearchController
 */

?>

<div class="result box generic">
  <h3 class="box-title"><?php echo CHtml::link($data->contact->last_name
          . ', ' . $data->contact->first_name
          . ($data->is_deceased ? ' (Deceased)' : ''),
          array('/patient/view', 'id' => $data->id), array('target' => '_blank')); ?></h3>
  <div class="row data-row">
    <div class="large-12 column">
        <?php echo "{$data->getGenderString()} ({$data->getAge()})"; ?>
    </div>
  </div>
    <?php if ($this->trialContext !== null &&
        Trial::checkTrialAccess(Yii::app()->user, $this->trialContext->id, UserTrialPermission::PERMISSION_EDIT)
    ) {
        $inOtherTrials = TrialPatient::model()->exists(
            'patient_id = :patientId AND trial_id != :trialId',
            array(
                ':patientId' => $data->id,
                ':trialId' => $this->trialContext->id,
            )
        );

        $inTrial = TrialPatient::model()->exists(
            'patient_id = :patientId AND trial_id = :trialId',
            array(
                ':patientId' => $data->id,
                ':trialId' => $this->trialContext->id,
            )
        );

        ?>
      <a id="add-to-trial-link-<?php echo $data->id; ?>"
         href="javascript:void(0)" <?php echo $inTrial ? 'style="display:none"' : ''; ?>
         onclick="addPatientToTrial(<?php echo $data->id; ?>, <?php echo $this->trialContext->id; ?>)">
        Add to Trial
      </a>
      <a id="remove-from-trial-link-<?php echo $data->id; ?>"
         href="javascript:void(0)" <?php echo !$inTrial ? 'style="display:none"' : ''; ?>
         onclick="removePatientFromTrial(<?php echo $data->id; ?>, <?php echo $this->trialContext->id; ?>)">
        Remove from Trial
      </a>
        <?php
    } ?>

    <?php $this->widget('PatientDiagnosesAndMedicationsWidget',
        array(
            'patient' => $data,
        )
    ); ?>

</div>

