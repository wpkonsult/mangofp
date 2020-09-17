<?php

use PHPUnit\Framework\TestCase;
use MangoFp\UseCases\MessageUseCase;
use MangoFp\Entities\Label;

class MessagesUseCaseTest extends TestCase {
    protected $storage;
    protected $output;

    public function testParseContentAndInsertToDatabase() {
        $this->output = new MockOutput();
        $this->storage = $this->createStub(MockStorage::class);

        $label = (new Label())->setDataAsArray([
            'id' => '47f3e442-b58f-4c9a-8e91-240f94c76ef6',
            'create_time' => '2020-01-29 23:49:52',
            'modify_time' => '',
            'labelName' => 'Test Label'
        ]);
        $this->storage->method('fetchLabelByName')->willReturn($label);
        $this->storage->method('insertMessage')->willReturn(true);
        $this->storage->method('storeMessage')->willReturn(true);
        $this->storage->method('getLabelTag')->willReturn('post_title');

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
            'post_title' => 'Test Label'
        ];

        $result = $useCase->parseContentAndInsertToDatabase($content);
        $this->assertEquals(false, isset($result['error']));
        $this->assertEquals(
            array_merge($result['payload']['message'], ['id' => 'testid', 'lastUpdated' => 'do not check']),
            [
                'id' => 'testid',
                'form' => '19',
                'labelId' => '47f3e442-b58f-4c9a-8e91-240f94c76ef6',
                'code' => 'NEW',
                'email' => 'test@test.com',
                'name' => 'Test Name',
				'note' => '',
				'lastUpdated' => 'do not check',
                'changeHistory' => null,
                'content' => json_encode([
                    'your-phone' => '+341234 12341234 1234123',
                    'your-message' => 'Test message',
                ])
            ]
        );
    }
    public function testErrorInParseContentAndInsertToDatabase() {
        $this->output = new MockOutput();
        $this->storage = $this->createStub(MockStorage::class);

        $label = (new Label())->setDataAsArray([
            'id' => '47f3e442-b58f-4c9a-8e91-240f94c76ef6',
            'create_time' => '2020-01-29 23:49:52',
            'modify_time' => '',
            'labelName' => 'Test Label'
        ]);
        $this->storage->method('fetchLabelByName')->willReturn($label);
        $this->storage->method('insertMessage')->willReturn(false);

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
            'post_title' => 'Test Label'
        ];

        $result = $useCase->parseContentAndInsertToDatabase($content);
        $this->assertEquals(true, isset($result['error']));

        $this->assertEquals(
            [
                'status' => 'RESULT_ERROR',
                'error' => [
                    'code' => 'ERROR_FAILED',
                    'message' => 'ERROR: unable to insert message'
                ]
            ],
            $result
        );
    }

    public function testFetchingExistingLabelByName() {
        $this->storage = $this->createStub(MockStorage::class);
        $this->output = new MockOutput();
        $label = (new Label())->setDataAsArray([
            'id' => '47f3e442-b58f-4c9a-8e91-240f94c76ef6',
            'create_time' => '2020-01-29 23:49:52',
            'modify_time' => '',
            'labelName' => 'Test Label2'
        ]);
        $this->storage->method('fetchLabelByName')->willReturn($label);
        $this->storage->method('insertLabel')->willReturn(false);
        $useCase = new MessageUseCase($this->output, $this->storage);

        $fetchedLabel = $useCase->fetchExistingOrCreateNewLabelByName('Test Label2');
        $this->assertEquals(
            array_merge($fetchedLabel->getDataAsArray(), ['modify_time' => '']),
            [
                'id' => '47f3e442-b58f-4c9a-8e91-240f94c76ef6',
                'create_time' => '2020-01-29 23:49:52',
                'modify_time' => '',
                'labelName' => 'Test Label2'
            ]
        );
    }

    public function testFetchingCreatedLabel() {
        $this->storage = $this->createStub(MockStorage::class);
        $this->output = new MockOutput();
        $label = (new Label())->setDataAsArray([
            'labelName' => 'Test Label3'
        ]);
        $this->storage->method('insertLabel')->willReturn($label);
        $this->storage->method('fetchLabelByName')->willReturn(false);
        $useCase = new MessageUseCase($this->output, $this->storage);

        $fetchedLabel = $useCase->fetchExistingOrCreateNewLabelByName('Test Label3');
        $this->assertNotEquals($fetchedLabel->get('id'), '');
        $this->assertEquals($fetchedLabel->get('labelName'), 'Test Label3');
        $this->assertNotEquals($fetchedLabel->get('create_time'), '');
    }
}