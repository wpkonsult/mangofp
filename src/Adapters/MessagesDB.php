<?php
namespace MangoFp;

class MessagesDB {
    const VERSION_PARAM_NAME = 'mangofp_db_version';
	const VERSION = '0.0.4';
	const TABLE_MESSAGES = 'mangofp_messages';
    const TABLE_LABELS = 'mangofp_labels';

	public static function installDatabase() {
        global $wpdb;
		if (self::VERSION == get_site_option(self::VERSION_PARAM_NAME, '0.0.0') ) {
            error_log('will not install');
			return;
		}
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . self::TABLE_MESSAGES;
        $createSql = "CREATE TABLE $table_name (
			id varchar(50) NOT NULL,
			create_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			modify_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			delete_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            label varchar(100),
            label_id varchar(20),
            status_code varchar(20),
            email varchar(100),
            person_name varchar(100),
            content varchar(4000),
            rawdata varchar(4000),
            UNIQUE KEY id (id)
		) $charset_collate;";
        dbDelta( $createSql );
		
        $table_name = $wpdb->prefix . self::TABLE_LABELS;
        $createSql = "CREATE TABLE $table_name (
			id varchar(50) NOT NULL,
			create_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			modify_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			delete_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            label_name varchar(100),
            UNIQUE KEY id (id),
            KEY label_name (label_name)
		) $charset_collate;";
		dbDelta( $createSql );

		update_option( self::VERSION_PARAM_NAME, self::VERSION );
	}

	public static function removeDatabase() {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_MESSAGES;
		$sql = "DROP TABLE IF EXISTS $table_name";
		$wpdb->query($sql);
		$table_name = $wpdb->prefix . self::TABLE_LABELS;
		$sql = "DROP TABLE IF EXISTS $table_name";
		$wpdb->query($sql);
		delete_option( self::VERSION_PARAM_NAME );
	}

    public function storeMessage(Message $message) { return false; }
    public function insertMessage(Message $message) { return false; }
    public function messageExists(Message $message) { return false; }
    public function fetchMessage(string $id) { return false; }
    public function fetchSettings() { return false; }

    public function fetchLabelByName(string $labelName) {
        global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_LABELS;
		$request = $wpdb->prepare( 
            "SELECT id, create_time, modify_tim, delete_time, label_name
            FROM $table_name
            WHERE label_name LIKE '%s';
            ",
            [$labelName]
        );
        $labelRow = $wpdb->get_results($request);
    }

/*
    public static function storeMessage( $message ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_MESSAGES;

		if (!$) {
			return false;
		}

		$data = array (
			'create_time' => $logItem->getDateTime()->format('Y-m-d H:i:s '),
			'target_id' => $logItem->getTargetId(),
			'target_url' => $logItem->getTargetUrl(),
			'ping_status' => $logItem->getStatus(),
			'details' => $logItem->getDetailsJson(),
			'ping_message' => $logItem->getPingMessage(),
			'ping_duration' => $logItem->getDuration(),
			'record_level' => $logItem->getlevel()
		);

		$result = $wpdb->insert($table_name, $data);
		if ($result != 1) {
			error_log('Insert failed');
			return false;
		}

		$logItem->setId($wpdb->insert_id);

		return $logItem->getId();
	} 
    */  
}