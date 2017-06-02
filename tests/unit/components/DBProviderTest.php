<?php

/**
 * Created by PhpStorm.
 * User: andre
 * Date: 2/06/2017
 * Time: 10:55 AM
 */
class DBProviderTest extends CDbTestCase
{
    protected $provider;

    public static function setupBeforeClass()
    {
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp()
    {
        $this->provider = new DBProvider('mysql');
    }

    public function tearDown()
    {
        unset($this->provider);
    }

    /**
     * @covers DBProvider::executeSearch()
     */
    public function testExecuteSearch()
    {
        // executeSearch is a protected function so it needs to be run via DBProvider's parent function, search.
        $testParameter1 = new PatientAgeParameter();
        $testParameter1->id = 0;
        $testParameter1->operation = '>';
        $testParameter1->textValue = 5;

        $testParameter2 = new PatientAgeParameter();
        $testParameter2->operation = '<';
        $testParameter2->textValue = 80;
        $testParameter2->id = 1;

        $results = $this->provider->search(array($testParameter1, $testParameter2));

        $this->assertNotEmpty($results);
    }
}