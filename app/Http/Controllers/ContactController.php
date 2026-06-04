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
        $isAr = app()->getLocale() === 'ar';

        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ], $isAr ? [
            'name.required'    => 'الاسم مطلوب',
            'email.required'   => 'البريد الإلكتروني مطلوب',
            'email.email'      => 'صيغة البريد الإلكتروني غير صحيحة',
            'subject.required' => 'الموضوع مطلوب',
            'message.required' => 'الرسالة مطلوبة',
            'message.min'      => 'الرسالة يجب أن تكون على الأقل 10 أحرف',
        ] : [
            'name.required'    => 'Name is required',
            'email.required'   => 'Email is required',
            'email.email'      => 'Invalid email address',
            'subject.required' => 'Subject is required',
            'message.required' => 'Message is required',
            'message.min'      => 'Message must be at least 10 characters',
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
