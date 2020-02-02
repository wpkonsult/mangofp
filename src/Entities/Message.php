<?php
namespace MangoFp\Entities;

function __generateUuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
    
            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),
    
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,
    
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,
    
            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
}

class Message {
    private $data;
    private $attributeMapping = [
        'name' => 'your-name',
        'email' => 'your-email',
        'label' => 'post_title',
        'form' => '_wpcf7'
    ];
    private $blacklistedAttributes = [
        '_wpcf7_version',
        '_wpcf7_locale',
        '_wpcf7_unit_tag',
        '_wpcf7_container_post',
    ];

    function __construct() {
        $this->data = [
            'id' => __generateUuid(),
            'create_time' => (new \DateTime())->format('Y-m-d H:i:s '),
            'form' => '',
            'status_code' => 'NEW',
            'email' => '',
            'name' => '',
            'label' => '',
            'content' => '',
            'raw_data' => null
        ];
    }

    public function getDataAsArray() : array {
        return $this->data;
    }

    public function setDataAsArray($newData) {
        $modified = false;
        foreach($this->data as $key => $value) {
            if (isset($newData[$key])) {
                $modified = true;
                $this->data[$key] = $newData[$key];
            }
        }
        if ($modified) {
            $this->data['modify_time'] = (new \DateTime())->format('Y-m-d H:i:s ');
        }
        return $this;
    }

    public function setFromRawData(array $rawData) {
        $data = [];
        $content = [];
        $primaries = \array_flip($this->attributeMapping);

        foreach ($rawData as $key => $value) {
            if (\in_array($key, $this->blacklistedAttributes)) {
                continue;
            }
            if (isset($primaries[$key])) {
                $primaryKey = $primaries[$key];
                $data[$primaryKey] = $value;
                //$data['debug'] = print_r($primaries, true);
            } else {
                $content[$key] = $value; 
            }
        }

        //$data['debug'] = \json_encode();

        $data['content'] = \json_encode($content);
        $data['raw_data'] = \json_encode($rawData);
        $this->data = \array_merge($this->data, $data);
        return $this;
    }
}