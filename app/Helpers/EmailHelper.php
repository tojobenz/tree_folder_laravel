<?php
namespace App\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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
        try {
            // main mail
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Log::warning('Invalid email address provided: ' . $email);
                return response()->json([
                    'error' => true,
                    'message' => 'Adresse email invalide'
                ]);
            }

            // destination validation 
            if (!is_array($destinataire_principale)) {
                $destinataire_principale = [$destinataire_principale];
            }

            // filter invalid email
            $validEmails = array_filter($destinataire_principale, function($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });

            if (empty($validEmails)) {
                Log::warning('No valid email addresses in recipients');
                return response()->json([
                    'error' => true,
                    'message' => 'Aucun destinataire valide'
                ]);
            }

            self::$email = $email;
            self::$subject = $subject;
            self::$destinataire_principale = $validEmails;

            Mail::send($view, $data, function($message) {
                $message
                    ->to(self::$email)
                    ->bcc(self::$destinataire_principale)
                    ->subject(self::$subject);
            });

            if (count(Mail::failures()) > 0) {
                Log::error('Email sending failed for recipients: ' . implode(', ', Mail::failures()));
                return response()->json([
                    'error' => true,
                    'message' => 'Une erreur technique est survenue lors de l’envoi de l’email'
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Email envoyé avec succès'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Email sending error: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage()
            ]);
        }
    }
}
