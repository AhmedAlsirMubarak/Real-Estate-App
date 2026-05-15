<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(20);
        $unreadCount = ContactMessage::where('is_read', false)->count();

        return view('manager.contacts.index', compact('messages', 'unreadCount'));
    }

    public function show(ContactMessage $contact)
    {
        if (!$contact->is_read) {
            $contact->markAsRead();
        }

        return view('manager.contacts.show', compact('contact'));
    }

    public function destroy(ContactMessage $contact)
    {
        $contact->delete();
        return redirect()->route('manager.contacts.index')
            ->with('success', app()->getLocale() === 'ar' ? 'تم حذف الرسالة بنجاح' : 'Message deleted successfully');
    }
}
