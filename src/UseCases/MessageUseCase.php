<?php
namespace MangoFp\UseCases;
use MangoFp\Entities\Message;
use MangoFp\Entities\Label;

class MessageUseCase {
    private $attributeMapping = [
        'name' => 'your-name',
        'email' => 'your-email',
        'form' => '_wpcf7'
    ];
    private $blacklistedAttributes = [
        '_wpcf7_version',
        '_wpcf7_locale',
        '_wpcf7_unit_tag',
        '_wpcf7_container_post',
    ];    

    function __construct(iOutput $output, iStorage $storage) {
        $this->output = $output;
        $this->storage = $storage;
    }

    public function fetchExistingOrCreateNewLabelByName(string $labelName) {
        $label = $this->storage->fetchLabelByName($labelName);
        if ($label) {
            return $label;
        }
            
        $newLabel = (new Label())->setDataAsArray(['labelName' => $labelName]);
        $result = $this->storage->insertLabel($newLabel);
        if (!$result) {
            return null;
        }
        return $newLabel;
    }

    public function parseContentAndInsertToDatabase(array $content) {
        $data = [];
        $secondaries = [];
        $primaries = \array_flip($this->attributeMapping);
        $label = null;
        $labelTag = $this->storage->getLabelTag();

        foreach ($content as $key => $value) {
            if ($key === $labelTag) {
                $label = $this->fetchExistingOrCreateNewLabelByName($value);
                continue;
            }

            if (\in_array($key, $this->blacklistedAttributes)) {
                continue;
            }

            if (isset($primaries[$key])) {
                $primaryKey = $primaries[$key];
                $data[$primaryKey] = $value;
            } else {
                $secondaries[$key] = $value; 
            }
        }
        $data['content'] = \json_encode($secondaries);
        $data['raw_data'] = \json_encode($content);
        $data['labelId'] = $label ? $label->get('id') : '';
        $message = (new Message())->setDataAsArray($data);

        $res = $this->storage->insertMessage($message);
        if (!$res) {
            return $this->output->outputError('ERROR: unable to insert message', iOutput::ERROR_FAILED);
        }
        //TODO: store label and fetch labelId, send it back
        //TODO: Fetch state for code and send it back
        
        return $this->output->outputResult([
            'id' => $message->get('id'),
            'form' => $message->get('form'),
            'code' => $message->get('statusCode'),
            'content' => $message->get('content'),
            'state' => 'New',
            'label' => $label ? $label->get('labelName') : '',
            'labelId' =>  $message->get('labelId'),
            'email' => $message->get('email'),
            'name' => $message->get('name')
        ]);
    }
} 
