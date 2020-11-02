<?php
namespace MangoFp\Entities;

class Option extends BaseEntity {
   function __construct() {
        parent::__construct();
        $this->data = \array_merge( $this->data, [
            'id' => $this->generateUuid(),
			'key' => '',
			'value' => ''
        ]);
	}

}