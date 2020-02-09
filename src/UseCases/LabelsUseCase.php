<?php
namespace MangoFp\UseCases;
use MangoFp\Entities\Label;

class LabelsUseCase {
    function __construct(iOutput $output, iStorage $storage) {
        $this->output = $output;
        $this->storage = $storage;
    }

    public function fetchAllLabelsToOutput() {
        $labels = $this->storage->fetchLabels();
        
        if (!$labels || !\is_array($labels)) {
            return $this->output->outputError('ERROR: unable to read labels list', iOutput::ERROR_FAILED);
        }

        $data = [];
        foreach ($labels as $label) {
            $data[] = [
                'id' => $label->get('id'),
                'name' => $label->get('labelName')
            ];
        }
        return $this->output->outputResult(['labels' => $data]);
    }
}