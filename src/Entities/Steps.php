<?php

namespace MangoFp\Entities;

class Steps extends Option {
	public function __construct($data = []) {
        parent::__construct($data);
        $this->data = \array_merge($this->data, [
			'key' => Option::OPTION_STEPS
        ]);
    }
}
