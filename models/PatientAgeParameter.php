<?php

/**
 * Created by PhpStorm.
 * User: andre
 * Date: 22/05/2017
 * Time: 1:42 PM
 */
class PatientAgeParameter extends Parameter
{
    public function getKey()
    {
        return 'Patient Age';
    }

    public function renderParameter()
    {
        $ops = array(
            '<' => '<',
            '>' => '>',
            '>=' => '>=',
            '<=' => '<=',
            '=' => '=',
            '!=' => '!=',
            'BETWEEN' => 'between'
        );
        echo CHtml::label($this->getKey(), false);
        echo CHtml::activeDropDownList($this, 'operation', $ops, array('onchange' => 'refreshValues(this)', 'prompt' => 'Select One...'));


        echo '<div class="single-value"> ';
        echo CHtml::activeTextField($this, 'textValue');
        echo '</div>';

        echo '<div class="dual-value"> ';
        echo CHtml::activeTextField($this, 'minValue', array('placeholder' => 'min'));
        echo CHtml::activeTextField($this, 'maxValue', array('placeholder' => 'max'));
        echo '</div> ';

        echo CHtml::link('Remove', '#', array('onclick'=> 'removeParam(this)'));
    }
}