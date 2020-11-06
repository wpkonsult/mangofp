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
        $stepsData = $stepsObj->setDataAsInitialSteps()->get('value');

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

        $archived = makeArchived();

        $this->assertEquals(
            $archived,
            $stepsEntity->get('value')['4']
        );

        $archived['order'] = 5;

        $this->assertEquals(
            $archived,
            $stepsObj['ARCHIVED']
        );
    }

    public function testUpdateAndDeleteStep() {
        $stepsEntity = new Steps();
        $stepsObj = $stepsEntity->setDataAsInitialSteps()->getDataAsOrderedObject();
        $archived = $stepsObj['ARCHIVED'];
        $archived['action'] = 'To trash';
        $archived['next'][] = 'TEST';
        $stepsEntity->updateStep('ARCHIVED', $archived);

        $test = makeArchived();
        $test['action'] = 'To trash';
        $test['next'] = ['NEW', 'TEST'];

        $this->assertEquals(
            $test,
            $stepsEntity->get('value')[4]
        );

        $this->assertEquals(
            5,
            count($stepsEntity->get('value'))
        );
        $stepsEntity->deleteStep('ACCEPTED');

        $this->assertEquals(
            4,
            count($stepsEntity->get('value'))
        );

        $this->assertEquals(
            $test,
            $stepsEntity->get('value')[3]
        );
    }

    public function testMovingStepsUp() {
        $stepsEntity = new Steps();
        $stepsObj = $stepsEntity->setDataAsInitialSteps()->getDataAsOrderedObject();
        $this->assertEquals(
            ['NEW', 'INPROGRESS', 'ACCEPTED', 'DECLINED', 'ARCHIVED'],
            array_keys($stepsObj)
        );

        $stepsEntity->moveStep('ACCEPTED', Steps::ORDER_UP);

        $stepsObj = $stepsEntity->getDataAsOrderedObject();
        $this->assertEquals(
            ['NEW', 'ACCEPTED', 'INPROGRESS', 'DECLINED', 'ARCHIVED'],
            array_keys($stepsObj)
        );

        $stepsOrder = '';
        foreach ($stepsObj as $key => $value) {
            $stepsOrder .= $value['order'];
        }

        $this->assertEquals('12345', $stepsOrder);

        //move first element and nothing should happen
        $stepsEntity->moveStep('NEW', Steps::ORDER_UP);

        $stepsObj = $stepsEntity->getDataAsOrderedObject();
        $this->assertEquals(
            ['NEW', 'ACCEPTED', 'INPROGRESS', 'DECLINED', 'ARCHIVED'],
            array_keys($stepsObj)
        );

        //move last element
        $stepsEntity->moveStep('ARCHIVED', Steps::ORDER_UP);

        $stepsObj = $stepsEntity->getDataAsOrderedObject();
        $this->assertEquals(
            ['NEW', 'ACCEPTED', 'INPROGRESS', 'ARCHIVED', 'DECLINED'],
            array_keys($stepsObj)
        );
    }

    public function testMovingStepsDown() {
        $stepsEntity = new Steps();
        $stepsObj = $stepsEntity->setDataAsInitialSteps()->getDataAsOrderedObject();
        $this->assertEquals(
            ['NEW', 'INPROGRESS', 'ACCEPTED', 'DECLINED', 'ARCHIVED'],
            array_keys($stepsObj)
        );

        $stepsEntity->moveStep('ACCEPTED', Steps::ORDER_DOWN);

        $stepsObj = $stepsEntity->getDataAsOrderedObject();
        $this->assertEquals(
            ['NEW', 'INPROGRESS', 'DECLINED', 'ACCEPTED', 'ARCHIVED'],
            array_keys($stepsObj)
        );

        $stepsEntity->moveStep('ARCHIVED', Steps::ORDER_DOWN);

        $stepsObj = $stepsEntity->getDataAsOrderedObject();
        $this->assertEquals(
            ['NEW', 'INPROGRESS', 'DECLINED', 'ACCEPTED', 'ARCHIVED'],
            array_keys($stepsObj)
        );

        $stepsEntity->moveStep('NEW', Steps::ORDER_DOWN);

        $stepsObj = $stepsEntity->getDataAsOrderedObject();
        $this->assertEquals(
            ['INPROGRESS', 'NEW', 'DECLINED', 'ACCEPTED', 'ARCHIVED'],
            array_keys($stepsObj)
        );

        $stepsOrder = '';
        foreach ($stepsObj as $key => $value) {
            $stepsOrder .= $value['order'];
        }

        $this->assertEquals('12345', $stepsOrder);
    }

    public function testAppendingStepWithNoStep() {
        $stepsEntity = new Steps();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Missing state from step');
        $stepsEntity->appendStep([]);
    }

    public function testAppendingStepWithExistingStep() {
        $stepsEntity = new Steps();
        $stepsEntity->setDataAsInitialSteps();

        $this->assertEquals(
            5,
            count($stepsEntity->get('value'))
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('NEW allready exists - can not append');
        $stepsEntity->appendStep(['code' => 'NEW', 'state' => 'test']);
    }

	public function testAppendingStepWithNonExistingNextStep() {
        $stepsEntity = new Steps();
        $stepsEntity->setDataAsInitialSteps();

        $this->assertEquals(
            5,
            count($stepsEntity->get('value'))
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Step WRONG not a valid step');
        $stepsEntity->appendStep(['state' => 'test', 'next' => ['NEW', 'WRONG']]);
    }

    public function testAppendingStepWithMinimumValidData() {
        $stepsEntity = new Steps();
        $stepsEntity->setDataAsInitialSteps();

        $this->assertEquals(
            5,
            count($stepsEntity->get('value'))
        );

        $stepsEntity->appendStep(['state' => 'test']);

        $this->assertEquals(
            6,
            count($stepsEntity->get('value'))
        );

		$addedStep = $stepsEntity->get('value')[5];
		$this->assertNotEquals(
			'',
			$addedStep['code'] ?? ''
		);

		$this->assertEquals(
            'test',
            $addedStep['state']
        );

		$this->assertEquals(
            'test',
            $addedStep['action']
        );

		$this->assertEquals(
            [],
            $addedStep['next']
        );
    }
    public function testAppendingStepWithValidData() {
        $stepsEntity = new Steps();
        $stepsEntity->setDataAsInitialSteps();

        $this->assertEquals(
            5,
            count($stepsEntity->get('value'))
        );

        $stepsEntity->appendStep([
			'code' => 'TEST',
			'state' => 'test state',
			'action' => 'test action',
			'next' => ['NEW', 'ACCEPTED']
		]);

        $this->assertEquals(
            6,
            count($stepsEntity->get('value'))
        );

		$addedStep = $stepsEntity->get('value')[5];
		$this->assertEquals(
			'TEST',
			$addedStep['code']
		);

		$this->assertEquals(
            'test state',
            $addedStep['state']
        );

		$this->assertEquals(
            'test action',
            $addedStep['action']
        );

		$this->assertEquals(
            ['NEW', 'ACCEPTED'],
            $addedStep['next']
        );
    }
}
