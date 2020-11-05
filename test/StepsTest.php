<?php

use MangoFp\Entities\Steps;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
function makeArchived() {
    return [
        'code' => 'ARCHIVED',
        'state' => 'Archived',
        'action' => 'Archive',
        'next' => ['NEW'],
    ];
}

/**
 * @internal
 * @coversNothing
 */
class StepsTest extends TestCase {
    public function testGenerationOfInitialSteps() {
        $stepsObj = new Steps();
        $stepsData = $stepsObj->setDataAsInitialSteps()->getDataAsArray()['value'];

        $this->assertEquals(
            5,
            count($stepsData)
        );

        $this->assertEquals(
            makeArchived(),
            $stepsData[4]
        );
    }

    public function testGettingDataAsObject() {
        $stepsEntity = new Steps();
        $stepsObj = $stepsEntity->setDataAsInitialSteps()->getDataAsOrderedObject();
        $this->assertEquals(
            ['NEW', 'INPROGRESS', 'ACCEPTED', 'DECLINED', 'ARCHIVED'],
            array_keys($stepsObj)
        );

        $arhvied = makeArchived();

        $this->assertEquals(
            $arhvied,
            $stepsEntity->getDataAsArray()['value']['4']
        );

        $arhvied['order'] = 5;

        $this->assertEquals(
            $arhvied,
            $stepsObj['ARCHIVED']
        );
    }
}
