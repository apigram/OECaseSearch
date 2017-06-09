<?php
class PatientDeceasedParameterTest extends CTestCase
{
    protected $parameter;
    protected $searchProvider;

    protected function setUp()
    {
        $this->parameter = new PatientDeceasedParameter();
        $this->searchProvider = new DBProvider('mysql');
        $this->parameter->id = 0;
    }

    protected function tearDown()
    {
        unset($this->parameter); // start from scratch for each test.
        unset($this->searchProvider);
    }

    /**
     * @covers PatientDeceasedParameter::query()
     */
    public function testQuery()
    {
        $correctOps = array(
            0,
            1
        );
        $invalidOps = array(
            'LIKE'
        );

        // Ensure the query is correct for each operator.
        foreach ($correctOps as $operator) {
            $this->parameter->operation = $operator;
            $sqlValue = ($operator === 0) ? "SELECT id FROM patient WHERE NOT(is_deceased)" : "SELECT id FROM patient";
            $this->assertEquals($sqlValue, $this->parameter->query($this->searchProvider));
        }

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
        $this->parameter->operation = 1;
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
        $this->parameter->operation = 0;
        $innerSql = $this->parameter->query($this->searchProvider);

        // Ensure that the JOIN string is correct.
        $expected = " JOIN ($innerSql) p_de ON p_a_0.id = p_de.id";
        $this->assertEquals($expected, $this->parameter->join('p_a_0', array('id' => 'id'), $this->searchProvider));
    }
}
