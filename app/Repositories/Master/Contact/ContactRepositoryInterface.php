<?php

namespace App\Repositories\Master\Contact;

use App\Repositories\BaseRepositoryInterface;

interface ContactRepositoryInterface extends BaseRepositoryInterface
{
    public function checkContactByPhoneNumber($phone_number);
    public function contactFromBot($bot, $phone_number);
}
