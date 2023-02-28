<?php
/**
 * ======================================
 * SUPPORT BOARD - PHP ADMIN
 * ======================================
 *
 * Documentation at board.support/docs/php
 */

session_start();
include("core.php");
$init_check = sb_installation_check();
$users_arr_string = "var sb_users_arr = [];";
$agents_arr_string = "var sb_agents_arr = [];";
$agent_logged_in = false;
$agent_type = "";
if (isset($_SESSION['sb-agent-login']) && !empty($_SESSION['sb-agent-login'])) {
    $agent_logged_in = true;
    if ($_SESSION['sb-agent-login'][0] == "super-admin") $agent_type = "super-admin";
    else $agent_type = "agent";
}
if ($init_check == "success" && $agent_logged_in) {
    $users_arr_string = str_replace('\\"','"', get_option("sb-users-arr"));
    $agents_arr_string = str_replace('\\"','"', get_option("sb-agents-arr"));
    if ($users_arr_string != "" && $users_arr_string != false) {
        $users_arr_string = "var sb_users_arr = '" . $users_arr_string . "';";
    } else {
        $users_arr_string = "var sb_users_arr = '';";
    }
    if ($agents_arr_string != "" && $agents_arr_string != false && strlen($agents_arr_string) > 5) {
        $agents_arr_string = " var sb_agents_arr = '" . $agents_arr_string . "';\n";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Support Board - Admin</title>
    <script src="jquery.min.js"></script>
    <link rel="stylesheet" href="../include/admin.css" />
    <link rel="stylesheet" href="admin.css" />
    <script id="sb-php-init" src="../include/admin.js"></script>
    <script src="../php/admin.js"></script>
    <script><?php echo $users_arr_string . " " . $agents_arr_string . " var sb_wp_users_arr = []; var sb_current_wp_user = ''; var sb_ajax_url; var sb_plugin_url = '" . sb_get_plugin_url() . "'"; ?></script>
</head>
<body>
    <header></header>
    <div id="sb-admin" class="settings-cnt sb-php">
        <input type="hidden" name="save_array_json" id="save_array_json" value='<?php echo str_replace("'", "&#96;", json_encode($sb_config))  ?>' />
        <form class="sb-upload-form" action="#" method="post" enctype="multipart/form-data">
            <input type="file" name="files[]" class="sb-upload-php" />
        </form>
        <div class="cnt-header">
            <img src="../media/logo-white.png" alt="" />
            <a class="sb-php-logout" href="#">
                <img src="../media/logout.svg" />Logout
            </a>
            <a class="sb-doc-link" href="https://board.support/docs/php/">Documentation</a>
            <a class="sb-version" href="#">v 1.2.7</a>
        </div>
        <div class="cnt-main <?php echo "type-" . $agent_type ?>">
            <?php
 if ($init_check == "success") {
                      if ($agent_logged_in) {
            ?>
            <div class="tab-plugin">
                <ul class="nav-plugin">
                    <li class="active li-tab-tickets">
                        <a id="tab-tickets" href="#">Tickets</a>
                    </li>
                    <li class="li-tab-users">
                        <a id="tab-users" href="#">Users</a>
                    </li>
                    <li class="li-tab-agents">
                        <a id="tab-agents" href="#">Agents</a>
                    </li>
                    <?php if ($agent_type == "super-admin") echo '<li class="li-tab-settings"><a id="tab-settings" href="#">Settings</a></li>'; ?>
                </ul>
                <div class="panel-plugin panel-tickets active">
                    <div class="sb-list sb-all-tickets">
                        <div class="sb-user-all-parent">
                            <div class="sb-btn sb-all-users-guests active">All</div>
                            <div class="sb-btn sb-all-users">Users</div>
                            <div class="sb-btn sb-all-guests">Guests</div>
                            <div class="sb-clear"></div>
                        </div>
                        <div class="sb-all-tickets-list">
                            <div class="sb-list-msg">You don't have any ticket, the users's messages will appear here.</div>
                        </div>
                    </div>
                    <div class="sb-list sb-user-tickets">
                        <img class="sb-loader" src="../media/loader.svg" alt="" />
                        <div class="sb-user-tickets-parent">
                            <div class="sb-btn sb-user-tickets-back">Back to tickets list</div>
                            <div class="sb-btn-text sb-user-tickets-delete">Delete conversation</div>
                            <div class="sb-user-tickets-cnt"></div>
                            <?php sb_get_php_editor() ?>
                        </div>
                    </div>
                </div>
                <div class="panel-plugin panel-users">
                    <table class="table-users sb-table">
                        <thead>
                            <tr>
                                <td>ID</td>
                                <td>Username or Email</td>
                                <td>Email</td>
                                <td>Password</td>
                                <?php
                          if (isset($sb_config)) {
                              if ($sb_config["user-extra-1"] != "") echo "<td>" . $sb_config["user-extra-1"] . "</td>";
                              if ($sb_config["user-extra-2"] != "") echo "<td>" . $sb_config["user-extra-2"] . "</td>";
                              if ($sb_config["user-extra-3"] != "") echo "<td>" . $sb_config["user-extra-3"] . "</td>";
                              if ($sb_config["user-extra-4"] != "") echo "<td>" . $sb_config["user-extra-4"] . "</td>";
                          }
                                ?>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <hr class="space s" />
                    <a id="sb-btn-save" class="button button-primary">Save</a>
                    <a id="sb-btn-add-new-user" class="button action">Add new user</a>
                    <span class="sb-msg sb-msg-success">Saved</span>
                    <span class="sb-msg sb-msg-error">Every user must have username and password.</span>
                </div>
                <div class="panel-plugin panel-agents">
                    <table class="table-agents sb-table wp-list-table widefat striped users fixed">
                        <thead>
                            <tr>
                                <td>ID</td>
                                <td>Username</td>
                                <td>Email</td>
                                <td>Password</td>
                                <?php if ($sb_config["slack-token"] !== "") echo '<td class="slack-td">Slack user</td>' ?>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <hr class="space s" />
                    <a id="sb-btn-save-agent" class="button button-primary">Save</a>
                    <a id="sb-btn-add-new-agent" class="button action">Add new agent</a>
                    <span class="sb-msg sb-msg-success-agent">Saved</span>
                    <span class="sb-msg sb-msg-error-agent">Every user must have username and password.</span>
                </div>
                <?php if ($agent_type == "super-admin") { ?>
                <div class="panel-plugin panel-settings">
                    <div class="panel-inner">
                        <h2>
                            Super admin
                        </h2>
                        <div class="item-row">
                            <div class="item-title">
                                <h4>
                                    Login informations
                                </h4>
                                <p>
                                    Change here username and password of the super admin.
                                </p>
                            </div>
                            <div class="item-setting">
                                <div class="item-input">
                                    <p>Username</p>
                                    <input id="super-admin-edit-username" placeholder="" value="<?php echo "" ?>" type="text" />
                                </div>
                                <div class="item-input">
                                    <p>Password</p>
                                    <input id="super-admin-edit-password" placeholder="" value="<?php echo "" ?>" type="password" />
                                </div>
                                <div class="item-input">
                                    <p>Retype password</p>
                                    <input id="super-admin-edit-password-2" placeholder="" value="<?php echo "" ?>" type="password" />
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <hr class="space s" />
                        <a id="sb-btn-save-super-admin" class="button button-primary">Save</a>
                        <span class="sb-msg sb-msg-success-super-admin">Saved</span>
                        <div class="clear"></div>
                    </div>
                    <div class="panel-inner">
                        <h2>
                           Slack
                        </h2>
                        <div class="item-row">
                            <div class="item-title">
                                <h4>
                                    Start
                                </h4>
                                <p>
                                    Click the button to start setting the Slack synchronization. Localhost not receive messages, only live domains.
                                    Insert the access token and the channel ID into the <i>slack-token</i> and <i>slack-channel</i> settings of file <i>config.php</i>.
                                </p>
                            </div>
                            <div class="item-setting">
                                <a href="https://board.support/slack/?customer_url=<?php echo sb_get_plugin_url() ?>" target="_blank" class="button action">
                                    Start Slack synchronization
                                </a>
                            </div>
                        </div>
                        <hr />
                        <div class="item-row">
                            <div class="item-title">
                                <h4>
                                    Test Slack
                                </h4>
                                <p>
                                    Send a test message to your Slack channel, this test check only send function, not read function.
                                </p>
                            </div>
                            <div class="item-setting">
                                <a id="sb-slack-test" class="button action">Send test message to Slack</a>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="panel-inner">
                        <h2>
                            General
                        </h2>
                        <div class="item-row">
                            <div class="item-title">
                                <h4>
                                    Delete all tickets
                                </h4>
                                <p>
                                    Clear the database and delete the tickets of all the users, use this function only if you have problems with the plugin.
                                </p>
                            </div>
                            <div class="item-setting">
                                <a href="#" class="button action sb-btn-delete-tickets">Delete all tickets</a>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
                <?php } ?>
            </div>
            <?php } else { ?>
            <div class="box-init">
                <div class="init-step">
                    <div class="init-header">
                        <p>Login</p>
                    </div>
                    <hr class="space s" />
                    <div class="input-row">
                        <p>Username</p>
                        <input id="login-username" type="text" value="" />
                    </div>
                    <div class="input-row">
                        <p>Password</p>
                        <input id="login-psw" type="password" value="" />
                    </div>
                    <hr class="space xs" />
                    <p class="sb-msg sb-msg-error sb-msg-error-login">Sorry, that password is incorrect.</p>
                    <a class="button button-primary login-submit">Login</a>
                </div>
            </div>
            <?php
 }
                  } else {
            ?>
            <div class="box-init">
                <h4>
                    Plugin installation
                </h4>
                <p>
                    Welcome to Support Board for PHP. Before getting started the plugin need to be configured with the following informations. Please type the informations in the form below.
                </p>
                <?php if ($init_check == "error_1_db_connection") { ?>
                <div class="init-step init-step-1">
                    <div class="init-header">
                        <div>1</div>
                        <p>Database connection</p>
                    </div>
                    <div class="init-text">
                        Open the file
                        <i>supportboard/php/config.php</i>and insert the accesses of your MySQL database. You need to create a new database or use an existing one.
                        After the accesses has been inserted click the button below, if the connection is valid you can continue to the next step.
                    </div>
                    <a class="button button-primary" onclick="location.reload()">Continue</a>
                </div>
                <?php
 }
                      if ($init_check == "error_2_no_super_admin") {
                ?>
                <div class="init-step init-step-2">
                    <div class="init-header">
                        <div>2</div>
                        <p>Super admin</p>
                    </div>
                    <div class="init-text">
                        Insert username and password for the super admin user. The super admin is the only user with full privileges and the only user that can create and delete agents.
                    </div>
                    <div class="input-row">
                        <p>Username</p>
                        <input id="super-admin-username" type="text" value="" />
                    </div>
                    <div class="input-row">
                        <p>Password</p>
                        <input id="super-admin-psw" type="password" value="" />
                    </div>
                    <div class="input-row">
                        <p>Retype password</p>
                        <input id="super-admin-psw-2" type="password" value="" />
                    </div>
                    <hr class="space xs" />
                    <a class="button button-primary">Continue</a>
                </div>
                <?php
 }
                      if ($init_check == "error_3_no_agent") {
                ?>
                <div class="init-step init-step-3">
                    <div class="init-header">
                        <div>3</div>
                        <p>First agent</p>
                    </div>
                    <div class="init-text">
                        Insert username, password and email for the first agent, all fields are required. You can add more agents later.
                    </div>
                    <div class="input-row">
                        <p>Username</p>
                        <input id="agent-username" type="text" value="" />
                    </div>
                    <div class="input-row">
                        <p>Email</p>
                        <input id="agent-email" type="email" value="" />
                    </div>
                    <div class="input-row">
                        <p>Password</p>
                        <input id="agent-psw" type="password" value="" />
                    </div>
                    <div class="input-row">
                        <p>Retype password</p>
                        <input id="agent-psw-2" type="password" value="" />
                    </div>
                    <hr class="space xs" />
                    <a class="button button-primary">Finish!</a>
                </div>

                <?php
  }
                  }
                ?>
            </div>
        </div>
    </div>
    <footer></footer>
</body>
</html>

