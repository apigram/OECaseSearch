<?php
class PatientAllergyParameter extends CaseSearchParameter
{
    public $textValue;

    /**
    * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
    * @param string $scenario
    */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'patient allergy';
    }

    public function getKey()
    {
        // This is a human-readable value, so feel free to change this as required.
        return 'Patient Allergy';
    }

    /**
    * Override this function for any new attributes added to the subclass. Ensure that you invoke the parent function first to obtain and augment the initial list of attribute names.
    * @return array An array of attribute names.
    */
    public function attributeNames()
    {
        return array_merge(parent::attributeNames(), array(
                'textValue',
            )
        );
    }

    /**
    * Override this function if the parameter subclass has extra validation rules. If doing so, ensure you invoke the parent function first to obtain the initial list of rules.
    * @return array The validation rules for the parameter.
    */
    public function rules()
    {
        return array_merge(parent::rules(), array(
                array('textValue', 'required'),
                array('textValue', 'safe'),
            )
        );
    }

    public function renderParameter($id)
    {
        // Place screen-rendering code here.
        $ops = array(
            '=' => 'Is allergic to',
            '!=' => 'Is not allergic to'
        );
        ?>

        <div class="large-2 column">
            <?php echo CHtml::label($this->getKey(), false); ?>
        </div>
        <div class="large-3 column">
            <?php echo CHtml::activeDropDownList($this, "[$id]operation", $ops, array('prompt' => 'Select One...')); ?>
            <?php echo CHtml::error($this, "[$id]operation"); ?>
        </div>

        <div class="large-5 column">
            <?php
            $html = Yii::app()->controller->widget('zii.widgets.jui.CJuiAutoComplete', array(
                'name' => 'allergy',
                'model' => $this,
                'attribute' => "[$id]textValue",
                'source' => Yii::app()->controller->createUrl('AutoComplete/commonAllergies'),
                'options' => array(
                    'minLength' => 2,
                ),
            ), true);
            Yii::app()->clientScript->render($html);
            echo $html;
            ?>
            <?php echo CHtml::error($this, "[$id]textValue"); ?>
        </div>
        <?php
    }

    /**
    * Generate a SQL fragment representing the subquery of a FROM condition.
    * @param $searchProvider SearchProvider The search provider. This is used to determine whether or not the search provider is using SQL syntax.
    * @return mixed The constructed query string.
     * @throws CHttpException
    */
    public function query($searchProvider)
    {
        // Construct your SQL query here.
        if ($searchProvider->providerID === 'mysql')
        {
            switch ($this->operation)
            {
                case '=':
                    $predicate = "a.name = :p_al_textValue_$this->id";
                    break;
                case '!=':
                    $predicate = "a.id IS NULL OR a.name != :p_al_textValue_$this->id";
                    break;
                default:
                    throw new CHttpException(400, 'Invalid operator specified.');
                    break;
            }

            return "
SELECT p.id 
FROM patient p 
LEFT JOIN patient_allergy_assignment paa
  ON paa.patient_id = p.id
LEFT JOIN allergy a
  ON a.id = paa.allergy_id
WHERE $predicate";
        }
        else
        {
            return null; // Not yet implemented.
        }
    }

    /**
    * Get the list of bind values for use in the SQL query.
    * @return array An array of bind values. The keys correspond to the named binds in the query string.
    */
    public function bindValues()
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        return array(
            "p_al_textValue_$this->id" => $this->textValue,
        );
    }

    /**
    * Generate a SQL fragment representing a JOIN condition to a subquery.
    * @param $joinAlias string The alias of the table being joined to.
    * @param $criteria array An array of join conditions. The ID for each element is the column name from the aliased table.
    * @param $searchProvider SearchProvider The search provider. This is used for an internal query invocation for subqueries.
    * @return string A SQL string representing a complete join condition. Join type is specified within the subclass definition.
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

    /**
    * Get the alias of the database table being used by this parameter instance.
    * @return string The alias of the table for use in the SQL query.
    */
    public function alias()
    {
        return "p_al_$this->id";
    }
}
