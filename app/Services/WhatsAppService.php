<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a WhatsApp message. Returns true on success, false on failure or when disabled.
     */
    public function send(string $phone, string $message): bool
    {
        $provider = config('whatsapp.provider', 'none');

        $normalized = $this->normalize($phone);
        if (!$normalized) {
            Log::warning('WhatsApp: invalid phone number', ['phone' => $phone]);
            return false;
        }

        try {
            return match ($provider) {
                'ultramsg' => $this->sendViaUltraMsg($normalized, $message),
                'meta'     => $this->sendViaMeta($normalized, $message),
                default    => false,
            };
        } catch (\Throwable $e) {
            Log::error('WhatsApp send failed', ['error' => $e->getMessage(), 'phone' => $normalized]);
            return false;
        }
    }

    /**
     * Build the wa.me link for manual sending (fallback / dashboard buttons).
     */
    public function link(string $phone, string $message): ?string
    {
        $normalized = $this->normalize($phone);
        if (!$normalized) return null;
        return 'https://api.whatsapp.com/send?phone=' . $normalized . '&text=' . rawurlencode($message);
    }

    public function isEnabled(): bool
    {
        return in_array(config('whatsapp.provider'), ['ultramsg', 'meta'], true);
    }

    // ── Private ─────────────────────────────────────────────────────────────

    private function sendViaUltraMsg(string $phone, string $message): bool
    {
        $instance = config('whatsapp.instance_id');
        $token    = config('whatsapp.token');

        $response = Http::asForm()->post(
            "https://api.ultramsg.com/{$instance}/messages/chat",
            ['token' => $token, 'to' => $phone, 'body' => $message]
        );

        $json = $response->json();
        $sent = $response->successful() && isset($json['sent']) && $json['sent'] === 'true';

        if (!$sent) {
            Log::warning('UltraMsg send failed', ['response' => $json]);
        }

        return $sent;
    }

    private function sendViaMeta(string $phone, string $message): bool
    {
        $token   = config('whatsapp.token');
        $phoneId = config('whatsapp.phone_id');

        $response = Http::withToken($token)->post(
            "https://graph.facebook.com/v19.0/{$phoneId}/messages",
            [
                'messaging_product' => 'whatsapp',
                'to'                => $phone,
                'type'              => 'text',
                'text'              => ['body' => $message],
            ]
        );

        $sent = $response->successful();
        if (!$sent) {
            Log::warning('Meta WhatsApp send failed', ['response' => $response->json()]);
        }

        return $sent;
    }

    private function normalize(string $phone): ?string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (empty($digits)) return null;

        // Strip leading 00
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        // Prepend Oman country code for local numbers
        if (!str_starts_with($digits, '968') && strlen($digits) <= 8) {
            $digits = '968' . $digits;
        }

        return '+' . $digits;
    }
}
