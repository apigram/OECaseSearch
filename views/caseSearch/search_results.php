<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 23/05/2017
 * Time: 2:14 PM
 */
?>

<div class="result">
    <?php echo CHtml::link($model->contact->last_name . ', ' . $model->contact->first_name . ' (' . $model->gender . ')', '#'); ?>
</div>

