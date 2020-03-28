<?php
namespace MangoFp\Entities;

class Message extends BaseEntity {
    function __construct() {
        parent::__construct();
        $this->data = \array_merge( $this->data, [
            'form' => '',
            'statusCode' => 'NEW',
            'email' => '',
            'name' => '',
            'labelId' => '',
            'content' => '',
            'rawData' => '',
            'note' =>''
        ]);
    }
    function lastUpdated() {
        if (
            isset($this->data['modify_time']) &&
            $this->data['modify_time']
        ) {
            return $this->data['modify_time'];
        }
        return $this->data['create_time'];
    }

}