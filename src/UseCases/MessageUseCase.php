<?php

namespace MangoFp\UseCases;

use MangoFp\Entities\HistoryItem;
use MangoFp\Entities\Label;
use MangoFp\Entities\Message;

class MessageUseCase {
    private $attributeMapping = [
        'name' => 'your-name',
        'email' => 'your-email',
        'form' => '_wpcf7',
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
        'acceptance-689',
    ];

    public function __construct(iOutput $output, iStorage $storage) {
        $this->output = $output;
        $this->storage = $storage;
    }

    public function fetchExistingOrCreateNewLabelByName(string $labelName) {
        $label = $this->storage->fetchLabelByName($labelName);
        if ($label) {
            return $label;
        }

        $newLabel = (new Label())->setDataFromArray(['labelName' => $labelName]);
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
        $message = (new Message())->setDataFromArray($data);

        $res = $this->storage->insertMessage($message);
        if (!$res) {
            return $this->output->outputError('ERROR: unable to insert message', iOutput::ERROR_FAILED);
        }
        //TODO: store label and fetch labelId, send it back
        //TODO: Fetch state for code and send it back

        return $this->output->outputResult($this->makeOneMessageOutputData($message));
    }

    public function fetchAllMessagesToOutput() {
        $messages = $this->storage->fetchMessages();

        if (!\is_array($messages)) {
            return $this->output->outputError('ERROR: unable to read messages list', iOutput::ERROR_FAILED);
        }

        $data = [];
        foreach ($messages as $message) {
            $data[] = $this->makeMessageOutputData($message);
        }

        return $this->output->outputResult(['messages' => $data]);
    }

    public function updateMessageAndReturnChangedMessage($params) {
        $UPDATEABLE_FIELDS = [
            'labelId' => 'labelId',
            'email' => 'email',
            'code' => 'statusCode',
            'name' => 'name',
            'note' => 'note',
        ];

        if (!isset($params['uuid'])) {
            return $this->output->outputError('No message id in message update request', iOutput::ERROR_VALIDATION);
        }

        if (!isset($params['message'])) {
            return $this->output->outputError('No data to be updated in the request', iOutput::ERROR_VALIDATION);
        }

        $messageObj = $this->storage->fetchMessage($params['uuid']);
        if (!$messageObj) {
            return $this->output->outputError('Message not found', iOutput::ERROR_NOTFOUND);
        }

        $messageData = $messageObj->getDataAsArray();
        $paramsMessage = $params['message'];
        $updatesHistory = [];
        foreach ($UPDATEABLE_FIELDS as $key => $field) {
            if (isset($paramsMessage[$key])) {
                $updatesHistory[] = (new HistoryItem())->setMessageChanges(
                    $messageObj->get('id'), // item id
                    'admin', //account
                    $key, //change type
                    $messageData[$field], // original content
                    $paramsMessage[$key] // content
                );

                $messageData[$field] = $paramsMessage[$key];
            }
        }

        $messageObj->setDataFromArray($messageData);
        \error_log('Will update message: '.print_r($messageObj->getDataAsArray(), 1));

        $updatedMessage = $this->storage->storeMessage($messageObj);
        if (!$updatedMessage) {
            return $this->output->outputError('Message update failed', iOutput::ERROR_FAILED);
        }

        foreach ($updatesHistory as $item) {
            //TODO - add error  handling???
            $this->storage->insertHistoryItem($item);
        }

        return $this->output->outputResult($this->makeOneMessageOutputData($updatedMessage));
    }

    public function getMessageDetailsAndReturn($params) {
        $messageObj = $this->storage->fetchMessage($params['uuid']);
        if (!$messageObj) {
            return $this->output->outputError('Message not found', iOutput::ERROR_NOTFOUND);
        }

        return $this->output->outputResult($this->makeOneMessageOutputData($messageObj));
    }

