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
                
            case str_contains($content, '!botão'):

                $button = Button::new(Button::STYLE_SUCCESS)
                    ->setLabel('Aqui é um botão');
                $button1 = Button::new(Button::STYLE_SUCCESS)
                    ->setLabel('Aqui é um');

                $select = SelectMenu::new()
                    ->setPlaceholder("Clique aqui para escolher a moeda!")
                    ->addOption(Option::new("USD"))
                    ->addOption(Option::new('EUR'));
                    
                $button->setListener(function (Interaction $interaction) {
                    $interaction->respondWithMessage(MessageBuilder::new()
                        ->setContent('Apenas testando resposta do botão'));
                }, $discord);

                $button1->setListener(function (Interaction $interaction) {
                    $interaction->respondWithMessage(MessageBuilder::new()
                        ->setContent('Apenas testando'));
                }, $discord);

                $row = ActionRow::new()
                    ->addComponent($button)
                    ->addComponent($button1);

                $embed = new Embed($discord);
                $embed->setTitle('Qual moeda?')
                        ->setColor( 0xFF0000 );

                $select->setListener(function (Interaction $interaction, Collection $options) {
                    foreach ($options as $option) {
                        echo $option->getValue().PHP_EOL;
                    }
                
                    $interaction->respondWithMessage(MessageBuilder::new()->setContent('thanks!'));
                }, $discord);

                $message->channel->sendMessage(MessageBuilder::new()
                    ->addEmbed($embed)
                    ->addComponent($row)
                    ->addComponent($select));                    
                break;

            case str_contains($content, '!cotação'):
                $embed = new Embed($discord);
                $embed->setTitle('Escolha abaixo qual moeda quer ver a cotação do dia')
                      ->setColor( 0xFF0000 );                 
                $message->channel->sendEmbed($embed);                

                $url = "https://economia.awesomeapi.com.br/json/last/USD-BRL";
                $cotação = json_decode(file_get_contents($url));
                $real = "R$";                
                $message->reply("{$cotação->USDBRL->code}: {$real}{$cotação->USDBRL->bid}");
                break;
        }
    });
});
    
$discord->run();