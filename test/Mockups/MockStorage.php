<?php
//namespace Mockups;
use MangoFp\UseCases\iStorage;
use MangoFp\Entities\Message;
use MangoFp\Entities\Label;

class MockStorage implements iStorage {
    protected $expectedResults = [];
    public function setExpectedResult(string $key, string $value) {
        $this->expectedResult[$key] = $value;
        return $this;
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
    public function fetchLabelByName(string $labelName) {
        return isset($this->expectedResult['fetchLabelByName']) ? $this->expectedResult['fetchLabelByName'] : false;
    }
    public function insertLabel(Label $label) {
        return isset($this->expectedResult['insertLabel']) ? $this->expectedResult['insertLabel'] : false;
    }
    public function getLabelTag() {
        return isset($this->expectedResult['getLabelTag']) ? $this->expectedResult['getLabelTag'] : 'post_title';
    }
    
}