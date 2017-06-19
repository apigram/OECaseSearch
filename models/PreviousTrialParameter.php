<?php

class PreviousTrialParameter extends CaseSearchParameter
{
    public $trial;
    public $type;

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
                array('type, trial', 'safe'),
            )
        );
    }

    public function renderParameter($id)
    {
        $ops = array(
            '=' => 'Has been in trial',
            '!=' => 'Has never been in trial',
        );

        $types = Trial::getTrialTypeOptions();

        ?>

      <div class="large-2 column">
          <?php echo CHtml::label($this->getKey(), false); ?>
      </div>
      <div class="large-3 column">
          <?php echo CHtml::activeDropDownList($this, "[$id]operation", $ops, array('prompt' => 'Select One...')); ?>
          <?php echo CHtml::error($this, "[$id]operation"); ?>
      </div>

      <div class="large-2 column trial-type">
          <?php echo CHtml::activeDropDownList($this, "[$id]type", $types,
              array('empty' => 'Any Trial', 'onchange' => 'getTrialList(this)')); ?>
      </div>

      <script type="text/javascript">
        function getTrialList(target) {
          var type = parseInt($(target).val());
          var trialHtml = null;
          $(target).parent().parent().find('.trial-list').remove();

          if (type === <?php echo Trial::TRIAL_TYPE_INTERVENTION; ?>) {
            trialHtml = <?php echo $this->getInterventionTrialList($this->id); ?>;
            $(target).parent().parent().find('.trial-type').after(trialHtml);
          }
          else if (type === <?php echo Trial::TRIAL_TYPE_NON_INTERVENTION; ?>) {
            trialHtml = <?php echo $this->getNonInterventionTrialList($this->id); ?>;
            $(target).parent().parent().find('.trial-type').after(trialHtml);
          }
          else {
            $(target).parent().parent().find('.trial-list').remove();
          }
        }
      </script>

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
        if ($searchProvider->providerID === 'mysql') {
            switch ($this->operation) {
                case '=':
                    $joinCondition = 'JOIN';
                    if ($this->type !== '' and isset($this->type)) {
                        if ($this->trial === '') {
                            // Any intervention/non-intervention trial
                            $condition = "t.trial_type = :p_t_type_$this->id";
                        } else {
                            // specific trial
                            $condition = "t_p.trial_id = :p_t_trial_$this->id";
                        }

                    } else {
                        // Any trial
                        $condition = "t_p.trial_id IS NOT NULL";
                    }
                    break;
                case '!=':
                    $joinCondition = 'LEFT JOIN';
                    if ($this->type !== '' and isset($this->type)) {
                        if ($this->trial === '') {
                            // Not in any intervention/non-intervention trial
                            $condition = "t_p.trial_id IS NULL OR t.trial_type != :p_t_type_$this->id";
                        } else {
                            // Not in a specific trial
                            $condition = "t_p.trial_id IS NULL OR t_p.trial_id != :p_t_trial_$this->id";
                        }
                    } else {
                        // not in any trial
                        $condition = "t_p.trial_id IS NULL";
                    }
                    break;
                default:
                    throw new CHttpException(400, 'Invalid operator specified.');
                    break;
            }

            return "
SELECT p.id 
FROM patient p 
$joinCondition trial_patient t_p 
  ON t_p.patient_id = p.id 
$joinCondition trial t
  ON t.id = t_p.trial_id
WHERE $condition";
        } else {
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
        $binds = array();

        if ($this->trial !== '') {
            $binds[":p_t_trial_$this->id"] = $this->trial;
        } elseif ($this->type !== '') {
            $binds[":p_t_type_$this->id"] = $this->type;
        }

        return $binds;
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
        return "p_t_$this->id";
    }

    public function getInterventionTrialList($id)
    {
        $trialModels = Trial::model()->findAll('trial_type=:type', array(':type' => Trial::TRIAL_TYPE_INTERVENTION));
        $trials = CHtml::listData($trialModels, 'id', 'name');
        $dropDown = CHtml::activeDropDownList($this, "[$id]trial", $trials, array('empty' => 'Any'));

        return CJSON::encode('<div class="large-3 column trial-list">' . $dropDown . '</div>');
    }

    public function getNonInterventionTrialList($id)
    {
        $trialModels = Trial::model()->findAll('trial_type=:type',
            array(':type' => Trial::TRIAL_TYPE_NON_INTERVENTION));
        $trials = CHtml::listData($trialModels, 'id', 'name');
        $dropDown = CHtml::activeDropDownList($this, "[$id]trial", $trials, array('empty' => 'Any'));

        return CJSON::encode('<div class="large-3 column trial-list">' . $dropDown . '</div>');
    }
}
