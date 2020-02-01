<?php
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
    function __construct() {
        $this->data = [
            'id' => __generateUuid(),
            'create_time' => (new DateTime())->format('Y-m-d H:i:s '),
            'label' => '',
            'label_id' => '',
            'status_code' => '',
            'email' => '',
            'person_code' => '',
            'email' => '',
            'person_name' => '',
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
            $this->data['modify_time'] = (new DateTime())->format('Y-m-d H:i:s ');
        }
    }
}