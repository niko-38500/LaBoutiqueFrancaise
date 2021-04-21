<?php

namespace App\Classes;

use Mailjet\Client;
use Mailjet\Resources;

class MailJet 
{
    private $public_api_key = 'f1caeb7d306b30ab0a2bbbab00ebb5db';
    private $private_api_key = '839c8740162ca9997da64a959d414f56';

    public function sendAccountValidation($to_email, $to_name, $validationLink) {
        $mj = new Client($this->public_api_key, $this->private_api_key, true, ['version' => 'v3.1']);
        
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "testdeveloppement38@gmail.com",
                        'Name' => "no_reply@laboutiquefrancaise.fr"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 2567105,
                    'TemplateLanguage' => true,
                    'Subject' => "Validez votre compte",
                    'Variable' => [
                        'account_validation' => $validationLink
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() && var_dump($response->getData());
    }
}