<?php
require_once 'app/init.php';

$settingsPage = App::url('#settings-modal');

$scopes = array(
	'facebook'  => array('email', 'user_about', 'user_birthday', 'user_website'),
	'google'    => array('email', 'profile', 'gplus_me', 'userinfo_profile'),
	'linkedin'  => array('r_fullprofile', 'r_emailaddress', 'r_contactinfo'),
	'microsoft' => array('basic', 'birthday', 'emails', 'postal_addresses'),
	'instagram' => array('basic'),
	'github'    => array('user'),
	'spotify'   => array('user-read-email', 'user-read-birthdate'),
);

$provider = isset($_GET['provider']) ? strtolower($_GET['provider']) : '';

$providers = Config::get('auth.providers', array());

unset($providers['yahoo']);

if (array_key_exists($provider, $providers) && !isset($_GET['error']) && !isset($_GET['denied'])) {

	if (Auth::check() && isset($_GET['disconnect'])) {
		Usermeta::delete(Auth::user()->id, "{$provider}_id");
		Usermeta::delete(Auth::user()->id, "{$provider}_avatar");
		Usermeta::delete(Auth::user()->id, 'avatar_type', $provider);
		Usermeta::delete(Auth::user()->id, "{$provider}_profile");

		redirect_to($settingsPage);
	}

	Session::delete('oauth_user');

	$credentials = new OAuth\Common\Consumer\Credentials(
	    Config::get("services.{$provider}.id"),
	    Config::get("services.{$provider}.secret"),
	    App::url("oauth.php?provider={$provider}")
	);

	$scope = isset($scopes[$provider]) ? $scopes[$provider] : array();
	$storage = new OAuth\Common\Storage\Session;
	$service = with(new OAuth\ServiceFactory)->createService($provider, $credentials, $storage, $scope);

	if ($provider == 'twitter') {
		if (empty($_GET['oauth_token'])) {
			$oauth_token = $service->requestRequestToken()->getRequestToken();
			$authUrl = $service->getAuthorizationUri(compact('oauth_token'));
		} else {
		    try {
			    $token = $storage->retrieveAccessToken(ucfirst($provider));
			    $service->requestAccessToken(
			        @$_GET['oauth_token'],
			        @$_GET['oauth_verifier'],
			        $token->getRequestTokenSecret()
			    );
			} catch(Exception $e) {}
		}
	} else {
		if (empty($_GET['code'])) {
			$authUrl = $service->getAuthorizationUri();
		} else {
			try {
				$state = isset($_GET['state']) ? $_GET['state'] : null;
				$service->requestAccessToken($_GET['code'], $state);
			} catch(Exception $e) {}
		}
	}

	if (isset($authUrl)) redirect_to($authUrl);

	try {
		$user = with(new OAuth\UserData\ExtractorFactory)->get($service);
		
		$user = array(
			'id'       => $user->getUniqueId(),
			'email'    => $user->getEmail(),
			'username' => str_replace('.', '', $user->getUsername()),
			'first_name' => $user->getfirstName(),
			'last_name' => $user->getLastName(),
			'full_name' => $user->getfullName(),
			'about'    => $user->getDescription(),
			'profile'  => $user->getProfileUrl(),
			'avatar'   => $user->getImageUrl(),
			'location' => $user->getLocation(),
			'links'    => $user->getWebsites(),
			'birthday' => $user->getField('birthday'),
			'locale'   => $user->getField('locale'),
			'gender'   => $user->getField('gender'),
			'provider' => $provider,
		);

		if ($provider == 'twitter') {
			$user['avatar'] = str_replace('_normal', '', $user['avatar']);
		}

		Session::set('oauth_user', $user);

		$storage->clearAllTokens()->clearAllAuthorizationStates();

	} catch(Exception $e) {}

	redirect_to( App::url('oauth.php') );
}

$user = Session::get('oauth_user');

if (empty($user)) redirect_to(App::url());

$provider = $user['provider'];

$userId = Usermeta::newQuery()->where('meta_key', "{$provider}_id")->where('meta_value', $user['id'])->pluck('user_id');

if (Auth::check()) {
	if (is_null($userId)) {
		Usermeta::add(Auth::user()->id, "{$provider}_id", $user['id'], true);
		
		Usermeta::add(Auth::user()->id, "{$provider}_avatar", $user['avatar'], true);

		if (!empty($user['profile'])) {
			Usermeta::add(Auth::user()->id, "{$provider}_profile", $user['profile'], true);
		}
	}

	redirect_to($settingsPage);
}

