<?php
class PatientNameParameterTest extends CDbTestCase
{
    protected $parameter;
    protected $searchProvider;
    protected $invalidProvider;
    protected $fixtures = array(
        'patient' => 'Patient',
        'contact' => 'Contact'
    );

    protected function setUp()
    {
        parent::setUp();
        $this->parameter = new PatientNameParameter();
        $this->searchProvider = new DBProvider('mysql');
        $this->invalidProvider = new DBProvider('invalid');
        $this->parameter->id = 0;
    }

    protected function tearDown()
    {
        parent::tearDown();
        unset($this->parameter, $this->searchProvider, $this->invalidProvider);
    }

    /**
     * @covers DBProvider::search()
     * @covers DBProvider::executeSearch()
     * @covers PatientNameParameter::query()
     */
    public function testSearch()
    {
        $expected = array($this->patient('patient1'));

        $this->parameter->operation = 'LIKE';
        $this->parameter->patient_name = 'Jim';

        $results = $this->searchProvider->search(array($this->parameter));

        $ids = array();

        foreach ($results as $result)
        {
            $ids[] = $result['id'];
        }
        $actual = Patient::model()->findAllByPk($ids);

        $this->assertEquals($expected, $actual);

        $this->parameter->patient_name = 'Aylward';

        $results = $this->searchProvider->search(array($this->parameter));

        $ids = array();

        foreach ($results as $result)
        {
            $ids[] = $result['id'];
        }
        $actual = Patient::model()->findAllByPk($ids);
        $this->assertEquals($expected, $actual);
    }
}
