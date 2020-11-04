<?php

namespace MangoFp\Entities;

class Option extends BaseEntity {
	const OPTION_STEPS = 'STEPS';
	public function __construct() {
        parent::__construct();
        $this->data = \array_merge($this->data, [
            'key' => '',
            'value' => '',
		]);
		$this->className = 'Option';

    }
}