if (is_null($userId)) {
	$data = array(
		'email' => $user['email'],
		'username' => $user['username'],
		'usermeta' => array(
			"{$provider}_id" => $user['id'],
			'avatar_type' => $provider,
			"{$provider}_avatar" => $user['avatar'],
		)
	);

	if (empty($data['username'])) {
		$data['username'] = sprintf('%s%s', $user['first_name'], $user['last_name']);
	}

	if (!empty($user['profile'])) {
		$data['usermeta']["{$provider}_profile"] = $user['profile'];
	}

	if (array_key_exists($user['locale'], Config::get('app.locales'))) {
		$data['usermeta']['locale'] = $user['locale'];
	}

	foreach (UserFields::all('user') as $key => $field) {
		if (isset($user[$key])) {
    		$data['usermeta'][$key] = escape($user[$key]);
    	}
    }

    if (isset($_POST['submit']) && csrf_filter()) {
		if (isset($_POST['username'])) {
			$data['username'] = $_POST['username'];
		}

		if (isset($_POST['email'])) {
			$data['email'] = $_POST['email'];
		}

		foreach (UserFields::all('signup') as $key => $field) {
    		if (isset($_POST[$key])) {
	    		$data['usermeta'][$key] = escape($_POST[$key]);
	    	}
	    }
	}

	if (OAuth::signup($provider, $data)) {
		OAuth::login($provider, $user['id']);
	}
} else {
	OAuth::login($provider, $user['id']);
}

if (OAuth::passes()) {
	$url = Config::get('auth.login_redirect');
	$url = empty($url) ? App::url() : $url;
	redirect_to($url);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>EasyLogin Pro</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?php echo asset_url('css/vendor/bootstrap.min.css') ?>" rel="stylesheet">
	<link href="<?php echo asset_url('css/bootstrap-custom.css') ?>" rel="stylesheet">
	<link href="<?php echo asset_url('css/main.css') ?>" rel="stylesheet">
	<style>
		.container {
			background: #fff;
			-webkit-box-shadow: 0 1px 3px rgba(0,0,0,.13);
			box-shadow: 0 1px 3px rgba(0,0,0,.13);
			max-width: 500px;
			margin: 30px auto 20px;
			padding: 20px;
		}
	</style>
</head>
<body>
	<div class="container">
		<h3 class="page-header">
			<?php _e('main.connecting_with'); echo Config::get("auth.providers.{$provider}"); ?>
			<a href="<?php echo App::url() ?>" class="btn btn-info btn-sm"><?php _e('main.cancel') ?></a>
		</h3>

		<?php if (OAuth::fails()) {
			echo '<div class="alert alert-danger alert-dismissible"><span class="close" data-dismiss="alert">&times;</span><ul>';
			foreach (OAuth::errors()->all('<li>:message</li>') as $error) {
				echo $error;
			}
			echo '</ul></div>';
		} ?>

		<?php if (is_null($userId)): ?>
			<form action="" method="POST" class="clearfix">
				<?php csrf_input() ?>

				<?php if (Config::get('auth.require_username') && OAuth::errors()->has('username')): ?>
					<div class="form-group">
				        <label for="signup-username"><?php _e('main.username') ?></label>
				        <input type="text" name="username" id="signup-username" class="form-control" value="<?php echo set_value('username', $data['username']) ?>">
				    </div>
				<?php endif ?>

				<?php if (OAuth::errors()->has('email')): ?>
				    <div class="form-group">
				        <label for="signup-email"><?php _e('main.email') ?></label>
				        <input type="text" name="email" id="signup-email" class="form-control" value="<?php echo set_value('email', $data['email']) ?>">
				    </div>
				<?php endif ?>

				<?php foreach (UserFields::with((array) $data['usermeta'])->all('signup') as $key => $f): ?>
					<?php if (OAuth::errors()->has($key)): ?>
						<?php echo UserFields::buildField($key) ?>
					<?php endif ?>
				<?php endforeach ?>

				<div class="form-group">
					<button type="submit" name="submit" class="btn btn-primary"><?php _e('main.signup') ?></button>
				</div>
			</form>

			<p class="help-block"><span class="label label-warning">!</span> <?php _e('main.oauth_extra') ?></p>
		<?php endif ?>
	</div>
</body>
