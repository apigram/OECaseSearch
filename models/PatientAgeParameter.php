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
     * @param string $scenario
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

    /**
     * @return string "Patient age".
     */
    public function getKey()
    {
        return 'Patient Age';
    }

    /**
     * @param The $id of the parameter for rendering purposes.
     */
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
        echo '<div class="large-2 column">';
        echo CHtml::label($this->getKey(), false);
        echo '</div>';
        echo '<div class="large-2 column">';
        echo CHtml::activeDropDownList($this, "[$id]operation", $ops, array('onchange' => 'refreshValues(this)', 'prompt' => 'Select One...'));
        echo CHtml::error($this, "[$id]operation");
        echo '</div>';

        if ($this->operation === 'BETWEEN') {
            echo '<div class="dual-value large-6 column" style="display: inline-block;"> ';
            echo CHtml::activeTextField($this, "[$id]minValue", array('placeholder' => 'min'));
            echo CHtml::activeTextField($this, "[$id]maxValue", array('placeholder' => 'max'));
            echo CHtml::error($this, "[$id]minValue");
            echo CHtml::error($this, "[$id]maxValue");
            echo '</div> ';
            echo '<div class="single-value large-4 column" style="display: none;"> ';
            echo CHtml::activeTextField($this, "[$id]textValue");
            echo CHtml::error($this, "[$id]textValue");
            echo '</div>';
        }
        else
        {
            echo '<div class="dual-value large-6 column" style="display: none;"> ';
            echo CHtml::activeTextField($this, "[$id]minValue", array('placeholder' => 'min'));
            echo CHtml::activeTextField($this, "[$id]maxValue", array('placeholder' => 'max'));
            echo CHtml::error($this, "[$id]minValue");
            echo CHtml::error($this, "[$id]maxValue");
            echo '</div> ';
            echo '<div class="single-value large-6 column"> ';
            echo CHtml::activeTextField($this, "[$id]textValue");
            echo CHtml::error($this, "[$id]textValue");
            echo '</div>';
        }
        echo CHtml::activeHiddenField($this, "[$id]id");
    }

    /**
     * Generate the SQL query for patient age.
     * @param The $searchProvider building the query.
     * @return null|string The query string for use by the search provider, or null if not implemented for the specified search provider.
     * @throws CHttpException
     */
    public function query($searchProvider)
    {
        if ($searchProvider->getProviderID()  === 'mysql')
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
        if (!empty($this->minValue))
        {
            $bindValues["p_a_min_$this->id"] = $this->minValue;
        }

        if (!empty($this->maxValue))
        {
            $bindValues["p_a_max_$this->id"] = $this->maxValue;
        }

        if (!empty($this->textValue))
        {
            $bindValues["p_a_value_$this->id"] = $this->textValue;
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
     * @param $joinAlias of the table.
     * @param A $criteria used for JOINs.
     * @param $searchProvider constructing the JOIN SQL statement.
     * @return string The constructed SQL JOIN statement.
     */
    public function join($joinAlias, $criteria, $searchProvider)
    {
        $subQuery = $this->query($searchProvider);
        $query = '';
        foreach ($criteria as $key => $column)
        {
            // if the string isn't empty, the condition is not the first so prepend it with an AND.
            if (!empty($query))
            {
                $query .= ' AND ';
            }
            $query .= "$joinAlias.$key = p_a_$this->id.$column";
        }

        $query = " JOIN ($subQuery) p_a_$this->id ON " . $query;

        return $query;
    }
}