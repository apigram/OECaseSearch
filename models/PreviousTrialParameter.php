<?php
class PreviousTrialParameter extends CaseSearchParameter
{
    public $trial;

    /**
    * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
    * @param string $scenario
    */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'previous_trial';
    }

    public function getKey()
    {
        // This is a human-readable value, so feel free to change this as required.
        return 'Previous Trial';
    }

    /**
    * Override this function for any new attributes added to the subclass. Ensure that you invoke the parent function first to obtain and augment the initial list of attribute names.
    * @return array An array of attribute names.
    */
    public function attributeNames()
    {
        return array_merge(parent::attributeNames(), array(
                'trial',
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
                array('trial', 'safe'),
            )
        );
    }

    public function renderParameter($id)
    {
        // Place screen-rendering code here.
        $ops = array(
            '=' => 'has been in trial',
            '!=' => 'has never been in trial'
        );

        $trialModels = Trial::model()->findAll();
        $trials = array();
        foreach ($trialModels as $trial) {
            $trials[$trial->id] = "$trial->name - $trial->description";
        }

        echo '<div class="large-1 column">';
        echo CHtml::label('Patient', false);
        echo '</div>';
        echo '<div class="large-3 column">';
        echo CHtml::activeDropDownList($this, "[$id]operation", $ops, array('prompt' => 'Select One...'));
        echo CHtml::error($this, "[$id]operation");
        echo '</div>';

        echo '<div class="large-6 column"> ';
        echo CHtml::activeDropDownList($this, "[$id]trial", $trials, array('empty' => 'Any'));
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
JOIN trial_patient t_p 
  ON t_p.patient_id = p.id 
LEFT JOIN trial t
  ON t.id = t_p.trial_id
WHERE :p_t_trial_$this->id IS NULL OR t.name $op :p_t_trial_$this->id";
        }
        else
        {
            return null; // Not yet implemented.
        }
        return null;
    }

    /**
    * Get the list of bind values for use in the SQL query.
    * @return array An array of bind values. The keys correspond to the named binds in the query string.
    */
    public function bindValues()
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        return array(
            "p_t_trial_$this->id" => $this->trial,
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
        foreach ($criteria as $key => $column)
        {
            // if the string isn't empty, the condition is not the first so prepend it with an AND.
            if (!empty($query))
            {
                $query .= ' AND ';
            }
            $query .= "$joinAlias.$key = p_t_$this->id.$column";
        }

        $query = " JOIN ($subQuery) p_t_$this->id ON " . $query;

        return $query;
    }

    /**
    * Get the alias of the database table being used by this parameter instance.
    * @return string The alias of the table for use in the SQL query.
    */
    public function alias()
    {
        return "p_t_$this->id";
    }
}
