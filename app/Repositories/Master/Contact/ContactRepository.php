<?php

namespace App\Repositories\Master\Contact;

use App\Models\Contact;
use App\Repositories\BaseRepository;
use App\Services\BotService;
use Illuminate\Support\Facades\Http;

class ContactRepository extends BaseRepository implements ContactRepositoryInterface
{
    protected $model;
    protected $botService;

    public function __construct(Contact $model, BotService $botService)
    {
        parent::__construct($model);
        $this->model = $model;
        $this->botService = $botService;
    }
    public function checkContactByPhoneNumber($phone_number)
    {
        return $this->model->byPhoneNumber($phone_number)->first();
    }
    public function contactFromBot($bot, $phone_number)
    {
        $contact = $this->checkContactByPhoneNumber($phone_number);
        if (is_null($contact)) {
            $jsonUrl = 'https://gist.githubusercontent.com/anubhavshrimal/75f6183458db8c453306f93521e93d37/raw/f77e7598a8503f1f70528ae1cbf9f66755698a16/CountryCodes.json';
            $jsonResponse = Http::get($jsonUrl);

            // Mendekodekan respons JSON menjadi array
            $countryCodes = $jsonResponse->json();
            $country_code = '';
            foreach ($countryCodes as $countryCode) {
                $dialCode = ltrim($countryCode['dial_code'], '+');
                if (strpos($phone_number, $dialCode) === 0) {
                    $country_code = $dialCode;
                }
            }
            $contact_data = $this->botService->getContact($bot, $phone_number);

            $contact_number = ltrim($phone_number, $country_code);
            if (!is_null($contact_data['response'])) {
                if (!isset($contact_data['response']['pushname']))
                {
                    dd($contact_data['response']);
                }
                $data = [
                    'name' => $contact_data['response']['pushname'] ?? $contact_data['response']['verifiedName'],
                    'country_code' => $country_code,
                    'contact_number' => $contact_number,
                    'data' => json_encode($contact_data['response']),
                ];
                $contact = $this->create($data);
            }
        }
        return $contact;
    }
}
