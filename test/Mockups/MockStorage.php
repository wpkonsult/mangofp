<?php
//namespace Mockups;
use MangoFp\UseCases\iStorage;
use MangoFp\Entities\Message;

class MockStorage implements iStorage {
    protected $expectedResults = [];
    public function setExpectedResult(string $key, string $value) {
        $this->expectedResult[$key] = $value;
    }
    public function storeMessage(Message $message) {
        return isset($this->expectedResult['storeMessage']) ? $this->expectedResult['storeMessage'] : false;
    }
    public function insertMessage(Message $message) {
        return isset($this->expectedResult['insertMessage']) ? $this->expectedResult['insertMessage'] : false;
    }
    public function fetchMessage(string $id) {
        return isset($this->expectedResult['fetchMessage']) ? $this->expectedResult['fetchMessage'] : false;
    }
    public function fetchSettings() {
        return isset($this->expectedResult['fetchSettings']) ? $this->expectedResult['fetchSettings'] : false;
    }
    public function messageExists(Message $message) {
        return isset($this->expectedResult['messageExists']) ? $this->expectedResult['messageExists'] : false;
    }
}