<?php

namespace MangoFp\Entities;

class Option extends BaseEntity {
    const OPTION_STEPS = 'steps';
    const OPTION_COMMENT = 'comment';

    public function __construct($data = []) {
        parent::__construct();
        $this->data = \array_merge($this->data, [
            'key' => $data['key'] ?? '',
            'value' => $data['value'] ?? '',
            'modify_time' => $data['modify_time'] ?? '',
        ]);
        $this->className = 'Option';
    }

    public static function isValidOption(string $key) {
		$allowedOptions = [Option::OPTION_STEPS, Option::OPTION_COMMENT];
		if (!\in_array($key, $allowedOptions)) {
			return false;
		}

		return true;
    }
}
