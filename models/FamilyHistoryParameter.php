<?php
class FamilyHistoryParameter extends CaseSearchParameter
{
    public $relative;
    public $side;
    public $condition;

    /**
    * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
    * @param string $scenario
    */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'family_history';
    }

    public function getKey()
    {
        // This is a human-readable value, so feel free to change this as required.
        return 'Family History';
    }

    /**
    * Override this function for any new attributes added to the subclass. Ensure that you invoke the parent function first to obtain and augment the initial list of attribute names.
    * @return array An array of attribute names.
    */
    public function attributeNames()
    {
        return array_merge(parent::attributeNames(), array(
                'relative',
                'side',
                'condition',
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
                array('condition', 'required'),
                array('relative, side, condition', 'safe')
            )
        );
    }

    public function renderParameter($id)
    {
        $ops = array(
            '=' => 'has',
            '!=' => 'does not have'
        );
        $relativeList = FamilyHistoryRelative::model()->findAll();
        $sideList = FamilyHistorySide::model()->findAll();
        $conditionList = FamilyHistoryCondition::model()->findAll();

        $relatives = array();
        $sides = array();
        $conditions = array();

        foreach ($relativeList as $relative) {
            $relatives[$relative->id] = $relative->name;
        }

        foreach ($sideList as $side) {
            if ($side !== 'N/A') {
                $sides[$side->id] = $side->name;
            }
        }

        foreach ($conditionList as $condition) {
            $conditions[$condition->id] = $condition->name;
        }
        // Place screen-rendering code here.

        echo '<div class="large-2 column">';
        echo CHtml::activeDropDownList($this, "[$id]side", $sides, array('empty' => 'Any side'));
        echo '</div>';
        
        echo '<div class="large-2 column">';
        echo CHtml::activeDropDownList($this, "[$id]relative", $relatives, array('empty' => 'Any relative'));
        echo '</div>';

        echo '<div class="large-2 column">';
        echo CHtml::activeDropDownList($this, "[$id]operation", $ops, array('prompt' => 'Select One...'));
        echo CHtml::error($this, "[$id]operation");
        echo '</div>';
        echo '<div class="large-2 column">';
        echo CHtml::activeDropDownList($this, "[$id]condition", $conditions, array('prompt' => 'Select One...'));
        echo CHtml::error($this, "[$id]condition");
        echo '</div>';
    }

    /**
    * Generate a SQL fragment representing the subquery of a FROM condition.
    * @param $searchProvider The search provider. This is used to determine whether or not the search provider is using SQL syntax.
    * @return mixed The constructed query string.
     * @throws CHttpException
    */
    public function query($searchProvider)
    {
        // Construct your SQL query here.
        if ($searchProvider->getProviderID()  === 'mysql')
        {
            switch ($this->operation)
            {
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

            return "
SELECT p.id 
FROM patient p 
JOIN family_history fh
  ON fh.patient_id = p.id
WHERE (:f_h_side_$this->id IS NULL OR fh.side_id $op :f_h_side_$this->id)
  AND (:f_h_relative_$this->id IS NULL OR fh.relative_id $op :f_h_relative_$this->id)
  AND (:f_h_condition_$this->id $op :f_h_condition_$this->id)";
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
            "f_h_relative_$this->id" => $this->relative,
            "f_h_side_$this->id" => $this->side,
            "f_h_condition_$this->id" => $this->condition,
        );
    }

    /**
    * Generate a SQL fragment representing a JOIN condition to a subquery.
    * @param $joinAlias The alias of the table being joined to.
    * @param $criteria An array of join conditions. The ID for each element is the column name from the aliased table.
    * @param $searchProvider The search provider. This is used for an internal query invocation for subqueries.
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
        return "f_h_$this->id";
    }
}
