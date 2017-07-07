<?php

/**
 * Class PatientAgeParameterTest
 */
class PatientAgeParameterTest extends CDbTestCase
{
    /**
     * @var CaseSearchParameter
     */
    protected $parameter;

    /**
     * @var DBProvider
     */
    protected $searchProvider;

    /**
     * @var DBProvider
     */
    protected $invalidProvider;

    protected $fixtures = array(
        'patient' => 'Patient',
    );

    public static function setupBeforeClass()
    {
        Yii::app()->getModule('OECaseSearch');
    }

    protected function setUp()
    {
        parent::setUp();
        $this->parameter = new PatientAgeParameter();
        $this->searchProvider = new DBProvider('mysql');
        $this->invalidProvider = new DBProvider('invalid');
        $this->parameter->id = 0;
    }

    protected function tearDown()
    {
        parent::tearDown();
        unset($this->parameter); // start from scratch for each test.
        unset($this->searchProvider);
        unset($this->invalidProvider);
    }

    /**
     * @covers PatientAgeParameter::query()
     */
    public function testQuery()
    {
        $this->parameter->textValue = 5;
        $this->parameter->minValue = 5;
        $this->parameter->maxValue = 80;
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
            $this->assertEquals($sqlValue, $this->parameter->query($this->searchProvider));
        }
        $this->assertNull($this->parameter->query($this->invalidProvider));

        // Ensure that a HTTP exception is raised if an invalid operation is specified.
        $this->setExpectedException(CHttpException::class);
        foreach ($invalidOps as $operator) {
            $this->parameter->operation = $operator;
            $this->parameter->query($this->searchProvider);
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
        $actual = $this->parameter->bindValues();
        // Ensure that (if all elements are set) all bind values are returned.
        $this->assertEquals($expected, $actual);

        // Ensure that all bind values are integers.
        $this->assertTrue(is_int($actual['p_a_value_0']) and is_int($actual['p_a_min_0']) and is_int($actual['p_a_max_0']));

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
        $this->parameter->operation = '=';
        $innerSql = $this->parameter->query($this->searchProvider);

        // Ensure that the JOIN string is correct.
        $expected = " JOIN ($innerSql) p_a_0 ON p_a_1.id = p_a_0.id";
        $this->assertEquals($expected, $this->parameter->join('p_a_1', array('id' => 'id'), $this->searchProvider));
    }

    /**
     * @covers DBProvider::search()
     * @covers PatientAgeParameter::query()
     */
    public function testSearchSingleInput()
    {
        // test an exact search using a simple operation
        $patients = array($this->patient('patient1'));
        $this->parameter->operation = '=';
        $dob = new DateTime($this->patient['patient1']['dob']);
        $this->parameter->textValue = $dob->diff(new DateTime())->format('%y');
        $results = $this->searchProvider->search(array($this->parameter));
        $ids = array();

        // deconstruct the results list into a single array of primary keys.
        foreach ($results as $result) {
            $ids[] = $result['id'];
        }
        $patientList = Patient::model()->findAllByPk($ids);

        // Ensure that results are returned.
        $this->assertEquals($patients, $patientList);

        $this->parameter->operation = '=';
        $this->parameter->textValue = 1;
        $results = $this->searchProvider->search(array($this->parameter));

        // Ensure that no results are returned.
        $this->assertEmpty($results);
    }

    public function testSearchDualInput()
    {
        $patients = array();
        $this->parameter->operation = 'BETWEEN';
        $this->parameter->minValue = 5;
        $this->parameter->maxValue = 80;

        for ($i = 1; $i < 10; $i++)
        {
            $patients[] = $this->patient("patient$i");
        }

        $results = $this->searchProvider->search(array($this->parameter));

        $ids = array();

        // deconstruct the results list into a single array of primary keys.
        foreach ($results as $result) {
            $ids[] = $result['id'];
        }
        $patientList = Patient::model()->findAllByPk($ids);

        // Ensure that results are returned.
        $this->assertEquals($patients, $patientList);
    }
}
