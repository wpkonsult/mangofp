<?php

use PHPUnit\Framework\TestCase;
use MangoFp\UseCases\MessageUseCase;

class MessagesUseCaseTest extends TestCase {
    protected $storage;
    protected $output;
    
    public function testvalidateContentAndInsertAsNewMessage() {
        $this->storage = new MockStorage();
        $this->output = new MockOutput();
        $this->storage->setExpectedResult('insertMessage', true);
        $this->storage->setExpectedResult('storeMessage', true);
        $useCase = new MessageUseCase($this->output, $this->storage);
        $content = [
            '_wpcf7' => '19',
            '_wpcf7_version' => '5.1.6',
            '_wpcf7_locale' => 'en_US',
            '_wpcf7_unit_tag' => 'wpcf7-f19-p17-o1',
            '_wpcf7_container_post' => '17',
            'your-name' => 'Test Name',
            'your-email' => 'test@test.com',
            'your-phone' => '+341234 12341234 1234123',
            'your-message' => 'Test message',
            'acceptance-231' => 1,
            'post_title' => 'Title'
        ];

        $result = $useCase->validateContentAndInsertAsNewMessage($content);
        $this->assertEquals(
            array_merge($result['payload'], ['id' => 'testid']),
            [
                'id' => 'testid',
                'form' => '19',
                'labelId' => '',
                'code' => 'NEW',
                'state' => 'New',
                'email' => 'test@test.com',
                'name' => 'Test Name',
                'label' => 'Title',
                'content' => json_encode([
                    'your-phone' => '+341234 12341234 1234123',
                    'your-message' => 'Test message',
                    'acceptance-231' => 1,
                ])
            ]
        );

        $this->storage->setExpectedResult('insertMessage', false);
        $result = $useCase->validateContentAndInsertAsNewMessage($content);
        $this->assertEquals(
            $result,
            [
                'status' => 'RESULT_ERROR',
                'error' => [
                    'code' => 'ERROR_FAILED',
                    'message' => 'ERROR: unable to insert message'
                ]
            ]
        );
        

    }
}