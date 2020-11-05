<?php

namespace MangoFp\Entities;

class Steps extends Option {
    public function __construct($data = []) {
        parent::__construct($data);
        $this->data = \array_merge($this->data, [
			'key' => Option::OPTION_STEPS,
			'value' => $data['value'] ?? [],
        ]);
    }

    public function setDataAsInitialSteps() {
        $this->data['value'] = [
            [
                'code' => 'NEW',
                'state' => 'New',
                'action' => 'Set as new',
                'next' => [
                    'INPROGRESS',
                    'DECLINED',
                    'ACCEPTED',
                ],
            ],
            [
                'code' => 'INPROGRESS',
                'state' => 'In progress',
                'action' => 'Start working',
                'next' => [
                    'DECLINED',
                    'ACCEPTED',
                    'NEW',
                ],
            ],
            [
                'code' => 'ACCEPTED',
                'state' => 'Accpeted',
                'action' => 'Accept',
                'next' => [
                    'NEW',
                    'DECLINED',
                    'ARCHIVED',
                ],
            ],
            [
                'code' => 'DECLINED',
                'state' => 'Declined',
                'action' => 'Decline',
                'next' => [
                    'NEW',
                    'ARCHIVED',
                ],
            ],
            [
                'code' => 'ARCHIVED',
                'state' => 'Archived',
                'action' => 'Archive',
                'next' => [
                    'NEW',
                ],
            ],
		];
		return $this;
	}

	public function getDataAsOrderedObject() {
		$retObj = [];
		foreach ($this->data['value'] as $key => $value) {
			$copy = $value;
			$copy['order'] = $key + 1;
			$generatedKey = $value['code'];
			$retObj[$generatedKey] = $copy;
		}

		return $retObj;
	}

	public function orderStep(string $code, string $order) {

	}

	public function deleteStep(string $code) {

	}

	public function setDataFromOrderedObject($orderedObj) {

	}

}
