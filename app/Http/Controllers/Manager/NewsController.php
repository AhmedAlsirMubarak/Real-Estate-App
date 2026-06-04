<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\NewsArticle;
use App\Models\NewsArticleImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index()
    {
        $articles = NewsArticle::orderByDesc('published_at')->paginate(15);
        return view('manager.news.index', compact('articles'));
    }

    public function create()
    {
        return view('manager.news.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title_ar'     => 'required|string|max:255',
            'title_en'     => 'nullable|string|max:255',
            'excerpt_ar'   => 'nullable|string|max:500',
            'excerpt_en'   => 'nullable|string|max:500',
            'body_ar'      => 'nullable|string',
            'body_en'      => 'nullable|string',
            'images'       => 'nullable|array',
            'images.*'     => 'image|max:4096',
            'published_at' => 'nullable|date',
            'is_active'    => 'boolean',
            'sort_order'   => 'integer|min:0',
        ]);

        $data['slug'] = $this->uniqueSlug($request->title_ar ?: $request->title_en);

        unset($data['images']);
        if ($request->hasFile('image')) {
            $stored = $this->storeImage($request->file('image'));
            if ($stored) $data['image'] = $stored;
        }

        $data['is_active']  = $request->boolean('is_active', true);
        $data['sort_order'] = $request->input('sort_order', 0);

        $article = NewsArticle::create($data);

        // Upload images
        if ($request->hasFile('images')) {
            $first = true;
            foreach ($request->file('images') as $file) {
                $path = $this->storeImage($file);
                if ($path) {
                    $article->images()->create([
                        'path'       => $path,
                        'is_primary' => $first,
                        'sort_order' => 0,
                    ]);
                    $first = false;
                }
            }
        }

        return redirect()->route('manager.news.edit', $article)
            ->with('success', 'تم إضافة المقال بنجاح');
    }

    public function edit(NewsArticle $news)
    {
        $news->load('images');
        return view('manager.news.edit', compact('news'));
    }

    public function update(Request $request, NewsArticle $news)
    {
        $data = $request->validate([
            'title_ar'     => 'required|string|max:255',
            'title_en'     => 'nullable|string|max:255',
            'excerpt_ar'   => 'nullable|string|max:500',
            'excerpt_en'   => 'nullable|string|max:500',
            'body_ar'      => 'nullable|string',
            'body_en'      => 'nullable|string',
            'images'       => 'nullable|array',
            'images.*'     => 'image|max:4096',
            'published_at' => 'nullable|date',
            'is_active'    => 'boolean',
            'sort_order'   => 'integer|min:0',
        ]);

        unset($data['images']);
        $data['is_active']  = $request->boolean('is_active', true);
        $data['sort_order'] = $request->input('sort_order', 0);

        $news->update($data);

        // Upload new images from the form
        if ($request->hasFile('images')) {
            $existingCount = $news->images()->count();
            $hasPrimary = $existingCount > 0 && $news->images()->where('is_primary', true)->exists();
            $offset = 0;
            foreach ($request->file('images') as $file) {
                $path = $this->storeImage($file);
                if ($path) {
                    $news->images()->create([
                        'path'       => $path,
                        'is_primary' => ! $hasPrimary,
                        'sort_order' => $existingCount + $offset,
                    ]);
                    $hasPrimary = true;
                    $offset++;
                }
            }
        }

        return redirect()->route('manager.news.edit', $news)
            ->with('success', 'تم تحديث المقال بنجاح');
    }

    public function destroy(NewsArticle $news)
    {
        $news->delete();
        return back()->with('success', 'تم حذف المقال');
    }

    public function storeImages(Request $request, NewsArticle $news)
    {
        $request->validate(['images.*' => 'required|image|max:4096']);
        $existingCount = $news->images()->count();
        $hasPrimary = $existingCount > 0 && $news->images()->where('is_primary', true)->exists();
        $offset = 0;

        foreach ($request->file('images', []) as $file) {
            $path = $this->storeImage($file);
            if ($path) {
                $news->images()->create([
                    'path'       => $path,
                    'is_primary' => ! $hasPrimary,
                    'sort_order' => $existingCount + $offset,
                ]);
                $hasPrimary = true;
                $offset++;
            }
        }

        return back()->with('success', 'تم رفع الصور بنجاح');
    }

    public function destroyImage(NewsArticle $news, NewsArticleImage $image)
    {
        abort_if($image->news_article_id !== $news->id, 403);
        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $news->images()->first()?->update(['is_primary' => true]);
        }

        return back()->with('success', 'تم حذف الصورة');
    }

    public function setPrimaryImage(NewsArticle $news, NewsArticleImage $image)
    {
        abort_if($image->news_article_id !== $news->id, 403);
        $news->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);
        return back()->with('success', 'تم تعيين الصورة الرئيسية');
    }

    private function storeImage(\Illuminate\Http\UploadedFile $file): ?string
    {
        $dir      = public_path('storage' . DIRECTORY_SEPARATOR . 'news');
        $ext      = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = 'news_' . time() . '_' . uniqid() . '.' . $ext;
        $dest     = $dir . DIRECTORY_SEPARATOR . $filename;

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        try {
            $file->move($dir, $filename);
            if (file_exists($dest)) {
                return 'news/' . $filename;
            }
        } catch (\Throwable) {}

        $tmp = $file->getRealPath() ?: '';
        if ($tmp && file_exists($tmp) && copy($tmp, $dest)) {
            return 'news/' . $filename;
        }

        return null;
    }

    private function uniqueSlug(string $base): string
    {
        $slug = Str::slug($base) ?: 'article';
        $count = 0;
        $candidate = $slug;
        while (NewsArticle::where('slug', $candidate)->exists()) {
            $candidate = $slug . '-' . ++$count;
        }
        return $candidate;
    }
}
