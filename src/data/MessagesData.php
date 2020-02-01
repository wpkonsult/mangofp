<?php
class MessagesData {
    const VERSION_PARAM_NAME = 'mangofp_db_version';
	const VERSION = '0.0.2';
	const TABLE_MESSAGES = 'mangofp_messages';

	public static function  getCreateSql() {
		global $wpdb;
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

        error_log($createSql);

		return $createSql;
	}

	public static function installDatabase() {
        global $wpdb;
		if (self::VERSION == get_site_option(self::VERSION_PARAM_NAME, '0.0.0') ) {
            error_log('will not install');
			return;
		}
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( self::getCreateSql() );
		update_option( self::VERSION_PARAM_NAME, self::VERSION );
	}

	public static function removeDatabase() {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_MESSAGES;
		$sql = "DROP TABLE IF EXISTS $table_name";
		$wpdb->query($sql);
		delete_option( self::VERSION_PARAM_NAME );
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