<?php

namespace MangoFp\Entities;

class Option extends BaseEntity {
    const OPTION_STEPS = 'steps';
    const OPTION_COMMENT = 'comment';
    const OPTION_REPLY_EMAIL = 'reply_email';
    const OPTION_EMAIL_FIELD = 'email_field';
    const OPTION_LABEL_FIELD = 'label_field';

    public function __construct($data = []) {
        parent::__construct();
        $this->data = \array_merge($this->data, [
            'key' => $data['key'] ?? '',
            'value' => $data['value'] ?? '',
            'modify_time' => $data['modify_time'] ?? '',
        ]);
        $this->className = 'Option';
    }

    public static function getListOfAllOptions() {
        $allowedOptions = [
            Option::OPTION_STEPS,
            Option::OPTION_EMAIL_FIELD,
            Option::OPTION_LABEL_FIELD,
            Option::OPTION_REPLY_EMAIL,
        ];
        //TODO: Add filter for email plugin
        return $allowedOptions;
    }

    public static function isValidOption(string $key) {
        if (!\in_array($key, Option::getListOfAllOptions())) {
            return false;
        }

        return true;
    }
}
