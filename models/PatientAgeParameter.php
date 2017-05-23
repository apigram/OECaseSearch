<?php

/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 22/05/2017
 * Time: 1:42 PM
 */
class PatientAgeParameter extends Parameter
{
    public $textValue;
    public $minValue;
    public $maxValue;

    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'age';
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = array('textValue, minValue, maxValue', 'safe');
        $rules[] = array('textValue, minValue, maxValue', 'values');
        return $rules;
    }

    public function attributeNames()
    {
        $attrs = parent::attributeNames();
        $attrs[] = 'textValue';
        $attrs[] = 'minValue';
        $attrs[] = 'maxValue';
        return $attrs;
    }

    public function attributeLabels()
    {
        return array(
            'textValue' => 'Value',
            'minValue' => 'Minimum Value',
            'maxValue' => 'Maximum Value'
        );
    }

    /**
     * Validator to validate parameter values for specific operators.
     * @param $attribute Attribute being validated.
     */
    public function values($attribute)
    {
        $label = $this->attributeLabels()[$attribute];
        if ($attribute === 'minValue' or $attribute === 'maxValue')
        {
            if ($this->operation === 'BETWEEN' and empty($this->attributes[$attribute]))
            {
                $this->addError($attribute, "$label must be specified.");
            }
        }
        else
        {
            if ($this->operation !== 'BETWEEN' and empty($this->attributes[$attribute]))
            {
                $this->addError($attribute, "$label must be specified.");
            }
        }
    }

    public function getKey()
    {
        return 'Patient Age';
    }

    public function renderParameter($id)
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
        echo CHtml::activeDropDownList($this, "[$id]operation", $ops, array('onchange' => 'refreshValues(this)', 'prompt' => 'Select One...'));

        if ($this->operation === 'BETWEEN') {
            echo '<div class="dual-value" style="display: inline-block;"> ';
            echo CHtml::activeTextField($this, "[$id]minValue", array('placeholder' => 'min'));
            echo CHtml::activeTextField($this, "[$id]maxValue", array('placeholder' => 'max'));
            echo '</div> ';
            echo '<div class="single-value" style="display: none;"> ';
            echo CHtml::activeTextField($this, "[$id]textValue");
            echo '</div>';
        }
        else
        {
            echo '<div class="dual-value" style="display: none;"> ';
            echo CHtml::activeTextField($this, "[$id]minValue", array('placeholder' => 'min'));
            echo CHtml::activeTextField($this, "[$id]maxValue", array('placeholder' => 'max'));
            echo '</div> ';
            echo '<div class="single-value"> ';
            echo CHtml::activeTextField($this, "[$id]textValue");
            echo '</div>';
        }
        echo CHtml::link('Remove', '#', array('onclick'=> 'removeParam(this)', 'class' => 'remove-link'));
    }

    public function query($searchProvider)
    {
        if ($searchProvider->isSql())
        {
            if ($this->operation === 'BETWEEN') {
                return ('SELECT id FROM patient WHERE TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE()))'
                    . ' ' . $this->operation
                    . ' ' . $this->minValue . ' AND ' . $this->maxValue);
            }
            else
            {
                return ('SELECT id FROM patient WHERE TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE()))'
                    . ' ' . $this->operation
                    . ' ' . $this->textValue);
            }

        }
        else
        {
            return null; // Not yet implemented.
        }
    }
}