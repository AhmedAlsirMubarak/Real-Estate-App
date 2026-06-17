<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(20);
        $unreadCount = ContactMessage::where('is_read', false)->count();

        return view('employee.contacts.index', compact('messages', 'unreadCount'));
    }

    public function show(ContactMessage $contact)
    {
        if (!$contact->is_read) {
            $contact->markAsRead();
        }

        return view('employee.contacts.show', compact('contact'));
    }
}
