<?php

use PHPUnit\Framework\TestCase;
use MangoFp\Entities\Label;

class LabelTest extends TestCase {
    public function testGetAsArray() {
        $label = new Label();
        $createdData = $label->getDataAsArray();
        $this->assertNotEquals(
            $createdData['id'],
            '47f3e442-b58f-4c9a-8e91-240f94c76ef6'
        );
        $this->assertNotEquals(
            $createdData['create_time'],
            '2020-01-29 23:49:52'
        );
        
        $label->setDataAsArray([
            'id' => '47f3e442-b58f-4c9a-8e91-240f94c76ef6',
            'create_time' => '2020-01-29 23:49:52',
            'modify_time' => '',
            'labelName' => 'Test Label'         
        ]);
        $changedData = $label->getDataAsArray();
        $this->assertEquals(
            $changedData['id'],
            '47f3e442-b58f-4c9a-8e91-240f94c76ef6'
        );
        $this->assertEquals(
            $changedData['create_time'],
            '2020-01-29 23:49:52'
        );
        $this->assertEquals(
            $changedData['labelName'],
            'Test Label'
        );
        $this->assertNotEquals(
            $changedData['modify_time'],
            ''
        );
    }
}