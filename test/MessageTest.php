<?php

use PHPUnit\Framework\TestCase;
use MangoFp\Entities\Message;

class MessageTest extends TestCase {
    public function testGetAsArray() {
        $message = new Message();
        $message->setDataAsArray([
            'id' => '47f3e442-b58f-4c9a-8e91-240f94c76ef6',
            'create_time' => '2020-01-29 23:49:52',
            'modify_time' => ''         
        ]);
        $changedData = $message->getDataAsArray();
        $this->assertEquals(
            $changedData['id'],
            '47f3e442-b58f-4c9a-8e91-240f94c76ef6'
        );
        $this->assertEquals(
            $changedData['create_time'],
            '2020-01-29 23:49:52'
        );
        $this->assertNotEquals(
            $changedData['modify_time'],
            ''
        );
    }
}