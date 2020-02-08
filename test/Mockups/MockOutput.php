<?php
//namespace Mockups;
use MangoFp\UseCases\iOutput;

class MockOutput implements iOutput {
    public function outputResult(array $data) {
        return [
            'payload' => $data,
            'status'=> iOutput::RESULT_SUCCESS 
        ];
    }

    public function outputError(string $message, string $errorCode) {
        return [
            'status' => iOutput::RESULT_ERROR,
            'error' => [
                'code'=> $errorCode,
                'message' => $message
            ]
        ];
    }
}