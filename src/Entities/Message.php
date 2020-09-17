<?php

namespace MangoFp\Entities;

class Message extends BaseEntity {
    public function __construct() {
        parent::__construct();
        $this->data = \array_merge($this->data, [
            'form' => '',
            'statusCode' => 'NEW',
            'email' => '',
            'name' => '',
            'labelId' => '',
            'content' => '',
            'rawData' => '',
            'note' => '',
			'modify_time' => '',
			'delete_time' => ''
        ]);
    }

    public function lastUpdated() {
        if (
            isset($this->data['modify_time']) &&
            $this->data['modify_time']
        ) {
            return $this->data['modify_time'];
        }

        return $this->data['create_time'];
    }
}
