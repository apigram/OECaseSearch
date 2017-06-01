<?php

/**
 * Created by PhpStorm.
 * User: andre
 * Date: 31/05/2017
 * Time: 3:39 PM
 */
class PatientAgeParameterTest extends CTestCase
{
    public static function setupBeforeClass()
    {
        Yii::app()->getModule('OECaseSearch');
    }
    protected $parameter;
    protected function setUp()
    {
        $this->parameter = new PatientAgeParameter();
        $this->parameter->id = 0;
    }

    protected function tearDown()
    {
        unset($this->parameter);
    }

    /**
     * @covers PatientAgeParameter::query()
     */
    public function testQuery()
    {
        $this->parameter->textValue = 5;
        $this->parameter->minValue = 5;
        $this->parameter->maxValue = 80;

        $searchProvider = new DBProvider('mysql');
        $correctOps = array(
            '<',
            '>',
            '>=',
            '<=',
            '=',
            '!=',
            'BETWEEN'
        );
        $invalidOps = array(
            'LIKE',
            'NOT LIKE',
        );

        // Ensure the query is correct for each operator.
        foreach ($correctOps as $operator) {
            $this->parameter->operation = $operator;
            if ($operator !== 'BETWEEN') {
                $sqlValue = "SELECT id FROM patient WHERE TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE())) $operator :p_a_value_0";
            }
            else {
                $sqlValue = "SELECT id FROM patient WHERE TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE())) $operator :p_a_min_0 AND :p_a_max_0";
            }
            $this->assertEquals($sqlValue, $this->parameter->query($searchProvider));
        }

        // Ensure that a HTTP exception is raised if an invalid operation is specified.
        $this->setExpectedException(CHttpException::class);
        foreach ($invalidOps as $operator) {
            $this->parameter->operation = $operator;
            $this->parameter->query($searchProvider);
        }
    }

    /**
     * @covers PatientAgeParameter::bindValues()
     */
    public function testBindValues()
    {
        $this->parameter->textValue = 5;
        $this->parameter->minValue = 5;
        $this->parameter->maxValue = 80;
        $expected = array(
            'p_a_value_0' => $this->parameter->textValue,
            'p_a_min_0' => $this->parameter->minValue,
            'p_a_max_0' => $this->parameter->maxValue
        );
        // Ensure that (if all elements are set) all bind values are returned.
        $this->assertEquals($expected, $this->parameter->bindValues());

        // Ensure that only attributes with values are returned from the bindValues list.
        unset($expected['p_a_value_0']);
        $this->parameter->textValue = null;

        $this->assertEquals($expected, $this->parameter->bindValues());
    }

    /**
     * @covers PatientAgeParameter::alias()
     */
    public function testAlias()
    {
        // Ensure that the alias correctly utilises the ID.
        $expected = 'p_a_0';
        $this->assertEquals($expected, $this->parameter->alias());
    }

    /**
     * @covers PatientAgeParameter::join()
     */
    public function testJoin()
    {
        $searchProvider = new DBProvider('mysql');
        $this->parameter->operation = '=';
        $innerSql = $this->parameter->query($searchProvider);

        // Ensure that the JOIN string is correct.
        $expected = " JOIN ($innerSql) p_a_0 ON p_a_1.id = p_a_0.id";
        $this->assertEquals($expected, $this->parameter->join('p_a_1', array('id' => 'id'), $searchProvider));
    }
}
