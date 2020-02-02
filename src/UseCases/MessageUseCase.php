<?php
namespace MangoFp\UseCases;
use MangoFp\Entities\Message;

class MessageUseCase {
    function __construct(iOutput $output, iStorage $storage) {
        $this->output = $output;
        $this->storage = $storage;
    }

    public function validateContentAndInsertAsNewMessage(array $content) : array {
        $message = (new Message())->setFromRawData($content);
        $res = $this->storage->insertMessage($message);
        if (!$res) {
            return $this->output->outputError('ERROR: unable to insert message', iOutput::ERROR_FAILED);
        }
        $data = $message->getDataAsArray();
        //TODO: store label and fetch labelId, send it back
        //TODO: Fetch state for code and send it back
        return $this->output->outputResult([
            'id' => $data['id'],
            'form' => $data['form'],
            'labelId' => '',
            'code' => $data['status_code'],
            'content' => $data['content'],
            'state' => 'New',
            'label' => $data['label'],
            'email' => $data['email'],
            'name' => $data['name']
        ]);
    }
} 
