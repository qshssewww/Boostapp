<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/responses/PhoneValidationResponse.php';
require_once __DIR__ . '/../helpers/PasswordHelper.php';
require_once __DIR__ . '/../helpers/PhoneHelper.php';
require_once __DIR__ . '/../../office/Classes/Client.php';
require_once __DIR__ . '/../../office/Classes/Company.php';
require_once __DIR__ . '/../../office/Classes/Users.php';
require_once __DIR__ . '/../../office/services/AuthService.php';
require_once __DIR__ . '/../../office/services/EmailService.php';
require_once __DIR__ . '/../../office/services/OTPService.php';

class LoginController extends BaseController
{
    /**
     * @param $otp
     * @return bool
     */
    public function loginByPhone($otp)
    {
        if (Auth::check()) {
            redirect_to('/office/');
        }

        $this->asJson();
        $response = new PhoneValidationResponse();

        // get the phone
        $phone = $_SESSION['phone'] ?? '';

        // check if phone number is valid
        if (!PhoneHelper::validatePhone($phone)) {
            $message = lang('wrong_phone_login');

            $response->status = 401;
            $response->success = false;
            $response->message = $message;

            return $response->send();
        }

        $phoneForDB = PhoneHelper::shortPhoneNumber($phone);

        // get user by phone number
        $user = Users::where('ContactMobile', 'like', '%' . $phoneForDB)->whereNotNull('MultiUserId')->orderBy('LastActivity', 'desc')->first();

        // check if user is not found
        if (!$user) {
            $message = lang('no_user_found_login_controller');

            $response->status = 401;
            $response->success = false;
            $response->message = $message;

            return $response->send();
        }

        // check if phone number is blocked
        if (!AuthService::canAuth($phone)) {
            $message = lang('try_again_login');

            $response->status = 401;
            $response->success = false;
            $response->blocked = true;
            $response->message = $message;
            $response->count = AuthService::getAttemptsNumber($phone);

            return $response->send();
        }

        // check one-time password
        $otpIsCorrect = OTPService::compareOtp($otp);
        if (!$otpIsCorrect) {
            $response->status = 400;
            $response->success = false;
            $response->message = lang('login_code_error', ['times' => AuthService::getAttemptsNumberLeft($phone)]);

            return $response->send();
        }

        // reset attempts number to prevent blocking next time
        AuthService::resetAttemptsNumber($phone);

        // sign in user
        Auth::loginById($user->id, true);

        $response->message = 'Authorization successful';

        return $response->send();
    }

    /**
     * @param $username
     * @param $password
     * @return void
     */
    public function loginByEmail($username, $password)
    {
        if (Auth::check()) {
            redirect_to('/office/');
        }

        $this->asJson();

        try {
            if (preg_match('/[^\w\d\-_.@+]+/', $username, $m) !== 0) {
                // check if username includes invalid chars
                throw new InvalidArgumentException('The username or password is incorrect');
            }

            $userModel = Users::where('username', $username)
                ->orWhere('email', $username)
                ->orderBy('CompanyNum', 'desc')
                ->first();

            if (!$userModel) {
                throw new InvalidArgumentException('The username or password is incorrect');
            }

            $username = trim($username);
            $password = trim($password);

            if (Auth::login($username, $password, true)) {
                echo json_encode([
                    'status' => 200,
                    'success' => true,
                    'message' => 'Success',
                ]);

                return;
            } else {
                throw new InvalidArgumentException('The username or password is incorrect');
            }
        } catch (\InvalidArgumentException $e) {
            echo json_encode([
                'status' => 400,
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            LoggerService::error($e);

            echo json_encode([
                'status' => 500,
                'success' => false,
                'message' => 'The error occurred. Please, contact support team.',
            ]);
        }
    }

    /**
     * @param $username
     * @return void
     */
    public function remindPassword($username)
    {
        if (Auth::check()) exit;

        if (!isset($username)) exit;

        $validator = Validator::make(array('email' => $username), array('email' => 'required'));

        $this->asJson();

        try {
            if (!$validator->passes()) {
                throw new InvalidArgumentException($validator->errors()->first(), 400);
            }

            $ClientEmail = $username;

            /** @var Users|null $user */
            $user = Users::where('username', '=', $ClientEmail)->orWhere('email', '=', $ClientEmail)->first();
            if (!$user) {
                throw new InvalidArgumentException(lang('no_user_found_login_controller'), 400); // TODO: check translation
            }

            $SettingsInfo = new Company($user->CompanyNum, false);

            $CompanyLink = App::url();
            $CompanyName = $SettingsInfo->CompanyName;
            $displayName = $user->display_name;

            $RandomPassword = PasswordHelper::generate(6);

            $user->password = PasswordHelper::hash($RandomPassword);
            $user->save();

            $subject = $displayName . ', ברוך הבא למערכת BOOSTAPP של ' . $CompanyName . '! פרטי הגישה שלך בפנים';

            EmailService::sendTemplate($ClientEmail, $subject, 'user/remind-password', [
                'displayName' => $displayName,
                'clientId' => null,
                'LunchLink' => $CompanyLink,
                'LunchName' => $CompanyName,
                'ClientEmail' => $ClientEmail,
                'RandomPassword' => $RandomPassword,
            ]);

            echo json_encode([
                'status' => 200,
                'success' => true,
                'message' => lang('remind_password_note_login'),
            ]);
            return;
        } catch (InvalidArgumentException $e) {
            // if we specified error in "try" section
            echo json_encode([
                'status' => $e->getCode(),
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            // some unexpected errors
            LoggerService::error($e);

            echo json_encode([
                'status' => 500,
                'success' => false,
                'message' => lang('error_service_ajax')
            ]);
        }
    }

    /**
     * @param $id
     * @return void
     */
    public function switchMultiUser($multiUserId)
    {
        if (!Auth::check()) {
            redirect_to('/');
        }

        $this->asJson();

        try {
            $user = Auth::user();
            if (!$user) {
                echo json_encode([
                    'status' => 403,
                    'success' => false,
                    'message' => 'Access denied.',
                ]);
                return;
            }

            $oldUserId = $user->id;

            /** @var Users $userToSwitch */
            $userToSwitch = Users::where('id', $multiUserId)->first();
            if (!$userToSwitch || $userToSwitch->multiUserId != Auth::user()->multiUserId) {
                throw new InvalidArgumentException('Wrong user ID. Old -> ' . $oldUserId . ', New -> ' . $userToSwitch->id);
            }

            if (Auth::loginById($userToSwitch->id, true)) {
                echo json_encode([
                    'status' => 200,
                    'success' => true,
                    'message' => '',
                ]);
            } else {
                throw new LogicException('Can\'t sign in with another multiuser. Old -> ' . $oldUserId . ', New -> ' . $userToSwitch->id);
            }
        } catch (\Throwable $e) {
            LoggerService::error($e);
            echo json_encode([
                'status' => $e->getCode(),
                'success' => false,
                'message' => lang('error_service_ajax')
            ]);
        }
    }
}
