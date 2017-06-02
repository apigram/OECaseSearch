<?php

/**
 * Created by PhpStorm.
 * User: andre
 * Date: 31/05/2017
 * Time: 4:51 PM
 */
class PreviousTrialParameterTest extends CTestCase
{
    protected $object;
    protected $searchProvider;

    protected function setUp()
    {
        $this->object = new PreviousTrialParameter();
        $this->searchProvider = new DBProvider('mysql');
        $this->object->id = 0;
    }

    protected function tearDown()
    {
        unset($this->object); // start from scratch for each test.
        unset($this->searchProvider);
    }

    /**
     * @covers PreviousTrialParameter::query()
     */
    public function testQuery()
    {
        $correctOps = array(
            '=',
            '!=',
        );
        $invalidOps = array(
            'NOT LIKE',
        );

        // Ensure the query is correct for each operator and returns a set of results.
        foreach ($correctOps as $operator) {
            $this->object->operation = $operator;
            $sqlValue = "
SELECT p.id 
FROM patient p 
JOIN trial_patient t_p 
  ON t_p.patient_id = p.id 
LEFT JOIN trial t
  ON t.id = t_p.trial_id
WHERE :p_t_trial_0 IS NULL OR t.name $operator :p_t_trial_0";
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
     * @covers PreviousTrialParameter::bindValues()
     */
    public function testBindValues()
    {
        $this->object->trial = 1;
        $expected = array(
            'p_t_trial_0' => $this->object->trial,
        );

        // Ensure that all bind values are returned.
        $this->assertEquals($expected, $this->object->bindValues());
    }

    /**
     * @covers PreviousTrialParameter::alias()
     */
    public function testAlias()
    {
        // Ensure that the alias correctly utilises the ID.
        $expected = 'p_t_0';
        $this->assertEquals($expected, $this->object->alias());
    }

    /**
     * @covers PreviousTrialParameter::join()
     */
    public function testJoin()
    {
        $this->object->operation = '=';
        $innerSql = $this->object->query($this->searchProvider);

        // Ensure that the JOIN string is correct.
        $expected = " JOIN ($innerSql) p_t_0 ON p_t_1.id = p_t_0.id";
        $this->assertEquals($expected, $this->object->join('p_t_1', array('id' => 'id'), $this->searchProvider));
    }
}
