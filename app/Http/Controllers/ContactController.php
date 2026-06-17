<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        // Honeypot: bots fill this, real browsers leave it empty
        if ($request->filled('website')) {
            return Redirect::to(route('home') . '#contact')->with('contact_success',
                app()->getLocale() === 'ar'
                    ? 'تم إرسال رسالتك بنجاح! سنتواصل معك قريباً.'
                    : 'Your message has been sent successfully! We will contact you soon.'
            );
        }

        $isAr = app()->getLocale() === 'ar';

        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email:rfc|max:255',
            'phone'   => ['nullable', 'string', 'max:20', 'regex:/^[+0-9\s\-\(\)]*$/'],
            'subject' => 'required|in:استفسار عام,طلب عرض سعر,الإبلاغ عن مشكلة,طلب شراكة,أخرى',
            'message' => 'required|string|min:10|max:5000',
        ], $isAr ? [
            'name.required'    => 'الاسم مطلوب',
            'email.required'   => 'البريد الإلكتروني مطلوب',
            'email.email'      => 'صيغة البريد الإلكتروني غير صحيحة',
            'phone.regex'      => 'رقم الجوال يجب أن يحتوي على أرقام فقط',
            'subject.required' => 'الموضوع مطلوب',
            'subject.in'       => 'الموضوع المحدد غير صالح',
            'message.required' => 'الرسالة مطلوبة',
            'message.min'      => 'الرسالة يجب أن تكون على الأقل 10 أحرف',
            'message.max'      => 'الرسالة يجب ألا تتجاوز 5000 حرف',
        ] : [
            'name.required'    => 'Name is required',
            'email.required'   => 'Email is required',
            'email.email'      => 'Invalid email address',
            'phone.regex'      => 'Phone number may only contain digits, spaces, +, -, ()',
            'subject.required' => 'Subject is required',
            'subject.in'       => 'The selected subject is invalid',
            'message.required' => 'Message is required',
            'message.min'      => 'Message must be at least 10 characters',
            'message.max'      => 'Message must not exceed 5000 characters',
        ]);

        if ($validator->fails()) {
            return Redirect::to(route('home') . '#contact')
                ->withErrors($validator)
                ->withInput();
        }

        ContactMessage::create($validator->validated());

        $msg = $isAr
            ? 'تم إرسال رسالتك بنجاح! سنتواصل معك قريباً.'
            : 'Your message has been sent successfully! We will contact you soon.';

        return Redirect::to(route('home') . '#contact')->with('contact_success', $msg);
    }
}
