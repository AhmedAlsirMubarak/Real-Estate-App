<x-app-layout>
<div class="max-w-5xl mx-auto px-4 py-8">

  <div class="flex items-center gap-3 mb-6">
    <a href="{{ route('manager.news.index') }}" class="text-sm font-medium transition" style="color:#64748b" onmouseover="this.style.color='#0f2444'" onmouseout="this.style.color='#64748b'">
      {{ app()->getLocale() === 'ar' ? '← الأخبار' : '← News' }}
    </a>
    <span style="color:#cbd5e1">/</span>
    <h1 class="text-xl font-black" style="color:#1e293b">
      {{ app()->getLocale() === 'ar' ? 'تعديل المقال' : 'Edit Article' }}
    </h1>
  </div>

  @if($errors->any())
  <div class="mb-5 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
    <ul class="text-sm text-red-700 space-y-1">
      @foreach($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  <form action="{{ route('manager.news.update', $news) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('manager.news._form')
  </form>

</div>
</x-app-layout>
