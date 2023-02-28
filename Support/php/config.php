<?php
/**
 * ======================================
 * SUPPORT BOARD - CONFIG FILE
 * ======================================
 *
 * From this file you can set all the settings of the plugin.
 * Documentation at board.support/docs/php
 */

$sb_config_mysql = array(
    'host' => '172.31.137.15',
    'username' => 'root',
    'password' => 'uZjgoJ%cR7Et',
    'db' => 'boostapp_support',
);

$sb_config = array(
 'color-main' => '#48AD42',
 'color-secondary' => '',
 'font-disable' => false,
 'rtl' => true,
 'auto-multilingual' => false,
 'agent-subtitle' => 'I\'m here for help you.',
 'desk-login' => false,
 'user-extra-1' => '',
 'user-extra-2' => '',
 'user-extra-3' => '',
 'user-extra-4' => '',
 'user-email' => true,
 'username-type' => 'email', //email,username
 'user-img' => true,
 'scrollbox-active' => false,
 'scrollbox-height' => '', //integer or "fullscreen"
 'scrollbox-offset' => '0',
 'scrollbox-options' => '',
 'hide-message-time' => false,
 'width' => '',
 'notify-agent-email' => true,
 'notify-user-email' => true,
 'push-notifications' => 'all', //all(default),users,agents
 'flash-notifications' => true,
 'flash-notifications-text' => '',
 'hide-chat-time' => true,
 'chat-visibility' => '', //all(default),logged
 'chat-sound' => true,
 'chat-avatars' => true,
 'welcome-active' => true,
 'welcome-always' => true,
 'welcome-msg' => '',
 'welcome-img' => '',
 'welcome-closed' => false,
 'follow-active' => true,
 'follow-msg' => '',
 'follow-fb' => '',
 'follow-wa' => '',
 'chat-title' => lang('boostapp_support_config'),
 'chat-header' => lang('fast_boostapp_support'),
 'chat-header-avatars' => true,
 'chat-header-type' => '', //agents(default), brand
 'chat-brand-img' => '',
 'chat-header-img' => '',
 'chat-icon' => '',
 'slack-active' => true,
 'slack-token' => 'xoxp-575089984097-583627320116-583550225459-e605c9cd49e645616c23564cfcf7b2a4',
 'slack-channel' => 'CGX2NSGL9',
 'bot-active' => false,
 'bot-token' => '',
 'bot-lan' => '',
 'bot-name' => '',
 'bot-img' => ''
);

$sb_config_email = array(
    'host' => '',
    'username' => '',
    'password' => '',
    'port' => '465',
    'SMTPSecure' => 'ssl',
    'email_from' => '',
    'email_subject_users' => '',
    'email_content_users' => '',
    'email_subject_agents' => '',
    'email_content_agents' => '',
);

?>
