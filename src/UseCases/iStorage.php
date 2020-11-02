<?php
namespace MangoFp\UseCases;
use MangoFp\Entities\Message;
use MangoFp\Entities\Label;
use MangoFp\Entities\HistoryItem;

interface iStorage {
    public function storeMessage(Message $message);
    public function insertMessage(Message $message);
    public function messageExists(Message $message);
    public function fetchMessage(string $id);
    public function fetchMessages();
    public function fetchSettings();
    public function fetchLabelByName(string $labelName);
    public function insertLabel(Label $label);
    public function getLabelTag();
    public function fetchLabels();
    public function insertHistoryItem(HistoryItem $historyItem);
    public function fetchItemHistory(string $id);
    public function fetchSteps();
}