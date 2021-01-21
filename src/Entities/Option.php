<?php

namespace MangoFp\Entities;

class Option extends BaseEntity {
    const OPTION_STEPS = 'steps';
    const OPTION_COMMENT = 'comment';
    const OPTION_REPLY_EMAIL = 'reply_email';
    const OPTION_EMAIL_FIELD = 'email_field';
    const OPTION_LABEL_FIELD = 'label_field';

    const OPTION_TYPE_EMAIL = 'EMAIL';
    const OPTION_TYPE_text = 'TEXT';

    public function __construct($data = []) {
        parent::__construct();
        $this->data = \array_merge($this->data, [
            'key' => $data['key'] ?? '',
            'value' => $data['value'] ?? '',
            'modify_time' => $data['modify_time'] ?? '',
        ]);
        $this->className = 'Option';
    }

    public static function getAllOptionsDefinitions() {
        return [
            Option::OPTION_EMAIL_FIELD => [
            "label" => Option::OPTION_EMAIL_FIELD,
            "type" => Option::OPTION_TYPE_text,
            "name" => __("Email field"),
            "hint" => __(
                "Email field on Contact Form(s) that will be seen as contact's email in Mango Contacts. Default: 'email'",
                ),
            ],
            Option::OPTION_LABEL_FIELD => [
                "label" => Option::OPTION_LABEL_FIELD,
                "type" => Option::OPTION_TYPE_text,
                "name" => __("Label field"),
                "hint" => __(
                    "Name of the Contact Form field that will be considered as label in Mango Contacts. If value is not set, name of the form's page is label value",
                ),
            ],
            Option::OPTION_REPLY_EMAIL => [
                "label" => Option::OPTION_REPLY_EMAIL,
                "type" => Option::OPTION_TYPE_EMAIL,
                "name" => __("Reply email"),
                "hint" => __(
                    "Email address for replies for emails that are sent from MangoFp. Premium emails add-on plugin enables receiving and management of replies directly in MangoFp",
                ),
            ],
        ];

    }

    public static function getListOfAllOptions() {
        $allowedOptions = array_keys(Option::getAllOptionsDefinitions());
        $allowedOptions[] = Option::OPTION_STEPS;
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
