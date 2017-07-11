<?php

/**
 * Class FamilyHistoryParameter
 */
class FamilyHistoryParameter extends CaseSearchParameter implements DBProviderInterface
{
    /**
     * @var $relative integer
     */
    public $relative;

    /**
     * @var $side integer
     */
    public $side;

    /**
     * @var $condition integer
     */
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

    public function getLabel()
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
                array('relative, side, condition', 'safe'),
            )
        );
    }

    public function renderParameter($id)
    {
        $ops = array(
            '=' => 'has',
            '!=' => 'does not have',
        );
        $relativeList = FamilyHistoryRelative::model()->findAll();
        $sideList = FamilyHistorySide::model()->findAll();
        $conditionList = FamilyHistoryCondition::model()->findAll();

        $relatives = CHtml::listData($relativeList, 'id', 'name');
        $sides = CHtml::listData($sideList, 'id', 'name');
        $conditions = CHtml::listData($conditionList, 'id', 'name');

        ?>
      <div class="large-2 column">
          <?php echo CHtml::label($this->getLabel(), false); ?>
      </div>

      <div class="large-2 column">
          <?php echo CHtml::activeDropDownList($this, "[$id]side", $sides, array('empty' => 'Any side')); ?>
      </div>

      <div class="large-2 column">
          <?php echo CHtml::activeDropDownList($this, "[$id]relative", $relatives, array('empty' => 'Any relative')); ?>
      </div>

      <div class="large-2 column">
          <?php echo CHtml::activeDropDownList($this, "[$id]operation", $ops, array('prompt' => 'Select One...')); ?>
          <?php echo CHtml::error($this, "[$id]operation"); ?>
      </div>
      <div class="large-2 column">
          <?php echo CHtml::activeDropDownList($this, "[$id]condition", $conditions,
              array('prompt' => 'Select One...')); ?>
          <?php echo CHtml::error($this, "[$id]condition"); ?>
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
        switch ($this->operation) {
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
WHERE (:f_h_side_$this->id IS NULL OR fh.side_id = :f_h_side_$this->id)
  AND (:f_h_relative_$this->id IS NULL OR fh.relative_id = :f_h_relative_$this->id)
  AND (:f_h_condition_$this->id $op :f_h_condition_$this->id)";
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
}
