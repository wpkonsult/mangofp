<?php

namespace MangoFp\Entities;

class Steps extends Option {
	public function __construct($data = []) {
        parent::__construct();
        $this->data = \array_merge($this->data, [
			'create_time' => $data['create_time'],
			'modify_time' => $data['modify_time'] ?? '',
			'key' => Option::OPTION_STEPS,
			'value' => $data['value'] ?? '',
        ]);
    }
}
