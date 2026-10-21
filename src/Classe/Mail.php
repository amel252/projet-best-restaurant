<?php

namespace App\Classe;
//  importe le client php de mailjet
use Mailjet\Client;
//  permet d'utiliser la ressource de l'api d'envoi
use Mailjet\Resources;

class Mail
//  permet de faire une classe reutilisable , au lieu de réecrire le code mailjet dans chaque cntls
{
    //  recoit l'adresse mail du destinataire , son nom sujet , contenu 
    public function send($to_email, $to_name, $subject,$content):bool
    {
         
        $mj= new Client(
            $_ENV['MJ_APIKEY_PUBLIC'],
            $_ENV['MJ_APIKEY_PRIVATE'], 
            true, 
            ['version'=>'v3.1']
        );
        //  contient le msg à envoyer 
        $body=[
            'Messages'=>[
                [
                    'From'=>[
                        'Email'=> 'amelghanem25@gmail.com',
                        'Name'=> "Best restaurant"

                    ],
                    'To'=>[
                        [
                            'Email'=> $to_email,
                            'Name'=> $to_name
                        ]
                    ],
                    'Subject'=>$subject,
                    'TextPart'=>'Greetings from Mailjet!',
                    'HTMLPart'=>$content
                ],
            ],
        ];
        // envoi de l'email
        $response = $mj->post(Resources::$Email,['body'=>$body]);
        return $response->success();



    }
}