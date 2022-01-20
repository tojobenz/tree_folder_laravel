<?php
namespace App\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EmailHelper {
     /**
     * @var
     */
    private static $email, $subject, $name, $destinataire_principale;

    /**
     * @param $view
     * @param $data
     * @param $email
     * @param $subject
     * @param $name
     * @return \Illuminate\Http\JsonResponse
     */
    public static function sendMail($view, $data, $email, $subject, $destinataire_principale) {
        self::$email = $email;
        self::$subject = $subject;
        self::$destinataire_principale = $destinataire_principale;
        Mail::send($view, $data, function($message) {
            $message
                ->to(self::$email)
                ->bcc(self::$destinataire_principale)
                ->subject(self::$subject);
        });

        if ( count(Mail::failures()) > 0) {
            return response()->json([
                'error' => true,
                'message' => 'Une erreur technique est survenue lors de l’envoi de l’email'
            ]);
        }else{
            return response()->json([
                'success' => true,
                'message' => 'Un email de confirmation vous est envoyez !'
            ]);
        }
    }
}
