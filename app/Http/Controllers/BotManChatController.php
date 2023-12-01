<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\BotMan;

class BotManChatController extends Controller
{
    public function invoke()
    {
        $botman = app('botman');
        $botman->hears('{message}', function($botman, $message) {
            if ($message == 'hello') {
                $this->askName($botman);
            }else{
                $botman->reply("Type 'hello' for demo ...");
            }
        });
        $botman->listen();
    }
   
    public function askInfo($botman)
    {
        $botman->ask('Hey There! How are you?', function(Answer $answer) {
            $ans = $answer->getText();
            $this->say('Whats up '.$ans);
        });
    }
}
