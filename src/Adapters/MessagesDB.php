<?php
namespace MangoFp;
use MangoFp\Entities\Message;
use MangoFp\Entities\Label;
use MangoFp\Entities\HistoryItem;
use MangoFp\UseCases\iStorage;

class MessagesDB implements iStorage {
    const VERSION_PARAM_NAME = 'mangofp_db_version';
    const VERSION = '3';
    const TABLE_MESSAGES = 'mangofp_messages';
    const TABLE_LABELS = 'mangofp_labels';
    const TABLE_HISTORY = 'mangofp_history';

    public static function installDatabase() {
        global $wpdb;
        if (self::VERSION == get_site_option(self::VERSION_PARAM_NAME, '0.0.0') ) {
            error_log('will not install');
            return;
        }
        error_log('installing database version ' . self::VERSION);

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $charset_collate = $wpdb->get_charset_collate();
        $table_messages = $wpdb->prefix . self::TABLE_MESSAGES;
        $table_labels = $wpdb->prefix . self::TABLE_LABELS;
        $table_history = $wpdb->prefix . self::TABLE_HISTORY;
        $createSql = "CREATE TABLE $table_labels (
            id varchar(50) NOT NULL,
            create_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            modify_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            delete_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            label_name varchar(100),
            UNIQUE KEY id (id),
            KEY label_name (label_name)
        ) $charset_collate;";
        dbDelta( $createSql );

