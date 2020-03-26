<?php
namespace MangoFp;
class Localization {
    static public function getStrings() {
        $MANGOFP = 'mangofp';
        return [
            'Send' => esc_html__('Send', $MANGOFP),
            'State' => esc_html__('State', $MANGOFP),
            'All' => esc_html__('All', $MANGOFP),
            'Confirm' => esc_html__('Confirm', $MANGOFP),
            'Confirm and send' => esc_html__('Confirm and send', $MANGOFP),
            'Date' => esc_html__('Date', $MANGOFP),
            'Status' => esc_html__('Status', $MANGOFP),
            'To' => esc_html__('To', $MANGOFP)
        ];
    }
}