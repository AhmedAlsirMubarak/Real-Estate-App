<x-app-layout>
<div class="max-w-5xl mx-auto px-4 py-8">

  {{-- Header --}}
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-black" style="color:#1e293b">
        {{ app()->getLocale() === 'ar' ? 'أخبار الموقع' : 'Latest News' }}
      </h1>
      <p class="text-sm mt-1" style="color:#64748b">
        {{ app()->getLocale() === 'ar' ? 'إدارة مقالات ومستجدات الموقع' : 'Manage news articles and updates' }}
      </p>
    </div>
    <a href="{{ route('manager.news.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white transition"
       style="background:#0f2444" onmouseover="this.style.background='#1a3a6b'" onmouseout="this.style.background='#0f2444'">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
      {{ app()->getLocale() === 'ar' ? 'مقال جديد' : 'New Article' }}
    </a>
  </div>

  @if(session('success'))
  <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl px-4 py-3">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-green-600 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
    <span class="text-sm font-semibold text-green-800">{{ session('success') }}</span>
  </div>
  @endif

  <div class="bg-white rounded-2xl border overflow-hidden" style="border-color:#e2e8f0">
    @if($articles->isEmpty())
    <div class="text-center py-20" style="color:#94a3b8">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-14 h-14 mx-auto mb-3 opacity-40"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z"/></svg>
      <p class="text-sm">{{ app()->getLocale() === 'ar' ? 'لا توجد مقالات بعد' : 'No articles yet' }}</p>
    </div>
    @else
    <div class="overflow-x-auto"><table class="w-full text-sm">
      <thead>
        <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0">
          <th class="px-5 py-3 text-start font-semibold" style="color:#475569">{{ app()->getLocale() === 'ar' ? 'العنوان' : 'Title' }}</th>
          <th class="px-5 py-3 text-start font-semibold hidden sm:table-cell" style="color:#475569">{{ app()->getLocale() === 'ar' ? 'تاريخ النشر' : 'Published' }}</th>
          <th class="px-5 py-3 text-start font-semibold hidden md:table-cell" style="color:#475569">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
          <th class="px-5 py-3 text-end font-semibold" style="color:#475569">{{ app()->getLocale() === 'ar' ? 'إجراءات' : 'Actions' }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach($articles as $article)
        <tr style="border-bottom:1px solid #f1f5f9" onmouseover="this.style.background='#fafbfc'" onmouseout="this.style.background=''">
          <td class="px-5 py-3.5">
            <div class="flex items-center gap-3">
              @if($article->image)
              <img src="{{ $article->imageUrl() }}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0" alt="">
              @else
              <div class="w-10 h-10 rounded-lg flex-shrink-0 flex items-center justify-center" style="background:#e2e8f0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="color:#94a3b8"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
              </div>
              @endif
              <div>
                <p class="font-semibold" style="color:#1e293b">{{ Str::limit($article->title_ar, 60) }}</p>
                @if($article->title_en)
                <p class="text-xs mt-0.5" style="color:#94a3b8">{{ Str::limit($article->title_en, 60) }}</p>
                @endif
              </div>
            </div>
          </td>
          <td class="px-5 py-3.5 hidden sm:table-cell" style="color:#64748b">
            {{ $article->published_at?->format('d M Y') ?? '—' }}
          </td>
          <td class="px-5 py-3.5 hidden md:table-cell">
            @if($article->is_active)
            <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full" style="background:#dcfce7;color:#15803d">
              <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
              {{ app()->getLocale() === 'ar' ? 'نشط' : 'Active' }}
            </span>
            @else
            <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full" style="background:#f1f5f9;color:#64748b">
              {{ app()->getLocale() === 'ar' ? 'مخفي' : 'Hidden' }}
            </span>
            @endif
          </td>
          <td class="px-5 py-3.5 text-end">
            <div class="flex items-center justify-end gap-2">
              <a href="{{ route('manager.news.edit', $article) }}"
                 class="text-xs font-semibold px-3 py-1.5 rounded-lg border transition"
                 style="border-color:#e2e8f0;color:#475569"
                 onmouseover="this.style.borderColor='#0f2444';this.style.color='#0f2444'"
                 onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#475569'">
                {{ app()->getLocale() === 'ar' ? 'تعديل' : 'Edit' }}
              </a>
              <form action="{{ route('manager.news.destroy', $article) }}" method="POST" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'تأكيد الحذف؟' : 'Confirm delete?' }}')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg border transition"
                  style="border-color:#fca5a5;color:#dc2626"
                  onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background=''">
                  {{ app()->getLocale() === 'ar' ? 'حذف' : 'Delete' }}
                </button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table></div>
    @if($articles->hasPages())
    <div class="px-5 py-4 border-t" style="border-color:#e2e8f0">
      {{ $articles->links() }}
    </div>
    @endif
    @endif
  </div>

</div>
</x-app-layout>
