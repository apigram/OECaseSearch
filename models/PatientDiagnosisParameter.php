<?php

/**
 * Class PatientDiagnosisParameter
 */
class PatientDiagnosisParameter extends CaseSearchParameter implements DBProviderInterface
{
    public $textValue;
    public $isConfirmed;

    const DIAGNOSIS_CONFIRMED = 1;
    const DIAGNOSIS_UNCONFIRMED = 0;

    /**
     * PatientAgeParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'diagnosis';
    }

    public function getLabel()
    {
        // This is a human-readable value, so feel free to change this as required.
        return 'Diagnosis';
    }

    /**
     * Override this function for any new attributes added to the subclass. Ensure that you invoke the parent function first to obtain and augment the initial list of attribute names.
     * @return array An array of attribute names.
     */
    public function attributeNames()
    {
        return array_merge(parent::attributeNames(), array('textValue', 'isConfirmed'));
    }

    /**
     * Override this function if the parameter subclass has extra validation rules. If doing so, ensure you invoke the parent function first to obtain the initial list of rules.
     * @return array The validation rules for the parameter.
     */
    public function rules()
    {
        return array_merge(parent::rules(), array(
                array('textValue', 'required'),
                array('textValue, isConfirmed', 'safe'),
            )
        );
    }

    public function renderParameter($id)
    {
        // Place screen-rendering code here.
        $ops = array(
            'LIKE' => 'Diagnosed with',
            'NOT LIKE' => 'Not diagnosed with',
        );

        $diagOptions = array(
            self::DIAGNOSIS_UNCONFIRMED => 'Unconfirmed only',
            self::DIAGNOSIS_CONFIRMED => 'Confirmed only',
        );
        ?>
      <div class="large-1 column">
          <?php echo CHtml::label($this->getLabel(), false); ?>
      </div>
      <div class="large-3 column">
          <?php echo CHtml::activeDropDownList($this, "[$id]operation", $ops, array('prompt' => 'Select One...')); ?>
          <?php echo CHtml::error($this, "[$id]operation"); ?>
      </div>

      <div class="large-3 column">
          <?php
          $html = Yii::app()->controller->widget('zii.widgets.jui.CJuiAutoComplete', array(
              'name' => 'diagnosis',
              'model' => $this,
              'attribute' => "[$id]textValue",
              'source' => Yii::app()->controller->createUrl('AutoComplete/commonDiagnoses'),
              'options' => array(
                  'minLength' => 2,
              ),
          ), true);
          Yii::app()->clientScript->render($html);
          echo $html;
          ?>
          <?php echo CHtml::error($this, "[$id]textValue"); ?>
      </div>
      <div class="large-3 column">
          <?php echo CHtml::activeDropDownList($this, "[$id]isConfirmed", $diagOptions,
              array('empty' => 'Confirmed/Unconfirmed')); ?>
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
        $query = "SELECT p.id 
FROM patient p 
LEFT JOIN secondary_diagnosis sd 
  ON sd.patient_id = p.id 
LEFT JOIN disorder d 
  ON d.id = sd.disorder_id 
WHERE LOWER(d.term) LIKE LOWER(:p_d_value_$this->id)";
        if ($this->isConfirmed === '0') {
            $query .= " AND sd.is_confirmed = :p_d_confirmed_$this->id";
        } elseif ($this->isConfirmed === '1') {
            $query .= " AND ((:p_d_confirmed_$this->id = " . self::DIAGNOSIS_CONFIRMED . " AND sd.is_confirmed IS NULL) OR sd.is_confirmed = :p_d_confirmed_$this->id)";
        }
        switch ($this->operation) {
            case 'LIKE':
                // Do nothing extra.
                break;
            case 'NOT LIKE':
                $query = "SELECT p1.id 
FROM patient p1
WHERE p1.id NOT IN (
  $query
)";
                break;
            default:
                throw new CHttpException(400, 'Invalid operator specified.');
                break;
        }

        return $query;
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues()
    {
        // Construct your list of bind values here. Use the format ":bind" => "value".
        if ($this->isConfirmed !== '') {
            return array(
                "p_d_value_$this->id" => '%' . $this->textValue . '%',
                "p_d_confirmed_$this->id" => $this->isConfirmed,
            );
        }

        return array(
            "p_d_value_$this->id" => '%' . $this->textValue . '%',
        );
    }
}