    public function sendEmailAndReturnMessage($emailData, $id) {
        if (
           !isset($emailData['content']) ||
           !isset($emailData['addresses']) ||
           !isset($emailData['subject'])
        ) {
            \error_log('Unable to send email - email field(s) missing. Submitted: '.\wp_json_encode($emailData));

            return $this->output->outputError('Unable to send email - email field(s) missing', iOutput::ERROR_FAILED);
        }

        $messageObj = $this->storage->fetchMessage($id);
        if (!$messageObj) {
            return $this->output->outputError('Message not found', iOutput::ERROR_NOTFOUND);
        }

        $isSuccess = $this->submitEmail($emailData, $id);
        if (!$isSuccess) {
            \error_log('Unable to send email. Submitted: '.\wp_json_encode($emailData));

            return $this->output->outputError('Sending email failed', iOutput::ERROR_FAILED);
        }

        return $this->output->outputResult($this->makeOneMessageOutputData($messageObj));
    }

    public function sendEmailAndUpdateMessageAndReturnChangedMessage($emailData, $params) {
        if (
           !isset($emailData['content']) ||
           !isset($emailData['addresses']) ||
           !isset($emailData['subject'])
        ) {
            \error_log('Unable to send email - email field(s) missing. Submitted: '.\wp_json_encode($emailData));

            return $this->output->outputError('Unable to send email - email field(s) missing', iOutput::ERROR_FAILED);
        }
        $code = isset($params['message']['code']) ? $params['message']['code'] : 'none';
        $isSuccess = $this->submitEmail($emailData, $params['uuid'], $code);
        if (!$isSuccess) {
            \error_log('Unable to send email. Submitted: '.\wp_json_encode($emailData));

            return $this->output->outputError('Sending email failed', iOutput::ERROR_FAILED);
        }

        return $this->updateMessageAndReturnChangedMessage($params);
    }

    protected function makeOneMessageOutputData(Message $message) {
        return [
            'message' => $this->makeMessageOutputData($message),
        ];
    }

    protected function makeMessageOutputData(Message $message) {
        return [
            'id' => $message->get('id'),
            'form' => $message->get('form'),
            'code' => $message->get('statusCode'),
            'content' => $message->get('content'),
            'labelId' => $message->get('labelId'),
            'email' => $message->get('email'),
            'name' => $message->get('name'),
            'note' => $message->get('note'),
            'lastUpdated' => $message->lastUpdated(),
            'changeHistory' => $this->storage->fetchItemHistory($message->get('id')),
        ];
    }

    protected function submitEmail($emailData, $id, $code = 'none') {
        $to = $emailData['addresses'];
        $subject = $emailData['subject'];
        $body = $emailData['content'];
        $attachments = [];
        $urls = [];
        if (isset($emailData['attachments'])) {
            foreach ($emailData['attachments'] as $attachmentId) {
                $url = \wp_get_attachment_url($attachmentId);
                $filePath = \get_attached_file($attachmentId);
                if (!$filePath) {
                    $email = $emailData['email'];
                    error_log("No file found for attachment {$attachmentId} while attempting to send email to {$email}");

                    return false;
                }
                $attachments[] = $filePath;
                $urls[] = $url;
            }
        }
        $success = false;

        //TODO: refactor email sending to the Adapter level
        //do not send email from development environment

        $headers = [];
        $ccForHistory = '';
        if (isset($emailData['ccAddresses']) && is_array($emailData['ccAddresses'])) {
            foreach ($emailData['ccAddresses'] as $email) {
                $headers[] = 'Cc: '.$email;
            }
            $ccForHistory = implode(', ', $emailData['ccAddresses']);
        }

        if (defined('MANGO_FP_DEBUG') && MANGO_FP_DEBUG) {
            $success = true;
        } else {
            $success = wp_mail(
                $to,
                $subject,
                $body,
                $headers, //TODO - add header to set reply address for incoming emails. e.g:  'Reply-To: Person Name <person.name@example.com>',
                $attachments
            );
        }

        if ($success) {
            $historyItem = (new HistoryItem())->setEmailSent(
                $id, // item id
                        'admin', //account
                        $code, //change type
                        [ // emailData
                            'to' => $to,
                            'cc' => $ccForHistory,
                            'subject' => $subject,
                            'message' => $body."\r\n\r\n"."Attachments:\r\n".implode(
                                "\r\n",
                                $urls
                            ),
                            'attachments' => json_encode($attachments),
                        ]
            );
            $this->storage->insertHistoryItem($historyItem);
        }

        return $success;
	}

}
