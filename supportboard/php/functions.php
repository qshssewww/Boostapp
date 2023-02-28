<?php
/**
 * ======================================
 * SUPPORT BOARD - FUNCTIONS - PHP ONLY
 * ======================================
 *
 * Various PHP only functions
 */

include_once("config.php");

/**
 * --------------------------------------
 * DATABASE MYSQL
 * --------------------------------------
 */

function get_option($option_name) {
    global $sb_config_mysql;
    if (isset($option_name)) {
        $conn = null;
        if (isset($sb_config_mysql['port']) && $sb_config_mysql['port'] != "") {
            $conn = new mysqli($sb_config_mysql["host"], $sb_config_mysql["username"], $sb_config_mysql["password"], $sb_config_mysql["db"], intval($sb_config_mysql['port']));
        } else {
            $conn = new mysqli($sb_config_mysql["host"], $sb_config_mysql["username"], $sb_config_mysql["password"], $sb_config_mysql["db"]);
        }
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        mysqli_set_charset($conn, 'utf8');
        $result = $conn->query('SELECT value FROM sb WHERE name="' . $option_name . '"');
        $value = false;
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $value = $row['value'];
            }
        }
        $conn->close();
        return $value;
    }
    return false;
}
function update_option($option_name, $value) {
    global $sb_config_mysql;
    if (isset($option_name) && isset($value)) {
        $conn = null;
        if (isset($sb_config_mysql['port']) && $sb_config_mysql['port'] != "") {
            $conn = new mysqli($sb_config_mysql["host"], $sb_config_mysql["username"], $sb_config_mysql["password"], $sb_config_mysql["db"], intval($sb_config_mysql['port']));
        } else {
            $conn = new mysqli($sb_config_mysql["host"], $sb_config_mysql["username"], $sb_config_mysql["password"], $sb_config_mysql["db"]);
        }
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        mysqli_set_charset($conn, 'utf8');
        $value = str_replace("'","\'", $value);
        $query = "INSERT INTO sb (name, value) VALUES('" . $option_name . "', '" . $value . "') ON DUPLICATE KEY UPDATE name='" . $option_name . "', value='" . $value . "'";
        $result = $conn->query($query);
        $conn->close();
        return $result;
    }
    return false;
}
function delete_option($option_name) {
    global $sb_config_mysql;
    if (isset($option_name)) {
        $conn = new mysqli($sb_config_mysql["host"], $sb_config_mysql["username"], $sb_config_mysql["password"], $sb_config_mysql["db"]);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $query = "DELETE FROM sb WHERE name='" . $option_name . "'";
        $conn->query($query);
        $conn->close();
        return true;
    }
    return false;
}
function sb_installation_check() {
    global $sb_config_mysql;
    if ($sb_config_mysql["host"] == "" || $sb_config_mysql["username"] == "" || $sb_config_mysql["db"] == "") {
        return "error_1_db_connection";
    }
    $conn = new mysqli($sb_config_mysql["host"], $sb_config_mysql["username"], $sb_config_mysql["password"], $sb_config_mysql["db"]);
    if ($conn->connect_error) {
        return "error_1_db_connection";
    } else {
        sb_installation();
    }
    $result = $conn->query("SELECT * FROM sb WHERE name='sb-super-admin';");
    $value = "";
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $value = $row['value'];
        }
    }
    if ($value == "") {
        return "error_2_no_super_admin";
    }
    $result = $conn->query("SELECT * FROM sb WHERE name='sb-agents-arr';");
    $value = "";
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $value = $row['value'];
        }
    }
    if ($value == "") {
        return "error_3_no_agent";
    }
    $conn->close();
    return "success";
}

/**
 * --------------------------------------
 * VARIOUS FUNCTIONS
 * --------------------------------------
 */

function sb_installation() {
    global $sb_config_mysql;
    $type = "";
    if (isset($_POST["type"])) $type = $_POST["type"];
    $conn = new mysqli($sb_config_mysql["host"], $sb_config_mysql["username"], $sb_config_mysql["password"], $sb_config_mysql["db"]);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $query = "CREATE TABLE sb (id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(150) NOT NULL UNIQUE, value LONGTEXT NOT NULL)";
    if ($type == "super-admin") {
        $_POST["user"];
        $query = "INSERT INTO sb (name, value) VALUES('sb-super-admin', '" . $_POST["user"] . "|" . $_POST["psw"] ."')";
    }
    $conn->query($query);
    $conn->close();
    return true;
}
function sb_agent_login() {
    $login = "error";
    if (isset($_POST["user"]) && isset($_POST["psw"])) {
        $user = strtolower($_POST["user"]);
        $psw = $_POST["psw"];
        $agents_arr =  json_decode(get_option("sb-agents-arr"), true);
        if ($agents_arr != false) {
            for ($i = 0; $i < count($agents_arr); $i++){
                if (strtolower($agents_arr[$i]["username"]) == $user && $agents_arr[$i]["psw"] == $psw) {
                    session_start();
                    $_SESSION['sb-agent-login'] = array("agent",$agents_arr[$i]);
                    $login = "success";
                    break;
                }
            }
        }
        $super_admin =  get_option("sb-super-admin");
        if ($super_admin != false && strpos($super_admin,"|") > 0) {
            $super_admin = explode("|",$super_admin);
            if (strtolower($super_admin[0]) == $user && $super_admin[1] == $psw) {
                session_start();
                $_SESSION['sb-agent-login'] =  array("super-admin",$super_admin);
                $login = "success";
            }
        }
    }
    die($login);
}
function sb_agent_logout() {
    session_start();
    $_SESSION['sb-agent-login'] = null;
    die("succes");
}
function sb_super_admin_update() {
    if (isset($_POST["user"], $_POST["psw"])) {
        session_start();
        if (isset($_SESSION['sb-agent-login']) && !empty($_SESSION['sb-agent-login'])) {
            if ($_SESSION['sb-agent-login'][0] == "super-admin") {
                update_option("sb-super-admin",$_POST["user"] . "|" . $_POST["psw"]);
                die("success");
            }
        }
    }
    die("error");
}

/**
 * --------------------------------------
 * MAIN EDITOR
 * --------------------------------------
 */
function sb_get_php_editor() { ?>
<div class="sb-editor">
    <textarea placeholder="Write a message..."></textarea>
    <div class="sb-btn sb-submit">Send</div>
    <div class="sb-attachment"></div>
    <img class="sb-loader" src="../media/loader.svg" alt="" />
    <div class="sb-progress">
        <div class="sb-progress-bar" style="width: 0%;"></div>
    </div>
    <div class="sb-attachment-cnt">
        <div class="sb-attachments-list"></div>
    </div>
    <form class="sb-upload-form" action="#" method="post" enctype="multipart/form-data">
        <input type="file" name="files[]" class="sb-upload-files" multiple />
    </form>
    <img class="sb-clear-msg" src="../media/close-black.svg" alt="" />
    <div class="sb-clear"></div>
</div>
<?php }
?>
