<?php

namespace MangoFp\UseCases;

use MangoFp\Entities\Option;
use MangoFp\Entities\Steps;

class SettingsUseCase {
    public function __construct(iOutput $output, iStorage $storage) {
        $this->output = $output;
        $this->storage = $storage;
    }

    public function fetchAllLabelsToOutput() {
        $labels = $this->storage->fetchLabels();

        if (!$labels || !\is_array($labels)) {
            $labels = [];
            //return $this->output->outputError('ERROR => unable to read labels list', iOutput::ERROR_FAILED);
        }

        $data = [];
        foreach ($labels as $label) {
            $data[] = [
                'id' => $label->get('id'),
                'name' => $label->get('labelName'),
            ];
        }

        return $this->output->outputResult(['labels' => $data]);
    }

    public function fetchStepsOrDefaultAsSteps() {
        $stepsOption = $this->storage->fetchOption(Option::OPTION_STEPS);
        $stepsObj = new Steps();
        if (!$stepsOption) {
			$stepsObj->setDataAsInitialSteps();
        } else {
            $stepsObj->setDataFromArray($stepsOption->getDataAsArray(), true);
        }

        return $stepsObj;
    }

    public function fetchAllStepsToOutput() {
        try {
            $stepsObj = $this->fetchStepsOrDefaultAsSteps();

            return $this->makeStepsOutput($stepsObj);
        } catch (\Exception $err) {
            return $this->output->outputError(
                'Error fetching steps: '.$err->getMessage(),
                iOutput::ERROR_NOTFOUND
            );
        }
    }

    public function updateOrInsertStepAndReturnAllSteps($stepData) {
        $stepsObj = $this->fetchStepsOrDefaultAsSteps();

        try {
            if (isset($stepData['code'])) {
                $stepsObj->updateStep($stepData['code'], $stepData);
            } else {
                $stepsObj->appendStep($stepData);
            }
        } catch (\Exception $err) {
            return $this->output->outputError(
                'Error updating or inserting step: '.$err->getMessage(),
                iOutput::ERROR_NOTFOUND
            );
        }

        if (!$this->storage->storeOption($stepsObj)) {
            return $this->output->outputError(
                'Error storing steps',
                iOutput::ERROR_NOTFOUND
            );
        }

        return $this->makeStepsOutput($stepsObj);
    }

    public function doWithStep($operation, $code) {
        $stepsObj = $this->fetchStepsOrDefaultAsSteps();

        try {
            switch ($operation) {
                case 'delete':
                    $stepsObj->deleteStep($code);
                    break;
                case 'moveup':
                    $stepsObj->moveStep($code, Steps::ORDER_UP);
                    break;
                case 'movedown':
                    $stepsObj->moveStep($code, Steps::ORDER_DOWN);
                    break;
                default:
					return $this->output->outputError(
					    'Action not allowed: ' . $operation,
					    iOutput::ERROR_NOTFOUND
					);
                    break;
            }
        } catch (\Exception $err) {
            return $this->output->outputError(
                'Failed to ' . $operation. ' step: '.$err->getMessage(),
                iOutput::ERROR_NOTFOUND
            );
        }

        if (!$this->storage->storeOption($stepsObj)) {
            return $this->output->outputError(
                'Error storing steps',
                iOutput::ERROR_NOTFOUND
            );
        }

        return $this->makeStepsOutput($stepsObj);
    }

