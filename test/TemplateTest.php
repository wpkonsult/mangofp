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
        $this->assertEquals(
            [],
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
