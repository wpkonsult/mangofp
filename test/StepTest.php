<?php

use MangoFp\Entities\Option;
use MangoFp\Entities\Steps;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class StepTest extends TestCase {
    public function testParseStepsToArray() {
        $stepsObj = new Steps([
            'create_time' => '2020-01-29 23:49:52',
            'modify_time' => '',
            'value' => 'Some test data',
        ]);
        $stepsData = $stepsObj->getDataAsArray();
        $this->assertEquals(
            $stepsData['key'],
            Option::OPTION_STEPS,
        );

        $this->assertEquals(
            $stepsData['value'],
            'Some test data',
        );
        $this->assertEquals(
            $stepsData['modify_time'],
            '',
        );
    }
}
