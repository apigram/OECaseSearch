<?php
class PatientDeceasedParameterTest extends CDbTestCase
{
    protected $parameter;
    protected $searchProvider;
    protected $invalidProvider;
    protected $fixtures = array(
        'patient' => 'Patient'
    );

    protected function setUp()
    {
        parent::setUp();
        $this->parameter = new PatientDeceasedParameter();
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
     * @covers PatientDeceasedParameter::query()
     */
    public function testQuery()
    {
        $correctOps = array(
            '1',
            '0'
        );
        $invalidOps = array(
            'LIKE',
            'NOT LIKE'
        );

        // Ensure the query is correct for each operator.
        foreach ($correctOps as $operator) {
            $this->parameter->operation = $operator;
            $sqlValue = ($operator === '0') ? "SELECT id FROM patient WHERE NOT(is_deceased)" : "SELECT id FROM patient";
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
     * @covers PatientDeceasedParameter::bindValues()
     */
    public function testBindValues()
    {
        $this->parameter->operation = '1';
        $expected = array();

        // Ensure that all bind values are returned.
        $this->assertEquals($expected, $this->parameter->bindValues());
    }

    /**
     * @covers PatientDeceasedParameter::alias()
     */
    public function testAlias()
    {
        // Ensure that the alias correctly utilises the ID.
        $expected = 'p_de';
        $this->assertEquals($expected, $this->parameter->alias());
    }

    /**
     * @covers PatientDeceasedParameter::join()
     */
    public function testJoin()
    {
        $this->parameter->operation = '0';
        $innerSql = $this->parameter->query($this->searchProvider);

        // Ensure that the JOIN string is correct.
        $expected = " JOIN ($innerSql) p_de ON p_a_0.id = p_de.id";
        $this->assertEquals($expected, $this->parameter->join('p_a_0', array('id' => 'id'), $this->searchProvider));
    }

    public function testSearch()
    {
        // Ensure all patient fixtures are returned.
        $match = array();
        for ($i = 1; $i < 10; $i++)
        {
            $match[] = $this->patient("patient$i");
        }

        $this->parameter->operation = '1';

        $results = $this->searchProvider->search(array($this->parameter));

        $ids = array();
        foreach ($results as $result)
        {
            $ids[] = $result['id'];
        }
        $patients = Patient::model()->findAllByPk($ids);

        $this->assertEquals($match, $patients);

        // Ensure all patient fixtures except patient9 are returned.
        $this->parameter->operation = '0';
        $match = array();
        for ($i = 1; $i < 9; $i++)
        {
            $match[] = $this->patient("patient$i");
        }

        $results = $this->searchProvider->search(array($this->parameter));

        $ids = array();
        foreach ($results as $result)
        {
            $ids[] = $result['id'];
        }
        $patients = Patient::model()->findAllByPk($ids);

        $this->assertEquals($match, $patients);

    }
}
