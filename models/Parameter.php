<?php

/**
 * Created by PhpStorm.
 * User: andre
 * Date: 22/05/2017
 * Time: 11:11 AM
 */
abstract class Parameter extends CFormModel
{
    public $name;
    public $operation;
    public $textValue;
    public $minValue;
    public $maxValue;
    public $selectValues;
    abstract public function getKey();

    public function attributeNames()
    {
        return array(
            'name' => '',
            'operation' => '',
            'textValue' => '',
            'minValue' => '',
            'maxValue' => '',
            'selectValues' => '',
        );
    }

    /**
     * Render the parameter on-screen.
     */
    abstract public function renderParameter();
}