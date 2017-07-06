<?php
class PatientNumberParameterTest extends CDbTestCase
{
    protected $parameter;
    protected $searchProvider;
    protected $invalidProvider;
    protected $fixtures = array(
        'patient' => 'Patient'
    );

    protected function setUp()
    {
        $this->parameter = new PatientNumberParameter();
        $this->searchProvider = new DBProvider('mysql');
        $this->invalidProvider = new DBProvider('invalid');
        $this->parameter->id = 0;
    }

    protected function tearDown()
    {
        unset($this->parameter, $this->searchProvider, $this->invalidProvider);
    }

    /**
     * @covers DBProvider::search()
     * @covers DBProvider::executeSearch()
     * @covers PatientNumberParameter::query()
     * @covers PatientNumberParameter::bindValues()
     */
    public function testSearch()
    {
        $expected = array($this->patient('patient1'));

        $this->parameter->operation = '=';
        $this->parameter->number = 12345;

        $results = $this->searchProvider->search(array($this->parameter));

        $this->assertCount(1, $results);
        $actual = Patient::model()->findAllByPk($results[0]);

        $this->assertEquals($expected, $actual);
    }
}
