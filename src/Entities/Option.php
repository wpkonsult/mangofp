<?php

namespace MangoFp\Entities;

class Option extends BaseEntity {
	const OPTION_STEPS = 'STEPS';
	public function __construct($data = []) {
        parent::__construct();
        $this->data = \array_merge($this->data, [
            'key' => $data['key'] ?? '',
			'value' => $data['value'] ?? '',
			'create_time' => $data['create_time'] ?? ''
		]);
		$this->className = 'Option';
    }
}