    public function fetchAllTemplatesToOutput() {
        $templates = [
            REGISTERED => [
                template => 'Tere!\n\nSuur tänu! Olete koolitusele registreeritud.\nTäpsema info ja arve saadame enne koolituse algust e-mailile.\n\nTervitustega\nSirli Järviste\n_______________\nN.O.R.T Koolitus\nVaksali 17a, (407), Tartu\nhttps://www.nort.ee\ninfo@nort.ee\ntel. 7428000',
            ],
            WAIT4CONF => [
                template => 'Tere!\n\nSuur tänu! Olete koolitusele registreeritud.\n\nSaadan Töötukassasse ära registreerimisteate ja annan teada kui neilt kinnitus saabub.\n\nTervitustega\nSirli Järviste\n_______________\nN.O.R.T Koolitus\nVaksali 17a, (407), Tartu\nhttps://www.nort.ee\ninfo@nort.ee\ntel. 7428000',
            ],
            CONFRECEIVED => [
                template => 'Tere!\n\nTöötukassalt saabus kinnitus, sellega on nüüd kõik korras ja jääb ainult koolitust oodata.\n\nSaadan enne koolituse algust veel täpsustava infomeili.\n\Tervitades\nSirli Järviste\n_______________\nN.O.R.T Koolitus\nVaksali 17a, (407), Tartu\nhttps://www.nort.ee\ninfo@nort.ee\ntel. 7428000',
            ],
            WAIT4ACCEPT => [
                template => 'Tere!\n\nSuur tänu, et tunnete huvi meie koolituse vastu. Kuna järgmine koolitusaeg ei ole hetkel veel paigas, siis jätame Teid ootelehele ja anname teada kui koolitusaeg selgub.\n\nTervitustega\nSirli Järviste\n_______________\nN.O.R.T Koolitus\nVaksali 17a, (407), Tartu\nhttps://www.nort.ee\ninfo@nort.ee\ntel. 7428000',
            ],
            WAIT4NEW => [
                template => 'Tere!\n\nSuur tänu, et tunnete huvi meie koolituse vastu. Kuna järgmine koolitusaeg ei ole hetkel veel paigas, siis jätame Teid ootelehele ja anname teada kui koolitusaeg selgub.\n\nTervitustega\nSirli Järviste\n_______________\nN.O.R.T Koolitus\nVaksali 17a, (407), Tartu\nhttps://www.nort.ee\ninfo@nort.ee\ntel. 7428000',
            ],
            NOTIFIED => [
                template => 'Tere!\n\nOotame Teid esmaspäeval, 06. novembril kell 10.00, Exceli täiendkoolituse esimesele päevale.\n\nKoolitus toimub NORT Koolituse arvutiklassis, Vaksali 17a, ruum 407, Tartu (sissepääs Vaksali tänavalt, lillepoe ja kohvikuga samast uksest, liftiga 4.korrusele, asume otse lifti vastas.)\n\nPanin kaasa ka koolitusarve. Kui midagi oleks selles vaja muuta, siis andke palun teada.\n\nParkimine -  tänava ääres kellaga 90 min tasuta ja alates kella 18.00-st tasuta. Raudtee äärses parklas ja Tiigi tn äärses parklas on kogu aeg tasuta. Lähim linnaliini peatus on „Vaksali“.\n_______________\nN.O.R.T Koolitus\nVaksali 17a, (407), Tartu\nhttps://www.nort.ee\ninfo@nort.ee\ntel. 7428000',
            ],
            NEWSLETTER => [
                template => 'Tere!\n\nAitäh, et liitusite meie uudiskirjaga!.\n\nTänutäheks pakume Teile koolitusel osalemiseks soodustust -10%. (Soodustuse saamiseks, kirjutage koolitusele registreerumisel lisainfo lahtrisse sõna - "uudiskiri") Meie koolituskalendri leiate aadressilt https://nort.ee/koolituskalender/ \n\nKui on küsimusi, siis vastan meeleldi.\n\nTervitustega\nSirli Järviste\n_______________\nN.O.R.T Koolitus\nVaksali 17a, (407), Tartu\nhttps://www.nort.ee\ninfo@nort.ee\ntel. 7428000',
            ],
        ];

        return $this->output->outputResult(['templates' => $templates]);
    }

    public function storeOption(array $payload) {
        if (!isset($payload['key'])) {
            return $this->output->outputError('key is mandatory', iOutput::ERROR_FAILED);
        }

        if (!Option::isValidOption($payload['key'])) {
            return $this->output->outputError(
                'Not allowed option '.$payload['key'],
                iOutput::ERROR_FAILED
            );
        }

        $optionObj = new Option();
        $optionObj->setDataFromArray(
            [
                'key' => $payload['key'],
                'value' => $payload['value'],
            ]
        );

        error_log('About to store: '.\json_encode($payload));

        $success = $this->storage->storeOption($optionObj);
        if (!$success) {
            return $this->output->outputError('Failed to store option', iOutput::ERROR_FAILED);
        }

        return $this->output->outputResult(['success' => 'Setting saved']);
    }

    public function getOption(string $key) {
        if (!Option::isValidOption($key)) {
            return $this->output->outputError(
                'Not allowed option '.$key,
                iOutput::ERROR_FAILED
            );
        }
        $optionObj = $this->storage->fetchOption($key);

        if (!$optionObj) {
            return $this->output->outputError('Data for option '.$key.' not found', iOutput::ERROR_FAILED);
        }

        return [
            'key' => $optionObj->get('key'),
            'value' => $optionObj->get('value'),
        ];
    }

    protected function makeStepsOutput(Steps $stepsObj) {
        try {
            $result = [
                'steps' => $stepsObj->getDataAsOrderedObject(),
            ];

            return $this->output->outputResult($result);
        } catch (\Exception $err) {
            return $this->output->outputError(
                'Error parsing steps output: '.$err->getMessage(),
                iOutput::ERROR_NOTFOUND
            );
        }
    }
}
