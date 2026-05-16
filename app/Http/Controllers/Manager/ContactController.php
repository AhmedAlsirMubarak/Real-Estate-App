<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    public function index()
    {
        ContactMessage::where('is_read', false)->update(['is_read' => true]);

        $messages = ContactMessage::latest()->paginate(20);
        $unreadCount = 0;

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
