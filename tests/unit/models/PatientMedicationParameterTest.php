<?php

/**
 * Class PatientMedicationParameterTest
 */
class PatientMedicationParameterTest extends CTestCase
{
    protected $object;
    protected $searchProvider;

    protected function setUp()
    {
        $this->object = new PatientMedicationParameter();
        $this->searchProvider = new DBProvider('mysql');
        $this->object->id = 0;
    }

    protected function tearDown()
    {
        unset($this->object, $this->searchProvider);
    }

    /**
     * @covers PatientMedicationParameter::query()
     */
    public function testQuery()
    {
        $this->object->textValue = 5;

        $correctOps = array(
            'LIKE',
            'NOT LIKE',
        );
        $invalidOps = array(
            '=',
        );

        // Ensure the query is correct for each operator.
        foreach ($correctOps as $operator) {
            $this->object->operation = $operator;
            $wildcard = '%';

            if ($operator === 'LIKE')
            {
                $sqlValue = "
SELECT p.id 
FROM patient p 
LEFT JOIN medication m 
  ON m.patient_id = p.id 
LEFT JOIN drug d 
  ON d.id = m.drug_id
LEFT JOIN medication_drug md
  ON md.id = m.medication_drug_id
WHERE d.name $operator '$wildcard' || :p_m_value_0 || '$wildcard'
  OR md.name $operator '$wildcard' || :p_m_value_0 || '$wildcard'";
            }
            elseif ($operator === 'NOT LIKE')
            {
                $sqlValue = "
SELECT p.id 
FROM patient p 
LEFT JOIN medication m 
  ON m.patient_id = p.id 
LEFT JOIN drug d 
  ON d.id = m.drug_id
LEFT JOIN medication_drug md
  ON md.id = m.medication_drug_id
WHERE d.name $operator '$wildcard' || :p_m_value_0 || '$wildcard'
  OR md.name $operator '$wildcard' || :p_m_value_0 || '$wildcard'
  OR m.id IS NULL";
            }
            $this->assertEquals($sqlValue, $this->object->query($this->searchProvider));
        }

        // Ensure that a HTTP exception is raised if an invalid operation is specified.
        $this->setExpectedException(CHttpException::class);
        foreach ($invalidOps as $operator) {
            $this->object->operation = $operator;
            $this->object->query($this->searchProvider);
        }
    }

    /**
     * @covers PatientMedicationParameter::bindValues()
     */
    public function testBindValues()
    {
        $this->object->textValue = 5;
        $expected = array(
            'p_m_value_0' => $this->object->textValue,
        );

        // Ensure that all bind values are returned.
        $this->assertEquals($expected, $this->object->bindValues());
    }
}
