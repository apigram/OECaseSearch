<?php

/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 22/05/2017
 * Time: 1:42 PM
 */
class PatientAgeParameter extends CaseSearchParameter
{
    /**
     * @var integer Represents a single value
     */
    public $textValue;

    /**
     * @var integer Represents a minimum value.
     */
    public $minValue;

    /**
     * @var integer Represents a maximum value.
     */
    public $maxValue;

    /**
     * PatientAgeParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario Model scenario.
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'age';
    }

    /**
     * This has been overridden to allow additional rules surrounding the operator and value fields.
     * @return array Complete array of validation rules.
     */
    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('textValue, minValue, maxValue', 'safe'),
            array('textValue, minValue, maxValue', 'numerical', 'min' => 0),
            array('textValue, minValue, maxValue', 'values'),
        ));
    }

    /**
     * This has been overridden to add additional attributes.
     * @return array Complete array of attribute names.
     */
    public function attributeNames()
    {
        return array_merge(parent::attributeNames(), array(
            'textValue',
            'minValue',
            'maxValue',
        ));
    }

    /**
     * Attribute labels for display purposes.
     * @return array Attribute key/value pairs.
     */
    public function attributeLabels()
    {
        return array(
            'textValue' => 'Value',
            'minValue' => 'Minimum Value',
            'maxValue' => 'Maximum Value',
            'id' => 'ID'
        );
    }

    /**
     * Validator to validate parameter values for specific operators.
     * @param $attribute string Attribute being validated.
     */
    public function values($attribute)
    {
        $label = $this->attributeLabels()[$attribute];
        if ($attribute === 'minValue' or $attribute === 'maxValue')
        {
            if ($this->operation === 'BETWEEN' and strlen($this->$attribute) === 0)
            {
                $this->addError($attribute, "$label must be specified.");
            }
        }
        else
        {
            if ($this->operation !== 'BETWEEN' and strlen($this->$attribute) === 0)
            {
                $this->addError($attribute, "$label must be specified.");
            }
        }
    }

    /**
     * @return string "Patient age".
     */
    public function getKey()
    {
        return 'Patient Age';
    }

    /**
     * @param $id integer ID of the parameter for rendering purposes.
     */
    public function renderParameter($id)
    {
        $ops = array(
            '<' => 'Younger than',
            '>' => 'Older than',
            '=' => 'Is',
            '!=' => 'Is not',
            'BETWEEN' => 'Between'
        );
        ?>
        <div class="large-2 column">
            <?php echo CHtml::label($this->getKey(), false); ?>
        </div>
        <div class="large-3 column">
            <?php echo CHtml::activeDropDownList($this, "[$id]operation", $ops, array('onchange' => 'refreshValues(this)', 'prompt' => 'Select One...')); ?>
            <?php echo CHtml::error($this, "[$id]operation"); ?>
        </div>

        <?php if ($this->operation === 'BETWEEN'): ?>

        <div class="dual-value large-3 column" style="display: inline-block;">
            <?php echo CHtml::activeTextField($this, "[$id]minValue", array('placeholder' => 'min')); ?>
            <?php echo CHtml::activeTextField($this, "[$id]maxValue", array('placeholder' => 'max')); ?>
            <?php echo CHtml::error($this, "[$id]minValue"); ?>
            <?php echo CHtml::error($this, "[$id]maxValue"); ?>
        </div>
        <div class="single-value large-3 column" style="display: none;">
            <?php echo CHtml::activeTextField($this, "[$id]textValue"); ?>
            <?php echo CHtml::error($this, "[$id]textValue"); ?>
        </div>

        <?php else: ?>

        <div class="dual-value large-3 column" style="display: none;">
            <?php echo CHtml::activeTextField($this, "[$id]minValue", array('placeholder' => 'min')); ?>
            <?php echo CHtml::activeTextField($this, "[$id]maxValue", array('placeholder' => 'max')); ?>
            <?php echo CHtml::error($this, "[$id]minValue"); ?>
            <?php echo CHtml::error($this, "[$id]maxValue"); ?>
        </div>
        <div class="single-value large-3 column">
            <?php echo CHtml::activeTextField($this, "[$id]textValue"); ?>
            <?php echo CHtml::error($this, "[$id]textValue"); ?>
        </div>
        <div class="large-2 column">
            <p>years of age</p>
        </div>

        <?php endif; ?>
        <?php
    }

    /**
     * Generate the SQL query for patient age.
     * @param $searchProvider SearchProvider The search provider building the query.
     * @return null|string The query string for use by the search provider, or null if not implemented for the specified search provider.
     * @throws CHttpException
     */
    public function query($searchProvider)
    {
        if ($searchProvider->providerID  === 'mysql')
        {
            switch ($this->operation)
            {
                case 'BETWEEN':
                    $op = 'BETWEEN';
                    break;
                case '>':
                    $op = '>';
                    break;
                case '<':
                    $op = '<';
                    break;
                case '>=':
                    $op = '>=';
                    break;
                case '<=':
                    $op = '<=';
                    break;
                case '=':
                    $op = '=';
                    break;
                case '!=':
                    $op = '!=';
                    break;
                default:
                    throw new CHttpException(400, 'Invalid operator specified.');
                    break;
            }

            $queryStr = 'SELECT id FROM patient WHERE TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE()))';
            if ($op === 'BETWEEN') {
                return "$queryStr $op :p_a_min_$this->id AND :p_a_max_$this->id";
            }
            else
            {
                return "$queryStr $op :p_a_value_$this->id";
            }

        }
        else
        {
            return null; // Not yet implemented.
        }
    }

    /**
     * @return array The list of bind values being used by the current parameter instance.
     */
    public function bindValues()
    {
        $bindValues = array();
        if (strlen($this->minValue) !== 0)
        {
            $bindValues["p_a_min_$this->id"] = intval($this->minValue);
        }

        if (strlen($this->maxValue) !== 0)
        {
            $bindValues["p_a_max_$this->id"] = intval($this->maxValue);
        }

        if (strlen($this->textValue) !== 0)
        {
            $bindValues["p_a_value_$this->id"] = intval($this->textValue);
        }

        return $bindValues;
    }

    /**
     * @return string Alias of the patient age parameter. Form is "p_a_#id".
     */
    public function alias()
    {
        return "p_a_$this->id";
    }

    /**
     * @param $joinAlias string Alias of the table.
     * @param $criteria array Criteria used for JOINs.
     * @param $searchProvider SearchProvider The search provider constructing the JOIN SQL statement.
     * @return string The constructed SQL JOIN statement.
     */
    public function join($joinAlias, $criteria, $searchProvider)
    {
        // Construct your JOIN condition here. Generally this involves wrapping the query in a JOIN condition.
        $subQuery = $this->query($searchProvider);
        $query = '';
        $alias = $this->alias();
        foreach ($criteria as $key => $column)
        {
            // if the string isn't empty, the condition is not the first so prepend it with an AND.
            if (!empty($query))
            {
                $query .= ' AND ';
            }
            $query .= "$joinAlias.$key = $alias.$column";
        }

        $query = " JOIN ($subQuery) $alias ON " . $query;

        return $query;
    }
}