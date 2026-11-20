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
    public function send(
        string $to_email, 
        string $to_name, 
        string $subject,
        array $vars=[],
        int $template_id = 8453422

    ):bool { 
        // dd('La méthode Mail.php est bien appelée');
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
                        'Email'=> 'contact.vitegourmand.restau@gmail.com',
                        'Name'=> "Vite & Gourmand"

                    ],
                    'To'=>[
                        [
                            'Email'=> $to_email,
                            'Name'=> $to_name
                        ]
                    ],
                    'TemplateID'=> $template_id,
                    'TemplateLanguage'=> true,
                    'Subject'=>$subject,
                    'Variables'=> $vars
                ],
            ],
        ];
        // envoi de l'email
        $response = $mj->post(
            Resources::$Email,
            ['body'=>$body]
        );
        return $response->success();



    }
}