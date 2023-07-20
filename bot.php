<?php

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use Discord\Parts\Embed\Embed;
use Discord\Builders\Components\ActionRow;
use Discord\Builders\Components\Button;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Interaction;
use Discord\Builders\Components\SelectMenu;
use Discord\Builders\Components\Option;
use Discord\Helpers\Collection;

include __DIR__.'/vendor/autoload.php';

$envPath = realpath(dirname(__FILE__) . '/env.ini');
$env = parse_ini_file($envPath);

$discord = new Discord([
    'token' => $env['token2'],
]);

$discord->on('ready', function (Discord $discord) {

    echo "O bot está ouvindo";

    $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
        if($message->author->bot) return;

        $content = $message->content;
        switch($content) {
            case str_contains($content, '!regras'):
                $message->channel->sendMessage("{$message->author}, ainda não temos um canal de regras!");
                break;
                
            case str_contains($content, '!cotação'):
                $moedas = ["USD", "EUR", "GBP", "JPY"];

                $menu = SelectMenu::new()
                    ->setPlaceholder("Clique aqui para escolher a moeda!");
                    foreach($moedas as $moeda) {
                        $menu->addOption(Option::new("$moeda"));
                    }

                $menu->setListener(function (Interaction $interaction, Collection $options) {
                    foreach ($options as $option) {
                        echo $option->getValue().PHP_EOL;
                    }

                    $moeda = $option->getLabel();
                    $url = "https://economia.awesomeapi.com.br/json/last/{$moeda}";
                    $cotação = json_decode(file_get_contents($url));
                    $par = $moeda . "BRL";
                    $real = "R$"; 
                
                    $interaction->respondWithMessage(MessageBuilder::new()->setContent("$real {$cotação->$par->bid}"));
                }, $discord);

                $message->channel->sendMessage(MessageBuilder::new()
                    ->addComponent($menu));                    
                break;

            case str_contains($content, '!afk'):
                $motivos = ["Banheiro", "Entretenimento", "Fora de casa", "Refeição", "Estudando"];
                $menu = SelectMenu::new()
                    ->setPlaceholder("Clique aqui para escolher o motivo!");
                    foreach($motivos as $motivo) {
                        $menu->addOption(Option::new("$motivo"));
                    }

                $menu->setListener(function (Interaction $interaction, Collection $options) {
                    foreach ($options as $option) {
                        echo $option->getValue().PHP_EOL;
                    }
                
                    $interaction->acknowledge();
                }, $discord);

                $message->channel->sendMessage(MessageBuilder::new()
                    ->addComponent($menu));                    
                break;
        }
    });
});
    
$discord->run();