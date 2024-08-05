<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SergiX44\Nutgram\Nutgram;

class SetWebhook extends Command
{
    protected $signature = 'telegram:set-webhook';
    protected $description = 'Set the Telegram bot webhook';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $token = config('services.telegram.bot_token');

        // Debugging line to check if the token is being retrieved correctly
        if (!$token) {
            $this->error('Telegram bot token is not set or retrieved correctly.');
            return;
        }

        $bot = new Nutgram($token);
        $webhookUrl = route('webhook'); // Assuming you are running this in a local environment with a publicly accessible URL

        $bot->setWebhook($webhookUrl);
        $this->info('Webhook set to ' . $webhookUrl);
    }
}
