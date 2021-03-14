<?php

use MangoFp\Entities\Option;
use MangoFp\Entities\Template;
use MangoFp\MessagesDB;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class TemplateTest extends TestCase {
    public function testParseTemplateToArray() {
        $templateObj = new Template([
			'code' => 'TESTCODE',
			'addresses' => ['test@test.ee', 'test2@test.ee'],
			'template' => 'Test template'
        ]);
        $templateData = $templateObj->getDataAsArray();
        $templateData['id'] = 'test-id';
        $templateData['create_time'] = 'test-time';
        $this->assertEquals(
            [
                'id' => 'test-id',
                'create_time' => 'test-time',
                'code' => 'TESTCODE',
                'modify_time' => '',
                'addresses' => Array ('test@test.ee', 'test2@test.ee'),
                'mainAddresses' => Array (),
                'template' => 'Test template'                ,
            ],
            $templateData,
        );

        //$this->assertEquals(
        //    $stepsData['value'],
        //    '["Some test data"]',
        //);
        //$this->assertEquals(
        //    $stepsData['modify_time'],
        //    '',
        //);
    }
}
