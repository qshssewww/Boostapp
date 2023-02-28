<?php

require_once __DIR__ . '/../../app/helpers/PhoneHelper.php';

// TODO: To run the test execute in console: php .\tests\Authentication\PhoneHelperTest.php

(new PhoneHelperTest())->run();

class PhoneHelperTest
{
    /**
     * @return void
     */
    public function run()
    {
        echo "\e[1;33;40m" . ">>> " . self::class . "\e[0m\n";

        $this->testProcessPhone();
    }

    /**
     * @return void
     */
    public function testProcessPhone()
    {
        $caseName = 'Phone number without code and leading 0';
        $phone = '551234567';

        $result = PhoneHelper::processPhone($phone);

        if ($result === null) {
            $this->printError($caseName);
        } else {
            $this->printSuccess($caseName);
        }


        $caseName = 'Phone number with leading 0 without country code';
        $phone = '0551234567';
        $result = PhoneHelper::processPhone($phone);

        if ($result === null) {
            $this->printError($caseName);
        } else {
            $this->printSuccess($caseName);
        }

        $caseName = 'Phone number with with country code without +';
        $phone = '972551234567';
        $result = PhoneHelper::processPhone($phone);

        if ($result === null) {
            $this->printError($caseName);
        } else {
            $this->printSuccess($caseName);
        }


        $caseName = 'Phone number with with country code with +';
        $phone = '+972551234567';
        $result = PhoneHelper::processPhone($phone);

        if ($result === null) {
            $this->printError($caseName);
        } else {
            $this->printSuccess($caseName);
        }


        $caseName = 'Phone number with with country code with + and 0 before phone number';
        $phone = '+9720551234567';
        $result = PhoneHelper::processPhone($phone);

        if ($result === null) {
            $this->printError($caseName);
        } else {
            $this->printSuccess($caseName);
        }

        $caseName = 'Wrong phone number with wrong number of digits - return null';
        $phone = '1234567';
        $result = PhoneHelper::processPhone($phone);

        if ($result !== null) {
            $this->printError($caseName);
        } else {
            $this->printSuccess($caseName);
        }
    }

    /**
     * @param $caseName
     * @return void
     */
    protected function printError($caseName)
    {
        $this->printMessage($caseName, 'ERROR', 31);
    }

    /**
     * @param $caseName
     * @return void
     */
    protected function printSuccess($caseName)
    {
        $this->printMessage($caseName, 'SUCCESS', 32);
    }

    /**
     * @param $caseName
     * @param $status
     * @param $colorCode
     * @return void
     */
    protected function printMessage($caseName, $status, $colorCode)
    {
        echo "\e[0;$colorCode;40m" . "\t >>> " . $status . "\t" .  $caseName . "\e[0m\n";
    }
}
