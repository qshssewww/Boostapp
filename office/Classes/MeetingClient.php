<?php

require_once __DIR__ . '/Client.php';
require_once __DIR__ . '/Token.php';

/**
 * @property $id
 * @property $CompanyNum
 * @property $ClientId
 * @property $GroupNumber
 * @property $TokenId
 */
class MeetingClient extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.meeting_client';

    private $_client;
    private $_token;

    /**
     * @return Client
     */
    public function client()
    {
        if (empty($this->_client)) {
            $this->_client = (new Client($this->ClientId));
        }
        return $this->_client;
    }

    /**
     * @return Token|\Hazzard\Database\Model
     */
    public function token()
    {
        if (empty($this->_token)) {
            $this->_token = Token::find($this->TokenId);
        }
        return $this->_token;
    }
}
