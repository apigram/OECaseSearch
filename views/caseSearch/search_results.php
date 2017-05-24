<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 23/05/2017
 * Time: 2:14 PM
 */
?>

<div class="result">
  <div>
    <?php echo CHtml::link($model->contact->last_name . ', ' . $model->contact->first_name, array('/patient/view', 'id' => $model->id)) ; ?>
    <p>
      <?php
      $now = new DateTime();
      echo $model->gender . ' ' . '(' . $now->diff(new DateTime($model->dob))->y . ')';
      ?>
    </p>
  </div>

  <div>
    <strong>Diagnoses</strong><?php echo CHtml::link('Show Diagnoses', '#', array('onclick' => 'toggleDetail(this, ".diagnoses")')) ?>
  </div>
  <div class="diagnoses detail">
    <table>
      <thead>
        <tr>
          <th>Diagnosis</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($model->secondarydiagnoses as $diagnosis): ?>
            <tr>
              <td><?php echo $diagnosis->disorder->fully_specified_name; ?></td>
              <td><?php echo $diagnosis->dateText;?></td>
            </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div>
    <strong>Medications</strong><?php echo CHtml::link('Show Medications', '#', array('onclick' => 'toggleDetail(this, ".medications")')) ?>
  </div>
  <div class="medications detail">
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
      <?php foreach ($model->medications as $medication): ?>
        <tr>
          <td><?php echo $medication->getDrugLabel(); ?></td>
          <td><?= $medication->dose ?>
              <?= isset($medication->route->name) ? $medication->route->name : '' ?>
              <?= $medication->option ? "({$medication->option->name})" : '' ?>
              <?= isset($medication->frequency->name) ? $medication->frequency->name : '' ?></td>
          <td><?php echo Helper::formatFuzzyDate($medication->start_date);?></td>
          <td><?php echo isset($medication->end_date) ? Helper::formatFuzzyDate($medication->end_date) : '';?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

