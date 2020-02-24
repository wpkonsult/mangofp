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
        '_wpcf7cf_hidden_group_fields',
        '_wpcf7cf_hidden_groups',
        '_wpcf7cf_visible_groups',
        '_wpcf7cf_repeaters',
        '_wpcf7cf_steps',
        '_wpcf7cf_options',
        'vormiurl',
        'acceptance-824',
        'acceptance-383',
        'acceptance-231',
        'acceptance-689'
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

            if (
                \in_array($key, $this->blacklistedAttributes) ||
                !$value
                ) {
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

    public function fetchAllMessagesToOutput() {
        $messages = $this->storage->fetchMessages();
        
        if (!\is_array($messages)) {
            return $this->output->outputError('ERROR: unable to read messages list', iOutput::ERROR_FAILED);
        }

        $data = [];
        foreach ($messages as $message) {
            $data[] = [
            'id' => $message->get('id'),
            'form' => $message->get('form'),
            'code' => $message->get('statusCode'),
            'content' => $message->get('content'),
            'labelId' =>  $message->get('labelId'),
            'email' => $message->get('email'),
            'name' => $message->get('name')
            ];
        }
        return $this->output->outputResult(['messages' => $data]);
    }

    public function updateMessageAndReturnChangedMessage($params) {
        $UPDATEABLE_FIELDS = [
            'labelId' => 'labelId',
            'email' => 'email',
            'code' => 'statusCode'
        ];

        if (!isset($params['uuid'])) {
            return $this->output->outputError('No message id in message update request', iOutput::ERROR_VALIDATION);
        }

        if (!isset($params['message'])) {
            return $this->output->outputError('No data to be updated in the request', iOutput::ERROR_VALIDATION);
        }

        $messageObj =  $this->storage->fetchMessage($params['uuid']);
        if (!$messageObj) {
             return $this->output->outputError('Message not found', iOutput::ERROR_NOTFOUND);
        }

        $messageData = $messageObj->getDataAsArray();
        $paramsMessage = $params['message'];
        foreach($UPDATEABLE_FIELDS as $key => $field) {
            if (isset($paramsMessage[$key])) {
                $messageData[$field] = $paramsMessage[$key];
            }
        }

        $messageObj->setDataAsArray($messageData);
        \error_log('Will update message: ' . print_r($messageObj->getDataAsArray(), 1));

        $updatedMessage = $this->storage->storeMessage($messageObj);
        if (!$updatedMessage) {
            return $this->output->outputError('Message update failed', iOutput::ERROR_FAILED);
        }

        //TODO refactor mapping to be a function in output. then use it here and in fetching messages list
        return $this->output->outputResult(['message' => [
            'id' => $updatedMessage->get('id'),
            'form' => $updatedMessage->get('form'),
            'code' => $updatedMessage->get('statusCode'),
            'content' => $updatedMessage->get('content'),
            'labelId' =>  $updatedMessage->get('labelId'),
            'email' => $updatedMessage->get('email'),
            'name' => $updatedMessage->get('name')
        ]]);
    }

    public function sendEmailAndUpdateMessageAndReturnChangedMessage($emailData, $params) {
       if (
           !isset($emailData['content']) ||
           !isset($emailData['addresses']) ||
           !isset($emailData['subject'])
        ) {
            \error_log('Unable to send email - email field(s) missing. Submitted: ' . \wp_json_encode( $emailData ));
            return $this->output->outputError('Unable to send email - email field(s) missing', iOutput::ERROR_FAILED);
        }
        $to = $emailData['addresses'];
        $subject = $emailData['subject'];
        $body = $emailData['content'];
        $headers = ['Content-Type: text/html; charset=UTF-8'];

        $isSuccess = wp_mail( $to, $subject, $body, $headers );

        if (!$isSuccess) {
            \error_log('Unable to send email. Submitted: ' . \wp_json_encode( $emailData ));
            return $this->output->outputError('Sending email failed', iOutput::ERROR_FAILED);
        }

        return $this->updateMessageAndReturnChangedMessage($params);
    }
} 
