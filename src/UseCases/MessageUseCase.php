<?php
namespace MangoFp\UseCases;

class MessageUseCase {
    function __construct(iOutput $output, iStorage $storage) {
        $this->output = $output;
        $this->storage = $storage;
    }

    public function validateContentAndInsertAsNewMessage(array $content) : array {
        return $this->output->outputResult($content);
    }
} 
