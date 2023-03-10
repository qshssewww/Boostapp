<?php
/**
 * ======================================
 * SUPPORT BOARD - FUNCTIONS - PHP AND WP
 * ======================================
 *
 * Various functions used by PHP and WordPress
 */

function sb_get_plugin_url() {
    if (!defined("SB_PLUGIN_URL")) {
        $SB_PLUGIN_URL = (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        $index = strpos($SB_PLUGIN_URL,"supportboard");
        if ($index > 0) {
            $SB_PLUGIN_URL = substr($SB_PLUGIN_URL,0,$index + 12);
        }
        return $SB_PLUGIN_URL;
    } else {
        return SB_PLUGIN_URL;
    }
}
function sb_get_option($option_name) {
    if (file_exists(dirname(__FILE__) . '/wp.php')) {
        include('wp.php');
        if ($sb_wp_prefix == "") $sb_wp_prefix = "wp_";
        if (isset($sb_db['DB_HOST']) && $sb_db['DB_HOST'] != "" && isset($option_name)) {
            $conn;
            if (isset($sb_db['DB_PORT']) && $sb_db['DB_PORT'] != "") {
                $conn = new mysqli($sb_db['DB_HOST'], $sb_db['DB_USER'], $sb_db['DB_PASSWORD'], $sb_db['DB_NAME'], $sb_db['DB_PORT']);
            } else {
                $conn = new mysqli($sb_db['DB_HOST'], $sb_db['DB_USER'], $sb_db['DB_PASSWORD'], $sb_db['DB_NAME']);
            }
            mysqli_set_charset($conn, 'utf8');
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $result = $conn->query('SELECT option_value FROM ' . $sb_wp_prefix . 'options WHERE option_name="' . $option_name . '"');
            $value = false;
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $value = $row['option_value'];
                }
            }
            $conn->close();
            return $value;
        }
    } else {
        include_once('../php/functions.php');
        return get_option($option_name);
    }
    return false;
}
function sb_update_option($option_name, $value) {
    if (file_exists(dirname(__FILE__) . '/wp.php')) {
        include('wp.php');
        if ($sb_wp_prefix == "") $sb_wp_prefix = "wp_";
        if (isset($option_name) && isset($value)) {
            $conn;
            if (isset($sb_db['DB_PORT']) && $sb_db['DB_PORT'] != "") {
                $conn = new mysqli($sb_db['DB_HOST'], $sb_db['DB_USER'], $sb_db['DB_PASSWORD'], $sb_db['DB_NAME'], $sb_db['DB_PORT']);
            } else {
                $conn = new mysqli($sb_db['DB_HOST'], $sb_db['DB_USER'], $sb_db['DB_PASSWORD'], $sb_db['DB_NAME']);
            }
            mysqli_set_charset($conn, 'utf8');
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $value = str_replace("'","\'", $value);
            $query = "INSERT INTO " . $sb_wp_prefix . "options (option_name, option_value) VALUES('" . $option_name . "', '" . $value . "') ON DUPLICATE KEY UPDATE option_name='" . $option_name . "', option_value='" . $value . "'";
            $result = $conn->query($query);
            $conn->close();
            return $result;
        }
    } else {
        include_once('../php/functions.php');
        $result = update_option($option_name, $value);
        return $result;
    }
    return false;
}
function sb_send_email($to = "", $subject = "", $message = "") {
    require 'phpmailer/PHPMailerAutoload.php';
    global $sb_config_email;
    $sb_email;
    if (file_exists('wp.php')) {
        require 'wp.php';
        $sb_email = $sb_config_email;
    } else {
        if (file_exists('../php/config.php')) {
            require '../php/config.php';
            $sb_email = $sb_config_email;
        }
    }
    if ($to != "" && $subject != "" && $message != "") {
        if ($sb_email["host"] != "" && $sb_email["username"] != "" && $sb_email["password"] != "" && $sb_email["email_from"] != "") {
            $mail = new PHPMailer;
            $message = nl2br($message);

            $mail->isSMTP();
            $mail->Host = $sb_config_email["host"];
            $mail->SMTPAuth = true;
            $mail->Username = $sb_config_email["username"];
            $mail->Password = $sb_config_email["password"];
            $mail->SMTPSecure = $sb_config_email["SMTPSecure"];
            $mail->Port = $sb_config_email["port"];
            $mail->setFrom($sb_config_email["email_from"]);
            if (strpos($to,",") > 0) {
                $arr = explode(",",$to);
                for ($i = 0; $i < count($arr); $i++) {
                    $mail->addAddress($arr[$i]);
                }
            } else {
                $mail->addAddress($to);
            }
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->AltBody = $message;

            $result;
            if(!$mail->send()) {
                $result = $mail->ErrorInfo;
                return false;
            } else {
                return true;
            }
        } else {
            mail($to, $subject, $message);
        }
    }

}
function sb_sanatize_msg($msg) {
    $msg = nl2br(htmlspecialchars($msg));
    return $msg;
}
function sb_add_message($costumer_id, $msg, $time, $user_id, $user_img, $username, $files = "", $arr_conversation = "") {
    $files_arr = array();
    $isBot = (($user_id == "10000") ? true : false);

    if (isset($costumer_id)) {
        if ($arr_conversation == "") {
            $arr_conversation = sb_get_option("sb-conversation-" . $costumer_id);
            if ($arr_conversation == false || $arr_conversation == "null") {
                $arr_conversation = array();
            } else {
                $arr_conversation = str_replace('\"','"', $arr_conversation);
                $arr_conversation = json_decode(str_replace('\\"','"', $arr_conversation), true);
            }
        }
        $count = count($arr_conversation);
        if ($files != "") {
            if (is_string($files))  $files_arr = explode("?", $files);
            elseif (is_array($files)) $files_arr = $files;
        }
        if ($count == 0 || ($count > 0 && $arr_conversation[$count - 1]["msg"] != $msg) || ($msg == "" && count($files_arr) > 0)) {
            if ($isBot && strpos($msg,'{') > -1) {
                $msg = json_decode($msg, true);
            } else {
                $msg = sb_sanatize_msg($msg);
            }
            $item = array("msg" => $msg, "files" => $files_arr, "time" => $time, "user_id" => $user_id, "user_img" => $user_img, "user_name" => $username);
            array_push($arr_conversation, $item);
            sb_update_option("sb-conversation-" . $costumer_id, json_encode($arr_conversation, JSON_UNESCAPED_UNICODE));
        }
    }
    return $arr_conversation;
}
function sb_update_message($costumer_id, $index, $action = "msg", $content = "") {
    if (isset($costumer_id) && isset($index)) {
        $arr_conversation = sb_get_option("sb-conversation-" . $costumer_id);
        if ($arr_conversation != false) {
            $arr_conversation = json_decode(str_replace('\\"','"', $arr_conversation), true);
        }
        if ($arr_conversation != false) {
            if ($index < count($arr_conversation)) {
                if ($action == "rich") {
                    $arr_conversation[$index]["msg"]["status"] = "closed";
                    $arr_conversation[$index]["msg"]["status_info"] = $content;
                    sb_update_option("sb-conversation-" . $costumer_id, json_encode($arr_conversation, JSON_UNESCAPED_UNICODE));
                    return true;
                }
            }
        }
    }
    return false;
}
function sb_bot_message($msg, $costumer_id = "123456789", $sb_lang = "") {
    global $sb_config;
    if (!isset($sb_config)) {
        $sb_config = sb_get_settings();
    }
    $bot_token = sb_get($sb_config,"bot-token");
    $lang = "";
    $lang_token = true;
    if (function_exists('icl_object_id')) {
        $lang = ICL_LANGUAGE_CODE;
    } else {
        if (isset($sb_lang) && $sb_lang != "") {
            $lang = $sb_lang;
        } else {
            if (isset($sb_config["auto-multilingual"]) && $sb_config["auto-multilingual"]) {
                $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
                $lang = $lang . "_" . strtoupper($lang);
            } else {
                $lang = sb_get($sb_config, "bot-lan");
                $lang_token = false;
            }
        }
    }
    switch ($lang)  {
        case "en_EN" : $lang = "en"; break;
        case "en_US" : $lang = "en"; break;
        case "en_GB" : $lang = "en"; break;
        case "zh" : $lang = "zh-HK"; break;
        case "nl_NL" : $lang = "nl"; break;
        case "nl" : $lang = "nl"; break;
        case "fr_FR" : $lang = "fr"; break;
        case "fr" : $lang = "fr"; break;
        case "de_DE" : $lang = "de"; break;
        case "de" : $lang = "de"; break;
        case "ja_JA" : $lang = "ja"; break;
        case "ja" : $lang = "ja"; break;
        case "ko_KO" : $lang = "ko"; break;
        case "ko" : $lang = "ko"; break;
        case "pt_PT" : $lang = "pt"; break;
        case "pt" : $lang = "pt"; break;
        case "ru_RU" : $lang = "ru"; break;
        case "ru" : $lang = "ru"; break;
        case "es_ES" : $lang = "es"; break;
        case "es" : $lang = "es"; break;
        case "uk_UK" : $lang = "uk"; break;
        case "uk" : $lang = "uk"; break;
        case "it_IT" : $lang = "it"; break;
        case "it" : $lang = "it"; break;
    }
    if ($lang_token) {
        $tmp = sb_get($sb_config,"bot-token-" . $lang);
        if ($tmp != "") $bot_token = $tmp;
    }
    $postData = array('query' => array($msg), 'lang' => $lang, 'sessionId' => $costumer_id);
    $jsonData = json_encode($postData, JSON_UNESCAPED_UNICODE);
    $ch = curl_init('https://api.api.ai/v1/query?v=20170428');
    if (FALSE === $ch)
        throw new Exception('failed to initialize');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Authorization: Bearer ' . $bot_token));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    if (FALSE === $result)
        throw new Exception(curl_error($ch), curl_errno($ch));

    curl_close($ch);
    return $result;
}
function encryptor($action, $string) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = 'supportboard';
    $secret_iv = 'supportboard_iv';
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if ( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if( $action == 'decrypt' ){
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}
function sb_get_user_name($user_arr) {
    $user_name = $user_arr["username"];
    if ($user_name == "") $user_name = $user_arr["name"] . " " . $user_arr["surname"];
    if ($user_name == "") $user_name = $user_arr["email"];
    return $user_name;
}
function sb_get_user($user_id) {
    global $sb_config;
    if (!isset($sb_config)) {
        $sb_config = sb_get_settings();
    }
    $user = false;
    $users_arr = null;
    if (sb_get($sb_config,"users-engine") == "wp") {
        $users = get_users();
        foreach ($users as $user) {
            if ($user->ID == $user_id) {
                $user = array("id" => $user->ID, "img" => get_avatar_url($user->ID), "username" => $user->user_login);
                break;
            }
        }
    } else {
        $users_arr = json_decode(str_replace('\\"','"', get_option("sb-users-arr")), true);
        if ($users_arr != false) {
            for ($i = 0; $i < count($users_arr); $i++){
                if ($users_arr[$i]["id"] == $user_id) {
                    $user = $users_arr[$i];
                    break;
                }
            }
        }
    }
    return $user;
}
function sb_get_agent($user_id, $search_type = "slack") {
    $user = false;
    $agents_arr =  json_decode(str_replace('\\"','"', get_option("sb-agents-arr")), true);
    if ($agents_arr != false) {
        for ($i = 0; $i < count($agents_arr); $i++){
            if ($search_type == "slack" && isset($agents_arr[$i]["slack_user_id"]) && $agents_arr[$i]["slack_user_id"] == $user_id) {
                $user = $agents_arr[$i];
                break;
            } else {
                if ($search_type == "id" && $agents_arr[$i]["id"] == $user_id) {
                    $user = $agents_arr[$i];
                    break;
                }
            }
        }
    }
    if (($search_type == "forced" || $search_type = "slack") && $user == false) {
        $user = $agents_arr[0];
    }
    return $user;
}
function sb_get($arr,$key,$default = "") {
    $result = "";
    if (is_string($key)) {
        if(isset($arr[$key])) {
            if (is_bool($default) && $arr[$key] == "") {
                $result = false;
            } else {
                $result = $arr[$key];
            }
        } else {
            $result = $default;
        }
    } else {
        $count = count($key);
        if ($count == 1) {
            if(isset($arr[$key[0]])) $result = $arr[$key[0]];
            else $result = $default;
        }
        if ($count == 2) {
            if(isset($arr[$key[0]][$key[1]])) $result = $arr[$key[0]][$key[1]];
            else $result = $default;
        }
        if ($count == 3) {
            if(isset($arr[$key[0]][$key[1]][$key[2]])) $result = $arr[$key[0]][$key[1]][$key[2]];
            else $result = $default;
        }
        if ($count == 4) {
            if(isset($arr[$key[0]][$key[1]][$key[2]][$key[3]])) $result = $arr[$key[0]][$key[1]][$key[2]][$key[3]];
            else $result = $default;
        }
        if ($count == 5) {
            if(isset($arr[$key[0]][$key[1]][$key[2]][$key[3]][$key[4]])) $result = $arr[$key[0]][$key[1]][$key[2]][$key[3]][$key[4]];
            else $result = $default;
        }
    }
    if ($result == "" && !is_bool($default)) return $default;
    else return $result;
}
function sb_set_css() {
    global $sb_config;
    if (isset($sb_config) && $sb_config["color-main"] != "") {
        $main = $sb_config["color-main"];
        $secondary = $sb_config["color-secondary"];
        $css = "#sb-main .sb-chat-btn,#sb-main .sb-chat-header,#sb-main .sb-header,body #sb-main .sb-chat .sb-card.sb-card-right,#sb-main .sb-chat .sb-card.sb-card-right.sb-card-no-msg .sb-files a {
    background-color: " . $main . ";
}

#sb-main .sb-card.sb-card-right, #sb-main .sb-btn {
    background-color: " . $main . ";
    border-color: " . $main . ";
}";
        if ($secondary != "") {
            $css .= "#sb-main .sb-logout,#sb-main .sb-chat .sb-card.sb-card-right.sb-card-no-msg .sb-files a:hover,#sb-main .sb-card.sb-card-right .sb-files a  {
    background-color: " . $secondary . ";
}

#sb-main .sb-btn:hover {
    background-color: " . $secondary . ";
    border-color: " . $secondary . ";
}";
        }
        if (isset($sb_config["chat-position"]) && $sb_config["chat-position"] == "left") {
            $css .= "#sb-main .sb-chat-cnt { right: auto; left: 25px; } #sb-main .sb-chat-btn { right: auto; left: 0; } #sb-main .sb-active .sb-chat,#sb-main .sb-chat { right: auto; } ";
        }
        return $css;
    }
    return "";
}
function sb_get_emails($name = "", $message = "", $files_arr = array(), $br = false, $type = "agent", $environment = "wp") {
    $emails_arr = false;

    if (file_exists('../php/config.php')) {
        $environment == "php";
        require '../php/config.php';
        $us = $sb_config_email["email_subject_users"];
        $uc = $sb_config_email["email_content_users"];
        $as = $sb_config_email["email_subject_agents"];
        $ac = $sb_config_email["email_content_agents"];
        if ($us != "" && $uc != "" && $as != "" && $ac != "") {
            $emails_arr = array($us,$uc,$as,$ac);
        }
    } else {
        $emails_arr = sb_get_option("sb-emails");
    }

    if ($emails_arr == false || $emails_arr == "" || (!is_string($emails_arr) && $environment == "wp")) {
        if ($environment == "wp") {
            $emails_arr = array(
"[{site_name}] You received a response to your support request.",
"{message}
{files}

You can reply to the support here: {reply_link}
This email is sent by {site_name} - {site_url}

Regards,
{site_name} Support Team",
"[Support] New support request from {user_username}",
"{message}
{files}

Support request by {user_username}.
You can reply to the request here: {reply_link}

This email is sent by {site_name} - {site_url}");
        } else {
            $emails_arr = array(
"You received a response to your support request.",
"{message}
{files}

Regards,
Support Team",
"[Support] New support request from {user_username}",
"{message}
{files}

This email is sent by Support Board");
        }
    } else {
        if ($environment == "wp") {
            $val = str_replace('\\"','"', $emails_arr);
            $val = str_replace('\\\\n','<br>', $val);
            $val = preg_replace( "/\r|\n/", "<br>", $val );
            $emails_arr = json_decode($val);
        }
        if (!$br) {
            for ($i = 0; $i < count($emails_arr); $i++)  {
                $emails_arr[$i] = str_replace('<br>', PHP_EOL,  $emails_arr[$i]);
            }
        }
    }
    if ($name != "" && $message != "" || $message == "{message}") {

        $site_name = "";
        $site_url = "";
        $reply_link = "";
        $files = "";

        if (!$br) {
            $message = str_replace('<br>',PHP_EOL, $message);
        }
        for ($i = 0; $i < count($files_arr); $i++){
            $files .= $files_arr[$i] . PHP_EOL;
        }
        if ($environment == "wp") {
            $site_url = str_replace("/wp-content/plugins/supportboard", "", sb_get_plugin_url());
            if ($type == "agent") {
                $reply_link = $site_url . "/wp-admin/admin.php?page=support-board";
            } else {
                $reply_link = $site_url . "/";
            }
            $site_name =  str_replace("http://", "", str_replace("https://", "", $site_url));
            $site_url = $site_name;
            if (strpos($site_name,"/") > 0) $site_name = substr($site_name,0, strpos($site_name,"/"));
        }
        for ($i = 0; $i < count($emails_arr); $i++) {
            $item = $emails_arr[$i];
            $item = str_replace("{user_username}", ucfirst($name), $item);
            $item = str_replace("{message}", $message, $item);
            $item = str_replace("{files}", PHP_EOL . $files, $item);
            if ($environment == "wp") {
                $item = str_replace("{reply_link}", $reply_link, $item);
                $item = str_replace("{site_name}", $site_name, $item);
                $item = str_replace("{site_url}", $site_url, $item);
            }
            $emails_arr[$i] = $item;
        }
    }
    return $emails_arr;
}
function sb_check_email_allowed($user_id) {
    $sendAllowed = true;
    $activity_email_arr = json_decode(sb_get_option("sb-activity-email"), true);
    $dh_now = date('d') . date('H');
    if (isset($activity_email_arr)) {
        if (isset($activity_email_arr[$user_id])) {
            $dh = $activity_email_arr[$user_id];
            if ($dh == $dh_now) {
                $sendAllowed = false;
            } else {
                $activity_email_arr[$user_id] = $dh_now;
            }
        } else {
            $activity_email_arr[$user_id] = $dh_now;
        }
    } else {
        $activity_email_arr = array($user_id => $dh_now);
    }
    if ($sendAllowed) {
        sb_update_option("sb-activity-email", json_encode($activity_email_arr));
    }
    return $sendAllowed;
}
function sb_get_fonts_url($url_attr) {
    $font_url = '';
    if ( 'off' !== _x( 'on', 'Google font: on or off','hc') ) {
        $font_url = add_query_arg( 'family', $url_attr, "https://fonts.googleapis.com/css" );
    }
    return $font_url;
}
function sb_parse_message($msg) {
    if (isset($msg)) {
        //Breakline
        $msg = preg_replace('/(?:\r\n|\r|\n)/', '<br />',$msg);

        //Json
        $msg = str_replace("'", "&#39;", $msg);
        $msg = str_replace('\/"', "/&#8220;", $msg);
        $msg = str_replace('\"', "&#8220;", $msg);
    }
    return $msg;
}
function sb_get_settings() {
    $s = sb_get_option("sb-settings");
    $sb_config = array();
    if ($s != false) {
        $a = json_decode(str_replace('\\"','"', $s), true);
        if ($a != false) {
            $sb_config = $a;
        }
    }
    return $sb_config;
}
function sb_is_logged_in() {
    global $sb_config;
    $isLogged = false;
    if (isset($sb_config) && $sb_config["users-engine"] == "wp") {
        if (is_user_logged_in()) {
            $isLogged = true;
        }
    } else {
        if (isset($_SESSION['sb-login']) && !empty($_SESSION['sb-login'])) {
            $session = encryptor("decrypt", $_SESSION['sb-login']);
            if (strpos($session,"sb-logged-in") > -1) {
                $isLogged = true;
            }
        }
    }
    return $isLogged;
}
function sb_run_curl($url, $data) {
    $data = http_build_query($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
function sb_update_user($id="", $img="", $username="", $psw="", $email="", $extra1="", $extra2="", $extra3="", $extra4="", $last_email="") {
    global $sb_config;
    $users_arr = json_decode(str_replace('\\"','"', get_option("sb-users-arr")), true);
    $user = null;
    $user_i = -1;
    if ($users_arr != false) {
        for ($i = 0; $i < count($users_arr); $i++){
            if ($users_arr[$i]["id"] == $id) {
                $user = $users_arr[$i];
                $user_i = $i;
            }
        }
    }
    if (isset($user)) {
        if ($img != "" && $img != $user["img"]) {
            $user["img"] = $img;
        }
        if ($psw != "" && $psw != $user["psw"]) {
            $user["psw"] = $psw;
        }
        if ($email != "" && $email != $user["email"]) {
            $user["email"] = $email;
        }
        if (isset($sb_config)) {
            if ($sb_config["user-extra-1"] && $extra1 != "" && $extra1 != $user["extra1"]) {
                $user["extra1"] = $extra1;
            }
            if ($sb_config["user-extra-2"] && $extra2 != "" && $extra2 != $user["extra2"]) {
                $user["extra2"] = $extra2;
            }
            if ($sb_config["user-extra-3"] && $extra3 != "" && $extra3 != $user["extra3"]) {
                $user["extra3"] = $extra3;
            }
            if ($sb_config["user-extra-4"] && $extra4 != "" && $extra4 != $user["extra4"]) {
                $user["extra4"] = $extra4;
            }
        }
        if ($last_email != "") {
            $user["last-email"] = $last_email;
        }
        $users_arr[$user_i] = $user;
        update_option("sb-users-arr",json_encode($users_arr));
        return "success";
    }
    return "error";
}
function sb_update_agent($id="", $img="", $username="", $psw="", $email="", $wp_user_id="", $slack_user_id="", $last_email="") {
    global $sb_config;
    $users_arr = json_decode(str_replace('\\"','"', get_option("sb-agents-arr")), true);
    $user = null;
    $user_i = -1;
    if ($users_arr != false) {
        for ($i = 0; $i < count($users_arr); $i++){
            if ($users_arr[$i]["id"] == $id) {
                $user = $users_arr[$i];
                $user_i = $i;
            }
        }
    }
    if (isset($user)) {
        if ($img != "" && $img != $user["img"]) {
            $user["img"] = $img;
        }
        if ($psw != "" && $psw != $user["psw"]) {
            $user["psw"] = $psw;
        }
        if ($email != "" && $email != $user["email"]) {
            $user["email"] = $email;
        }
        if ($wp_user_id != "" && $wp_user_id != $user["wp_user_id"]) {
            $user["wp_user_id"] = $email;
        }
        if ($slack_user_id != "" && $slack_user_id != $user["slack_user_id"]) {
            $user["slack_user_id"] = $email;
        }
        if ($last_email != "" && $last_email != $user["last_email"]) {
            $user["last-email"] = $last_email;
        }
        $users_arr[$user_i] = $user;
        update_option("sb-agents-arr",json_encode($users_arr));
        return "success";
    }
    return "error";
}
function sb_get_files_arr($file_string) {
    //Input link|name?link|name...  Output array(link,link,...)
    $arr = array();
    if ($file_string != "") {
        $arr = explode("?",$file_string);
        for ($i = 0; $i < count($arr); $i++) {
            $arr[$i] =  explode("|",$arr[$i])[0];
        }
    }
    return $arr;
}
function sb_read_bot_response($response) {
    $success = false;
    $msg = "";
    $files_arr = array();
    if ($response != "" && $response != false) {
        $response = json_decode($response, true);
        if ($response != "" && $response != false) {
            try {
                if (isset($response['result']) && isset($response['result']['fulfillment'])) {
                    $msg = $response['result']['fulfillment']['speech'];
                    if ($msg == null) {
                        $msg = $response['result']['fulfillment']['messages'][0]['speech'];
                    }
                    if ($msg == null) {
                        $msg = $response['result']['fulfillment']['displayText'];
                    }
                    if ($msg == null) {
                        $msg_arr = $response['result']['fulfillment']['messages'];
                        for ($i = 0; $i < count($msg_arr); $i++) {
                            if (isset($msg_arr[$i]['payload']) && isset($msg_arr[$i]['payload']['platform'])) {
                                $msg = json_encode($msg_arr[$i]['payload'], JSON_UNESCAPED_UNICODE);
                                break;
                            }
                        }
                    }
                } else {
                    return array("error" => "undefined index response['result']['fulfillment']");
                }
            } catch (Exception $exception) { }
            if ($msg != null) {
                $success = true;
                //[files name|link name|link ...]
                if (strpos($msg,"[files") > -1) {
                    $start = strpos($msg,"[files");
                    $end = strpos($msg,"]",$start);
                    $files = substr($msg, $start + 7, ($end - $start  - 7));
                    $files_arr = explode(" ",$files);
                    $msg = substr($msg, 0, $start);
                }
            }
        }
    }
    return array("success" => $success, "msg" => $msg, "files" => $files_arr);
}
function sb_get_agent_bot() {
    global $sb_config;
    if (!isset($sb_config)) {
        $sb_config = sb_get_settings();
    }
    $bot_img = sb_get($sb_config, "bot-img");
    $bot_name = sb_get($sb_config, "bot-name");
    if ($bot_img == "") $bot_img = sb_get_plugin_url() . "/media/user-1.jpg";
    if ($bot_name == "") $bot_name = "Agent";
    return array("username" => $bot_name, "img" => $bot_img);
}
function sb_get_editor($agent = false) { ?>
<div class="sb-editor">
    <textarea placeholder="<?php _e("Write a message...","sb") ?>"></textarea>
    <div class="sb-btn sb-submit">
        <?php _e("Send","sb") ?>
    </div>
    <div class="sb-attachment"></div>
    <img class="sb-loader" src="<?php echo SB_PLUGIN_URL . "/media/loader.svg" ?>" alt="" />
    <div class="sb-progress">
        <div class="sb-progress-bar" style="width: 0%;"></div>
    </div>
    <div class="sb-attachment-cnt">
        <div class="sb-attachments-list"></div>
    </div>
    <form class="sb-upload-form" action="#" method="post" enctype="multipart/form-data">
        <input type="file" name="files[]" class="sb-upload-files" multiple />
    </form>
    <img class="sb-clear-msg" src="<?php echo SB_PLUGIN_URL . "/media/close-black.svg" ?>" alt="" />
    <div class="sb-clear"></div>
</div>
<?php }
?>