        $createSql = "CREATE TABLE $table_messages (
            id varchar(50) NOT NULL,
            create_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            modify_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            delete_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            label_id varchar(50),
            status_code varchar(20),
            email varchar(100),
            person_name varchar(100),
            note varchar(100),
            content varchar(4000),
            rawdata varchar(4000),
            UNIQUE KEY id (id),
            FOREIGN KEY (label_id) REFERENCES $table_labels(id)
        ) $charset_collate;";
        dbDelta( $createSql );

        $createSql = "CREATE TABLE $table_history (
            id varchar(50) NOT NULL,
            create_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            item_id varchar(50),
            change_type varchar(30),
            change_subtype varchar(30),
            original_content varchar(4000),
            user_account varchar(100),
            content varchar(4000),
            UNIQUE KEY id (id),
            KEY user_account (user_account)
        ) $charset_collate;";
        dbDelta( $createSql );

        update_option( self::VERSION_PARAM_NAME, self::VERSION );
    }

    public static function removeDatabase() {
        return true;
        //global $wpdb;
        //
        //$table_name = $wpdb->prefix . self::TABLE_MESSAGES;
        //$sql = "DROP TABLE IF EXISTS $table_name";
        //$wpdb->query($sql);
        //$table_name = $wpdb->prefix . self::TABLE_LABELS;
        //$sql = "DROP TABLE IF EXISTS $table_name";
        //$wpdb->query($sql);
        //$table_history = $wpdb->prefix . self::TABLE_HISTORY;
        //$sql = "DROP TABLE IF EXISTS $table_history";
        //$wpdb->query($sql);
        //delete_option( self::VERSION_PARAM_NAME );
    }

    public function getLabelTag() {
        return 'pealkiri';
    }

    public function storeMessage(Message $message) {
        global $wpdb;
        if (!$message || !$message->get('id')) {
            return null;
        }
        $result = $wpdb->update(
            $wpdb->prefix . self::TABLE_MESSAGES,
            $this->parseMessageToDbData($message),
            ['id' => $message->get('id')]
        );
        if (!$result) {
            error_log('ERROR: Message Update failed with data: ' . \json_encode($message->getDataAsArray()));
            return false;
        }
        if ($result > 1) {
            throw new \Error('More than one message was updated!!!!');
        }
        return $message;
    }
    public function insertMessage(Message $message) {
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . self::TABLE_MESSAGES,
            $this->parseMessageToDbData($message)
        );
        if ($result != 1) {
            error_log('ERROR: Message Insert failed with data: ' . \json_encode($message->getDataAsArray()));
            return false;
        }
        return true;
    }
    public function messageExists(Message $message) { return false; }
    public function fetchMessage(string $id) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_MESSAGES;
        $request = $wpdb->prepare(
            "SELECT id, create_time, modify_time, delete_time, label_id, status_code, email, person_name, content, rawdata, note
            FROM $table_name
            WHERE id = '%s';
            ",
            [$id]
        );
        $messageRow = $wpdb->get_row($request, ARRAY_A);
        if (!$messageRow) {
            return null;
        }
        return $this->makeMessageWithDbData($messageRow);
    }
    public function fetchMessages() {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_MESSAGES;
        $messageRows = $wpdb->get_results(
            "  SELECT id, create_time, modify_time, delete_time, label_id, status_code, email, person_name, content, rawdata, note
                FROM $table_name
                ORDER BY status_code, create_time desc;
            ",
            ARRAY_A
		);
		$modifiedMessages = apply_filters('mangofp_fetch_additional_messages', $messageRows);

		if (is_array($modifiedMessages)) {
            $messageRows = $modifiedMessages;
		}

        $allMessages = [];
        foreach ($messageRows as $messageRow) {
            $allMessages[] =  $this->makeMessageWithDbData($messageRow);
        }
        return $allMessages;
    }
    public function fetchSettings() { return false; }

    public function insertLabel(Label $label) {
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . self::TABLE_LABELS,
            [
                'id' => $label->get('id'),
                'label_name' => $label->get('labelName'),
                'modify_time' => $label->get('modify_time'),
                'create_time' => $label->get('create_time')
            ]
        );
        if ($result != 1) {
            error_log('ERROR: Label Insert failed with data: ' . \json_encode($label->getDataAsArray()));
            return false;
        }
        return true;
    }

    public function fetchLabelByName(string $labelName) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_LABELS;
        $request = $wpdb->prepare(
            "SELECT id, create_time, modify_time, delete_time, label_name
            FROM $table_name
            WHERE label_name LIKE '%s';
            ",
            [$labelName]
        );
        $labelRow = $wpdb->get_row($request, ARRAY_A);
        if (!$labelRow) {
            return null;
        }
        return (new Label())->setDataAsArray([
				'id' => $labelRow['id'],
				'labelName' => $labelRow['label_name'],
				'create_time' => $labelRow['create_time'],
				'delete_time' => $labelRow['delete_time'],
				'modify_time' => $labelRow['modify_time']
			],
			true
		);
    }

    public function fetchLabels() {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_LABELS;
        $labelRows = $wpdb->get_results(
            "  SELECT id, create_time, modify_time, delete_time, label_name
                FROM $table_name
                ORDER BY label_name;
            ",
            ARRAY_A
        );
        if (!$labelRows) {
            return [];
        }

        $labels = [];
        foreach ($labelRows as $labelRow) {
            $labels[] = (new Label())->setDataAsArray([
					'id' => $labelRow['id'],
					'labelName' => $labelRow['label_name'],
					'create_time' => $labelRow['create_time'],
					'delete_time' => $labelRow['delete_time'],
					'modify_time' => $labelRow['modify_time']
				],
				true
			);
        }
        return $labels;
    }

    public function insertHistoryItem(HistoryItem $historyItem) {
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . self::TABLE_HISTORY,
            $this->parseHistoryItemToDbData($historyItem)
        );
        if ($result != 1) {
            error_log('ERROR: History item insert failed with data: ' . \json_encode( $this->parseHistoryItemToDbData($historyItem) ));
            return false;
        }
        return true;
    }

    public function fetchItemHistory(string $id) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_HISTORY;
        $request = $wpdb->prepare(
            "SELECT id, create_time, item_id, change_type, change_subtype, original_content, content, user_account
            FROM $table_name
            WHERE item_id = '%s'
            ORDER BY create_time DESC;
            ",
            [$id]
        );
        $historyRows = $wpdb->get_results($request, ARRAY_A);
        if (!$historyRows) {
            return [];
        }

        $ħistory = [];
        foreach ($historyRows as $row) {
            $history[] = $this->makeHistoryItemWithDbData($row)->getDataAsArray();
        }
        return $history;
    }

    protected function makeMessageWithDbData($messageRow) {
        return (new Message())->setDataAsArray([
				'id' => $messageRow['id'],
				'create_time' => $messageRow['create_time'],
				'delete_time' => $messageRow['delete_time'],
				'modify_time' => $messageRow['modify_time'],
				'labelId' => $messageRow['label_id'],
				'statusCode' => $messageRow['status_code'],
				'email' => $messageRow['email'],
				'name' => $messageRow['person_name'],
				'content' => $messageRow['content'],
				'rawData' => $messageRow['rawdata'],
				'note' => $messageRow['note']
			],
			true
		);
    }

    protected function parseMessageToDbData(Message $message) {
        return [
            'id' => $message->get('id'),
            'modify_time' => $message->get('modify_time'),
            'create_time' => $message->get('create_time'),
            'status_code' => $message->get('statusCode'),
            'label_id' => $message->get('labelId') ? $message->get('labelId') : null,
            'email' => $message->get('email'),
            'person_name' => $message->get('name'),
            'content' => $message->get('content'),
            'rawdata' => $message->get('rawData'),
            'note' => $message->get('note'),
        ];
    }

    protected function parseHistoryItemToDbData(HistoryItem $historyItem) {
        return [
            'id' => $historyItem->get('id'),
            'create_time' => $historyItem->get('create_time'),
            'item_id' => $historyItem->get('itemId'),
            'change_type' => $historyItem->get('changeType'),
            'change_subtype' => $historyItem->get('changeSubType'),
            'original_content' => $historyItem->get('originalContent'),
            'content' => $historyItem->get('content'),
            'user_account' => $historyItem->get('userAccount')
        ];
    }

    protected function makeHistoryItemWithDbData($data) {
        return (new HistoryItem([
            'id' => $data['id'],
            'create_time' => $data['create_time'],
            'itemId' => $data['item_id'],
            'changeType' => $data['change_type'],
            'changeSubType' => $data['change_subtype'],
            'originalContent' => $data['original_content'],
            'content' => $data['content'],
            'userAccount' => $data['user_account']
        ]));
    }


}