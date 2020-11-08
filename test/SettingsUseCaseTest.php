<?php

use MangoFp\UseCases\SettingsUseCase;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class SettingsUseCaseTest extends TestCase {
    protected $storage;
    protected $output;

    public function testUpdateStepAndReturnSteps() {
        $this->output = new MockOutput();
        $this->storage = $this->createStub(MockStorage::class);

        $this->storage->method('fetchOption')->willReturn(false);
        $this->storage->method('storeOption')->willReturn(true);

        $useCase = new SettingsUseCase($this->output, $this->storage);

        $result = $useCase->fetchAllStepsToOutput();
        $this->assertEquals(false, isset($result['error']));
        $this->assertEquals(
            [
                'code' => 'ARCHIVED',
                'state' => 'Archived',
                'action' => 'Archive',
                'next' => ['NEW'],
                'order' => 5,
            ],
            $result['payload']['steps']['ARCHIVED']
        );

        $params = [
            'code' => 'ARCHIVED',
            'state' => 'Trashed',
        ];

        $result = $useCase->updateOrInsertStepAndReturnAllSteps($params);

        $this->assertEquals(
            [
                'code' => 'ARCHIVED',
                'state' => 'Trashed',
                'action' => 'Archive',
                'next' => ['NEW'],
                'order' => 5,
            ],
            $result['payload']['steps']['ARCHIVED']
        );
        $params = [
            'code' => 'WRONG',
            'state' => 'Trashed',
        ];

        $result = $useCase->updateOrInsertStepAndReturnAllSteps($params);

        $this->assertEquals(
            [
				'status' => 'RESULT_ERROR',
				'error' => [
					'code' => 'ERROR_NOTFOUND',
					'message' => 'Error updating or inserting step: Step WRONG not found.'
				]
			],
            $result
        );
    }
}
