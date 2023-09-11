<?php

namespace App\Service;

class FOGDiscordWebhookService {
    public function __construct(private string $webhook){

    }

    public function send(string $msg, array $embeds){
        $json_data = json_encode([
            'content' => '<@&278289623933911041>'.$msg,
            'username' => 'Website',
            'embeds' => $embeds
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $ch = curl_init($this->webhook);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        curl_close($ch);
    }
}