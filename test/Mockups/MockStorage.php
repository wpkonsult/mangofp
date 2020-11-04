<?php
//namespace Mockups;
use MangoFp\UseCases\iStorage;
use MangoFp\Entities\Message;
use MangoFp\Entities\Option;
use MangoFp\Entities\Label;
use MangoFp\Entities\HistoryItem;

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
    public function fetchMessages() {
        return isset($this->expectedResult['fetchMessage']) ? $this->expectedResult['fetchMessage'] : false;
    }
    public function fetchSetting(string $key) {
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
    public function fetchLabels() {
        return isset($this->expectedResult['fetchLabels']) ? $this->expectedResult['fetchLabels'] : [];
    }
    public function insertHistoryItem(HistoryItem $historyItem) {
        return isset($this->expectedResult['insertHistoryItem']) ? $this->expectedResult['insertHistoryItem'] : [];
    }
    public function fetchItemHistory(string $id) {
        return isset($this->expectedResult['fetchItemHistory']) ? $this->expectedResult['fetchItemHistory'] : [];
    }
    public function storeOption(Option $optionObj) {
        return isset($this->expectedResult['storeOption']) ? $this->expectedResult['storeOption'] : [];
    }
    public function fetchOption(string $code) {
        return isset($this->expectedResult['fetchOption']) ? $this->expectedResult['fetchOption'] : [];
    }

}