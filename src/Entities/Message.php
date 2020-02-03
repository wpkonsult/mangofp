<?php
namespace MangoFp\Entities;

class Message extends BaseEntity {
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
        parent::__construct();
        $this->data = \array_merge( $this->data, [
            'form' => '',
            'status_code' => 'NEW',
            'email' => '',
            'name' => '',
            'label' => '',
            'content' => '',
            'raw_data' => null
        ]);
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
            } else {
                $content[$key] = $value; 
            }
        }

        $data['content'] = \json_encode($content);
        $data['raw_data'] = \json_encode($rawData);
        $this->data = \array_merge($this->data, $data);
        return $this;
    }
}