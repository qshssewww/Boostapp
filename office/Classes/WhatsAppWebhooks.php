<?php

/**
 * @property $id
 * @property $phone
 * @property $wamid
 * @property $template_name
 * @property $valid
 * @property $classActId
 * @property $status
 */
class WhatsAppWebhooks extends \Hazzard\Database\Model
{
    protected $table = "boostapp.whatsapp_webhooks";

    public const STATUS_SENT = 0;
    public const STATUS_ANSWERED = 1;
    public const STATUS_INVALID = 2;

}
