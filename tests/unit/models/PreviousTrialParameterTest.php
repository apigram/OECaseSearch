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
    protected $invalidProvider;

    protected function setUp()
    {
        $this->object = new PreviousTrialParameter();
        $this->searchProvider = new DBProvider('mysql');
        $this->invalidProvider = new DBProvider('invalid');
        $this->object->id = 0;
    }

    protected function tearDown()
    {
        unset($this->object); // start from scratch for each test.
        unset($this->searchProvider);
        unset($this->invalidProvider);
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

        $types = array(
            Trial::TRIAL_TYPE_NON_INTERVENTION,
            Trial::TRIAL_TYPE_INTERVENTION,
            '',
        );

        $trials = array(
            1,
            ''
        );

        // Ensure the query is correct for each operator and returns a set of results.
        foreach ($correctOps as $operator) {
            $this->object->operation = $operator;
            foreach ($types as $type) {
                $this->object->type = $type;
                foreach ($trials as $trial) {
                    $this->object->trial = $trial;
                    if ($operator === '=') {
                        $joinCondition = 'JOIN';
                        if ($this->object->type !== '' and isset($this->object->type)) {
                            if ($this->object->trial === '') {
                                // Any intervention/non-intervention trial
                                $condition = "t.trial_type = :p_t_type_0";
                            } else {
                                // specific trial
                                $condition = "t_p.trial_id = :p_t_trial_0";
                            }

                        } else {
                            // Any trial
                            $condition = "t_p.trial_id IS NOT NULL";
                        }
                    } elseif ($operator === '!=') {
                        $joinCondition = 'LEFT JOIN';
                        if ($this->object->type !== '' and isset($this->object->type)) {
                            if ($this->object->trial === '') {
                                // Not in any intervention/non-intervention trial
                                $condition = "t_p.trial_id IS NULL OR t.trial_type != :p_t_type_0";
                            } else {
                                // Not in a specific trial
                                $condition = "t_p.trial_id IS NULL OR t_p.trial_id != :p_t_trial_0";
                            }
                        } else {
                            // not in any trial
                            $condition = "t_p.trial_id IS NULL";
                        }
                    }
                    $sqlValue = "
SELECT p.id 
FROM patient p 
$joinCondition trial_patient t_p 
  ON t_p.patient_id = p.id 
$joinCondition trial t
  ON t.id = t_p.trial_id
WHERE $condition";
                    $this->assertEquals($sqlValue, $this->object->query($this->searchProvider));
                }
            }
        }

        $this->assertNull($this->object->query($this->invalidProvider));

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
        $this->object->type = Trial::TRIAL_TYPE_INTERVENTION;
        $expected = array(
            ':p_t_trial_0' => $this->object->trial,
        );

        // Ensure that all bind values are returned.
        $this->assertEquals($expected, $this->object->bindValues());

        $this->object->trial = '';

        $expected = array(
            ':p_t_type_0' => $this->object->type,
        );

        $this->assertEquals($expected, $this->object->bindValues());

        $this->object->trial = 1;

        // Both binds should never be returned. Only one should be returned.
        $incorrect = array(
            ':p_t_type_0' => $this->object->type,
            ':p_t_trial_0' => $this->object->trial
        );

        $this->assertNotEquals($incorrect, $this->object->bindValues());
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
