<?php
namespace MangoFp\UseCases;
use MangoFp\Entities\Message;

interface iStorage {
    public function storeMessage(Message $message);
    public function insertMessage(Message $message);
    public function messageExists(Message $message);
    public function fetchMessage(string $id);
    public function fetchSettings();
}