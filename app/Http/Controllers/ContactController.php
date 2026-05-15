<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ], [
            'name.required'    => 'الاسم مطلوب',
            'email.required'   => 'البريد الإلكتروني مطلوب',
            'email.email'      => 'صيغة البريد الإلكتروني غير صحيحة',
            'subject.required' => 'الموضوع مطلوب',
            'message.required' => 'الرسالة مطلوبة',
            'message.min'      => 'الرسالة يجب أن تكون على الأقل 10 أحرف',
        ]);

        ContactMessage::create($validated);

        return redirect()->route('home')->with('contact_success', 'تم إرسال رسالتك بنجاح! سنتواصل معك قريباً.');
    }
}
