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
        unset($this->object); // start from scratch for each test.
        unset($this->searchProvider);
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

    /**
     * @covers PatientMedicationParameter::alias()
     */
    public function testAlias()
    {
        // Ensure that the alias correctly utilises the ID.
        $expected = 'p_m_0';
        $this->assertEquals($expected, $this->object->alias());
    }

    /**
     * @covers PatientMedicationParameter::join()
     */
    public function testJoin()
    {
        $this->object->operation = 'LIKE';
        $innerSql = $this->object->query($this->searchProvider);

        // Ensure that the JOIN string is correct.
        $expected = " JOIN ($innerSql) p_m_0 ON p_m_1.id = p_m_0.id";
        $this->assertEquals($expected, $this->object->join('p_m_1', array('id' => 'id'), $this->searchProvider));
    }
}
