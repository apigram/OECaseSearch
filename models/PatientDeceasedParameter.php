<?php

/**
 * Class PatientDeceasedParameter
 */
class PatientDeceasedParameter extends CaseSearchParameter implements DBProviderInterface
{
    /**
     * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'patient_deceased';
        $this->operation = false;
    }

    public function getLabel()
    {
        // This is a human-readable value, so feel free to change this as required.
        return 'Patient Deceased';
    }

    /**
     * Override this function for any new attributes added to the subclass. Ensure that you invoke the parent function first to obtain and augment the initial list of attribute names.
     * @return array An array of attribute names.
     */
    public function attributeNames()
    {
        return parent::attributeNames();
    }

    /**
     * Override this function if the parameter subclass has extra validation rules. If doing so, ensure you invoke the parent function first to obtain the initial list of rules.
     * @return array The validation rules for the parameter.
     */
    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('operation', 'boolean'),
        ));
    }

    public function renderParameter($id)
    {
        // Initialise any rendering variables here.
        ?>
      <!-- Place screen-rendering code here. -->
      <div class="large-3 column">
          <?php echo CHtml::label('Include deceased patients', false); ?>
      </div>
      <div class="large-1 column end">
          <?php echo CHtml::activeCheckBox($this, "[$id]operation"); ?>
      </div>
        <?php
    }

    /**
     * Generate a SQL fragment representing the subquery of a FROM condition.
     * @param $searchProvider DBProvider The search provider. This is used to determine whether or not the search provider is using SQL syntax.
     * @return string The constructed query string.
     * @throws CHttpException
     */
    public function query($searchProvider)
    {
        switch ($this->operation)
        {
            case '0':
                return 'SELECT id FROM patient WHERE NOT(is_deceased)';
                break;
            case '1':
                return 'SELECT id FROM patient';
                break;
            default:
                throw new CHttpException(400, "Invalid value specified: $this->operation");
                break;
        }
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues()
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        // No binds are used in this query, so return an empty array.
        return array();
    }

    /**
     * Generate a SQL fragment representing a JOIN condition to a subquery.
     * @param $joinAlias string The alias of the table being joined to.
     * @param $criteria array An array of join conditions. The ID for each element is the column name from the aliased table.
     * @param $searchProvider DBProvider The search provider. This is used for an internal query invocation for subqueries.
     * @return string A SQL string representing a complete join condition. Join type is specified within the subclass definition.
     */
    public function join($joinAlias, $criteria, $searchProvider)
    {
        // Construct your JOIN condition here. Generally this involves wrapping the query in a JOIN condition.
        $subQuery = $this->query($searchProvider);
        $query = '';
        $alias = $this->alias();
        foreach ($criteria as $key => $column) {
            // if the string isn't empty, the condition is not the first so prepend it with an AND.
            if (!empty($query)) {
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
        return "p_de_$this->id";
    }
}
