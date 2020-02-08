<?php
namespace MangoFp\Entities;

class Label extends BaseEntity {
   function __construct() {
        parent::__construct();
        $this->data = \array_merge( $this->data, [
            'id' => $this->generateUuid(),
            'labelName' => '',
        ]);
    }
}