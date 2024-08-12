<?php

namespace App\Telegram\Conversations;

use App\Models\User;
use App\Models\VerificationCode;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardRemove;
use SergiX44\Nutgram\Telegram\Types\WebApp\WebAppInfo;

class StartConversation extends Conversation
{
    public $chatId;
    public $messageIds = [];

    public function start(Nutgram $bot)
    {
        $this->chatId = $bot->chat()->id;

        $this->askForPhoneNumber($bot);
    }

    private function askForPhoneNumber(Nutgram $bot)
    {
        $message = $bot->sendMessage(
            'Davom etish uchun raqamingizni +998XXXXXXXXX formatida kiriting yoki ulashing',
            reply_markup: ReplyKeyboardMarkup::make(
                resize_keyboard: true,
                one_time_keyboard: true,
                input_field_placeholder: '+998XXXXXXXXX',
                selective: true
            )->addRow(
                KeyboardButton::make('Mening telefon raqamim', request_contact: true)
            )
        );
        $this->messageIds[] = $message->message_id;
        $bot->stepConversation([$this, 'handlePhoneNumber']);
    }

    public function handlePhoneNumber(Nutgram $bot)
    {
        $message = $bot->message();
        $this->messageIds[] = $message->message_id;
        $phoneNumber = $message->contact->phone_number ?? $message->text;
        if ($this->isValidPhoneNumber($phoneNumber)) {
            $message = $bot->sendMessage("Telefon raqamingiz qabul qilindi: $phoneNumber", reply_markup: ReplyKeyboardRemove::make(remove_keyboard: true));
            // $this->messageIds[] = $message->message_id;
            $this->sendVerificationCode($bot, $phoneNumber);
        } else {
            $message = $bot->sendMessage("Noto'g'ri telefon raqami formati. Iltimos, qayta kiriting.");
            $this->messageIds[] = $message->message_id;
            $this->askForPhoneNumber($bot);
        }
    }

    private function isValidPhoneNumber($phoneNumber)
    {
        return preg_match('/^\+998\d{9}$/', $phoneNumber);
    }

    private function sendVerificationCode(Nutgram $bot, $phoneNumber)
    {
        $telegramUserId = $bot->user()->id;
        $user = User::where('telegram_id', $telegramUserId)->first();
        if (!$user) {
            $user = User::create([
                'phone' => $phoneNumber,
                'telegram_id' => $telegramUserId,
            ]);
        } else {
            $user->update([
                'phone' => $phoneNumber,
            ]);
        }
        $verificationCode = rand(100000, 999999);
        VerificationCode::where('user_id', $user->id)->delete();
        VerificationCode::create([
            'user_id' => $user->id,
            'code' => $verificationCode,
        ]);

        $message = $bot->sendMessage("Tasdiqlash kodi $phoneNumber raqamiga yuborildi: $verificationCode");
        $this->messageIds[] = $message->message_id;
        $this->askForVerificationCode($bot);
    }

    public function askForVerificationCode(Nutgram $bot)
    {
        $message = $bot->sendMessage("Iltimos, tasdiqlash kodini kiriting:");
        $this->messageIds[] = $message->message_id;
        $bot->stepConversation([$this, 'handleVerificationCode']);
    }

    public function handleVerificationCode(Nutgram $bot)
    {
        $telegramUserId = $bot->user()->id;
        $user = User::where('telegram_id', $telegramUserId)->first();
        $enteredCode = $bot->message();
        $this->messageIds[] = $enteredCode->message()->message_id;
        $storedCode = VerificationCode::where('user_id', $user->id)->value('code');

        if ($enteredCode->text == $storedCode) {
            $message = $bot->sendMessage("Tasdiqlash kodi to'g'ri. Rahmat!");
            $this->messageIds[] = $message->message_id;
            $this->sendWebAppLink($bot);
        } else {
            $message = $bot->sendMessage("Noto'g'ri tasdiqlash kodi!");
            $this->messageIds[] = $message->message_id;
            $this->askForVerificationCode($bot);
        }
    }

    private function sendWebAppLink(Nutgram $bot)
    {

        $bot->deleteMessages($bot->chat()->id, $this->messageIds);
        $webAppInfo = new WebAppInfo(url: env('WEB_APP_URL'));
        $message = $bot->sendMessage(
            text: 'Tasdiqlash muvaffaqiyatli yakunlandi. Buyurtma berishingiz mumkin!',
            reply_markup: InlineKeyboardMarkup::make()->addRow(
                InlineKeyboardButton::make(
                    text: 'Korzinka',
                    url: null,
                    callback_data: null,
                    web_app: $webAppInfo
                )
            )
        );
    }
}
