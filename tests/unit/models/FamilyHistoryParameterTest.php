<?php

/**
 * Class FamilyHistoryParameterTest
 */
class FamilyHistoryParameterTest extends CDbTestCase
{
    protected $object;
    protected $searchProvider;

    public static function setupBeforeClass()
    {
        Yii::app()->getModule('OECaseSearch');
    }

    protected function setUp()
    {
        parent::setUp();
        $this->object = new FamilyHistoryParameter();
        $this->searchProvider = new DBProvider('mysql');
        $this->object->id = 0;
    }

    protected function tearDown()
    {
        parent::tearDown();
        unset($this->object, $this->searchProvider);
    }

    /**
     * @covers FamilyHistoryParameter::query()
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

        // Ensure the query is correct for each operator.
        foreach ($correctOps as $operator) {
            $this->object->operation = $operator;
            $sqlValue = "
SELECT p.id 
FROM patient p 
JOIN family_history fh
  ON fh.patient_id = p.id
WHERE (:f_h_side_0 IS NULL OR fh.side_id = :f_h_side_0)
  AND (:f_h_relative_0 IS NULL OR fh.relative_id = :f_h_relative_0)
  AND (:f_h_condition_0 $operator :f_h_condition_0)";
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
     * @covers FamilyHistoryParameter::bindValues()
     */
    public function testBindValues()
    {
        $this->object->relative = 1;
        $this->object->side = 1;
        $this->object->condition = 1;
        $expected = array(
            'f_h_relative_0' => 1,
            'f_h_side_0' => 1,
            'f_h_condition_0' => 1,
        );

        // Ensure that all bind values are returned.
        $this->assertEquals($expected, $this->object->bindValues());
    }

    /**
     * @covers FamilyHistoryParameter::alias()
     */
    public function testAlias()
    {
        // Ensure that the alias correctly utilises the ID.
        $expected = 'f_h_0';
        $this->assertEquals($expected, $this->object->alias());
    }

    /**
     * @covers FamilyHistoryParameter::join()
     */
    public function testJoin()
    {
        $this->object->operation = '=';
        $innerSql = $this->object->query($this->searchProvider);

        // Ensure that the JOIN string is correct.
        $expected = " JOIN ($innerSql) f_h_0 ON f_h_1.id = f_h_0.id";
        $this->assertEquals($expected, $this->object->join('f_h_1', array('id' => 'id'), $this->searchProvider));
    }
}
