<?php

namespace MangoFp\Entities;

class Steps extends Option {
    const ORDER_UP = 'up';
    const ORDER_DOWN = 'down';

    public function __construct($data = []) {
        parent::__construct($data);
        $this->data = \array_merge($this->data, [
            'key' => Option::OPTION_STEPS,
            'value' => $data['value'] ?? [],
        ]);
    }

    public function setDataAsCustNo1teps() {
        $this->data['value'] = [
            [
                'order' => 1,
                'code' => 'NEW',
                'state' => 'Uus',
                'action' => 'Määra uueks',
                'next' => [
                    'REGISTERED',
                    'WAIT4CONF',
                    'WAIT4NEW',
                    'WAIT4ACCEPT',
                    'CANCELLED',
                    'NEWSLETTER',
                    'ARCHIVED',
                ],
            ],
            [
                'order' => 2,
                'code' => 'REGISTERED',
                'state' => 'Registreeritud',
                'action' => 'Registreeri',
                'next' => ['NOTIFIED', 'ARCHIVED', 'CANCELLED'],
            ],
            [
                'order' => 3,
                'code' => 'WAIT4CONF',
                'state' => 'TTA kaudu',
                'action' => 'TTA kaudu',
                'next' => ['CONFRECEIVED', 'CANCELLED'],
            ],
            [
                'order' => 4,
                'code' => 'CONFRECEIVED',
                'state' => 'TTA kinnitanud',
                'action' => 'TTA kinnitanud',
                'next' => ['REGISTERED', 'NOTIFIED', 'CANCELLED'],
            ],
            [
                'order' => 5,
                'code' => 'WAIT4NEW',
                'state' => 'Ooteleht',
                'action' => 'Ooteleht',
                'next' => ['REGISTERED', 'WAIT4ACCEPT', 'CANCELLED'],
            ],
            [
                'order' => 6,
                'code' => 'WAIT4ACCEPT',
                'state' => 'Aeg pakutud',
                'action' => 'Paku uus aeg',
                'next' => ['REGISTERED', 'WAIT4ACCEPT', 'CANCELLED'],
            ],
            [
                'order' => 7,
                'code' => 'NOTIFIED',
                'state' => 'Teade saadetud',
                'action' => 'Saada meeldetuletus',
                'next' => ['FBASKED', 'ARCHIVED'],
            ],
            [
                'order' => 8,
                'code' => 'FBASKED',
                'state' => 'Tagasiside küsitud',
                'action' => 'Küsi tagasiside',
                'next' => ['ARCHIVED'],
            ],
            [
                'order' => 9,
                'code' => 'NEWSLETTER',
                'state' => 'Uudiskiri',
                'action' => 'Uudiskiri',
                'next' => ['ARCHIVED'],
            ],
            [
                'order' => 10,
                'code' => 'ARCHIVED',
                'state' => 'Arhiveeritud',
                'action' => 'Arhiveeri',
                'next' => [],
            ],
            [
                'order' => 11,
                'code' => 'CANCELLED',
                'state' => 'Katkestatud',
                'action' => 'Katkesta',
                'next' => ['ARCHIVED'],
            ],
        ];

        return $this;
	}

	public static function getDefaultSteps() {
		return [
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
	}

    public function setDataAsInitialSteps() {
        $this->data['value'] = Steps::getDefaultSteps();
        return $this;
    }

    //Returns value as json string (internally value is kept as assoc. array)
    public function getDataAsArray(): array {
        $dataCopy = $this->data;
        $dataCopy['value'] = json_encode($this->data['value']);

        return $dataCopy;
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

    //if value is given as json string, convert it to array before storing in object
    public function setDataFromArray($newData, $loading = false) {
        if (!is_array($newData['value'])) {
            $stepsData = \json_decode($newData['value'], true);
            if (!$stepsData) {
                throw new \Exception('Error converting data from json string');
            }
            $newData['value'] = $stepsData;
        }

        return parent::setDataFromArray($newData, $loading);
    }

    public function updateStep(string $code, array $params) {
        $stepIndex = $this->findStepIndex($code);
        $stepData = $this->data['value'][$stepIndex];
        $modified = false;

        foreach ($stepData as $key => $value) {
            if (isset($params[$key])) {
                $modified = true;
                $stepData[$key] = $params[$key];
            }
        }
        $this->data['value'][$stepIndex] = $stepData;

        if ($modified) {
            $this->refreshModifiedTime();
        }

        return $this;
    }

    public function moveStep(string $code, string $direction) {
        if (!\in_array($direction, [Steps::ORDER_DOWN, Steps::ORDER_UP])) {
            throw new \Exception('Order '.$order.' not allowed');
        }

        $stepIndex = $this->findStepIndex($code);
        $newindex = $stepIndex;
        $lastIndex = count($this->data['value']);

        if (Steps::ORDER_UP === $direction) {
            if (0 === $stepIndex) {
                return $this;
            }
            $newindex = $stepIndex - 1;
        } else {
            if (($stepIndex + 1) >= $lastIndex) {
                return $this;
            }
            $newindex = $stepIndex + 1;
        }

        $tempStep = $this->data['value'][$newindex];
        $this->data['value'][$newindex] = $this->data['value'][$stepIndex];
        $this->data['value'][$stepIndex] = $tempStep;

        return $this;
    }

    public function deleteStep(string $code) {
        $stepInUse = [];

        $stepIndex = $this->findStepIndex($code);

        foreach ($this->data['value'] as $step) {
            if (\in_array($code, $step['next'])) {
                $stepInUse[] = $step['state'];
            }
        }

        if (count($stepInUse)) {
            throw new \Exception(
                'Can not delete step '.
                $code.
                ' It is in use for states:'.
                implode(', ', $stepInUse)
            );
        }

        array_splice($this->data['value'], $stepIndex, 1);

        return $this;
    }

    public function appendStep(array $stepData) {
        //validate that steps exist
        if (!isset($stepData['state'])) {
            throw new \Exception('Missing state from step');
        }

        $nextSteps = $stepData['next'] ?? [];
        $allSteps = [];

        foreach ($this->data['value'] as $step) {
            $allSteps[] = $step['code'];
        }

        if (isset($stepData['code']) &&
            \in_array($stepData['code'], $allSteps)
        ) {
            throw new \Exception($stepData['code'].' allready exists - can not append');
        }

        foreach ($nextSteps as $value) {
            if (!\in_array($value, $allSteps)) {
                throw new \Exception('Step '.$value.' not a valid step');
            }
        }

        $this->data['value'][] = [
            'code' => $stepData['code'] ?? strtoupper($this->generateUuid()),
            'state' => $stepData['state'],
            'action' => $stepData['action'] ?? $stepData['state'],
            'next' => $nextSteps,
        ];

        return $this;
    }

    protected function findStepIndex(string $code) {
        foreach ($this->data['value'] as $index => $step) {
            if ($step['code'] === $code) {
                return $index;
            }
        }

        throw new \Exception('Step '.$code.' not found.');
    }
}
