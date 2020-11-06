<?php

use MangoFp\Entities\Option;
use MangoFp\Entities\Steps;
use MangoFp\MessagesDB;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class StepTest extends TestCase {
    public function testParseStepsToArray() {
        $stepsObj = new Steps([
            'value' => ['Some test data'],
        ]);
        $stepsData = $stepsObj->getDataAsArray();
        $this->assertEquals(
            $stepsData['key'],
            Option::OPTION_STEPS,
        );

        $this->assertEquals(
            $stepsData['value'],
            '["Some test data"]',
        );
        $this->assertEquals(
            $stepsData['modify_time'],
            '',
        );
    }

    public function testParseStepsAsOption() {
        $stepsObj = new Steps([
            'value' => ['Some test data'],
        ]);
        $stepsData = MessagesDB::parseOptionToDbData($stepsObj);
        $this->assertEquals(
            [
                'modify_time' => '',
                'option_key' => 'steps',
                'option_value' => '["Some test data"]',
            ],
            $stepsData
        );
        $optionObj = MessagesDB::makeOptionWithDbData(
            [
                'modify_time' => '2020-01-29 23:49:52',
                'option_key' => 'STEPS',
                'option_value' => '["Some test data"]',
            ]
		);

		$this->assertEquals(
			[
				'id' => '',
				'key' => 'STEPS',
				'value' => '["Some test data"]',
				'modify_time' => '2020-01-29 23:49:52',
				'create_time' => ''
			],
			\array_merge(
				$optionObj->getDataAsArray(),
				['id' => '', 'create_time' => '']
			)
		);
    }
}
