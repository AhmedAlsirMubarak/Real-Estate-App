<?php

return [
    /*
     | Provider: 'ultramsg' | 'meta' | 'none'
     |
     | ultramsg — UltraMsg.com (popular in MENA, affordable)
     |   WHATSAPP_PROVIDER=ultramsg
     |   WHATSAPP_INSTANCE_ID=instance123
     |   WHATSAPP_TOKEN=your_token
     |
     | meta — Meta WhatsApp Cloud API (official)
     |   WHATSAPP_PROVIDER=meta
     |   WHATSAPP_TOKEN=your_bearer_token
     |   WHATSAPP_PHONE_ID=your_phone_number_id
     |
     | none — Disable server-side sending (use dashboard WA links only)
    */
    'provider'    => env('WHATSAPP_PROVIDER', 'none'),
    'instance_id' => env('WHATSAPP_INSTANCE_ID', ''),
    'token'       => env('WHATSAPP_TOKEN', ''),
    'phone_id'    => env('WHATSAPP_PHONE_ID', ''),
];
