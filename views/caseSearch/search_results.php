<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 23/05/2017
 * Time: 2:14 PM
 */
?>

<div class="result box generic">
    <h3 class="box-title"><?php echo CHtml::link($data->contact->last_name . ', ' . $data->contact->first_name . ($data->is_deceased ? ' (Deceased)' : ''), array('/patient/view', 'id' => $data->id), array('target' => '_blank')); ?></h3>
    <div class="row data-row">
        <div class="large-12 column">
            <?php
            $now = new DateTime();
            echo $data->gender . ' ' . '(' . $now->diff(new DateTime($data->dob))->y . ')';
            ?>
        </div>
    </div>
    <?php if ($this->trialContext != null) {

        if (Trial::canUserAccessTrial(Yii::app()->user, $this->trialContext->id, 'update')) {
            $inOtherTrials = TrialPatient::model()->exists(
                'patient_id = :patientId AND trial_id != :trialId',
                array(
                    ':patientId' => $data->id,
                    ':trialId' => $this->trialContext->id
                )
            );

            $inTrial = TrialPatient::model()->exists(
                'patient_id = :patientId AND trial_id = :trialId',
                array(
                    ':patientId' => $data->id,
                    ':trialId' => $this->trialContext->id
                )
            );

            if ($inOtherTrials): ?>
                Already in another Trial
            <?php else: ?>
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
            <?php endif;
        }
    } ?>

    <div class="row data-row">
        <div class="large-12 column">
            <h3 class="box-title">
                Diagnoses <?php echo CHtml::link('Show', '#', array('onclick' => 'event.preventDefault(); toggleDetail(this, ".diagnoses");')) ?></h3>
            <div class="diagnoses detail row data-row">
                <div class="large-12 column">
                    <table>
                        <thead>
                        <tr>
                            <th>Diagnosis</th>
                            <th>Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data->secondarydiagnoses as $diagnosis): ?>
                            <tr>
                                <td><?php echo $diagnosis->disorder->fully_specified_name; ?></td>
                                <td><?php echo $diagnosis->dateText; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="row data-row">
        <div class="large-12 column">
            <h3 class="box-title">
                Medications <?php echo CHtml::link('Show', '#', array('onclick' => 'event.preventDefault(); toggleDetail(this, ".medications");')) ?></h3>
            <div class="medications detail row data-row">
                <div class="large-12 column">
                    <table>
                        <thead>
                        <tr>
                            <th>Medication</th>
                            <th>Administration</th>
                            <th>Date From</th>
                            <th>Date To</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data->medications as $medication): ?>
                            <tr>
                                <td><?php echo $medication->getDrugLabel(); ?></td>
                                <td><?= $medication->dose ?>
                                    <?= isset($medication->route->name) ? $medication->route->name : '' ?>
                                    <?= $medication->option ? "({$medication->option->name})" : '' ?>
                                    <?= isset($medication->frequency->name) ? $medication->frequency->name : '' ?></td>
                                <td><?php echo Helper::formatFuzzyDate($medication->start_date); ?></td>
                                <td><?php echo isset($medication->end_date) ? Helper::formatFuzzyDate($medication->end_date) : ''; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

