<?php

/**
 * Created by PhpStorm.
 * User: andre
 * Date: 31/05/2017
 * Time: 4:51 PM
 */
class PatientDiagnosisParameterTest extends CTestCase
{
    protected $object;
    protected $searchProvider;

    protected function setUp()
    {
        $this->object = new PatientDiagnosisParameter();
        $this->searchProvider = new DBProvider('mysql');
        $this->object->id = 0;
    }

    protected function tearDown()
    {
        unset($this->object); // start from scratch for each test.
        unset($this->searchProvider);
    }

    /**
     * @covers PatientDiagnosisParameter::query()
     */
    public function testQuery()
    {
        $this->object->textValue = 5;

        $correctOps = array(
            '=',
            '!=',
            //'LIKE',
        );
        $invalidOps = array(
            'NOT LIKE',
        );

        // Ensure the query is correct for each operator.
        foreach ($correctOps as $operator) {
            $this->object->operation = $operator;
            $sqlValue = "SELECT p.id 
FROM patient p 
JOIN secondary_diagnosis sd 
  ON sd.patient_id = p.id 
JOIN disorder d 
  ON d.id = sd.disorder_id 
WHERE d.term $operator :p_d_value_0";
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
     * @covers PatientDiagnosisParameter::bindValues()
     */
    public function testBindValues()
    {
        $this->object->textValue = 5;
        $expected = array(
            'p_d_value_0' => $this->object->textValue,
        );

        // Ensure that all bind values are returned.
        $this->assertEquals($expected, $this->object->bindValues());
    }

    /**
     * @covers PatientDiagnosisParameter::alias()
     */
    public function testAlias()
    {
        // Ensure that the alias correctly utilises the ID.
        $expected = 'p_d_0';
        $this->assertEquals($expected, $this->object->alias());
    }

    /**
     * @covers PatientDiagnosisParameter::join()
     */
    public function testJoin()
    {
        $this->object->operation = '=';
        $innerSql = $this->object->query($this->searchProvider);

        // Ensure that the JOIN string is correct.
        $expected = " JOIN ($innerSql) p_d_0 ON p_d_1.id = p_d_0.id";
        $this->assertEquals($expected, $this->object->join('p_d_1', array('id' => 'id'), $this->searchProvider));
    }
}