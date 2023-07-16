<?php

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use Discord\Parts\WebSockets\VoiceStateUpdate;

include __DIR__.'/vendor/autoload.php';

$envPath = realpath(dirname(__FILE__) . '/env.ini');
$env = parse_ini_file($envPath);

$discord = new Discord([
    'token' => $env['token'],
    'loadAllMembers' => false,
]);

$discord->on('ready', function (Discord $discord) {

    echo "O bot está ouvindo";

    $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) { #TRANSFORMAR TUDO ISSO EM CASES
        if($message->author->bot) return;

        $content = $message->content;
        if(str_contains($content, '!regras')) {
            $message->channel->sendMessage("{$message->author}, aqui está o nosso canal de regras -> <#1130163573927198731>");
        }

        if(str_contains($content, '!cotação')) {
            $url = "https://economia.awesomeapi.com.br/json/last/USD-BRL";
            $cotação = json_decode(file_get_contents($url));
            $real = "R$";
            $message->reply("{$cotação->USDBRL->code}: {$real}{$cotação->USDBRL->bid}");
        }
    });

    $discord->on(Event::VOICE_STATE_UPDATE, function (VoiceStateUpdate $state, Discord $discord, $oldstate) {
        $channel = $discord->getChannel(1129957816229167208);
        $channel->sendMessage("{$state->member->nick} entrou no chat de voz: {$state->channel->name}");
    });

});
    
$discord->run();