<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSection;
use App\Models\WebsiteItem;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    private array $pages = [
        'home'   => ['label_ar' => 'الصفحة الرئيسية', 'label_en' => 'Home Page',    'icon' => 'home'],
        'global' => ['label_ar' => 'عام (تذييل)',      'label_en' => 'Global (Footer)', 'icon' => 'globe'],
    ];

    private array $sectionMeta = [
        'hero'                => ['label_ar' => 'البانر الرئيسي',              'has_items' => false, 'item_type' => null],
        'stats'               => ['label_ar' => 'الإحصائيات',                 'has_items' => true,  'item_type' => 'stat'],
        'featured_properties' => ['label_ar' => 'قسم العقارات المميزة',       'has_items' => false, 'item_type' => null],
        'services'            => ['label_ar' => 'عرض العقار المميز',          'has_items' => false, 'item_type' => null, 'has_showcase' => true],
        'property_types'      => ['label_ar' => 'أنواع العقارات',             'has_items' => true,  'item_type' => 'type'],
        'about'               => ['label_ar' => 'عن الشركة',                  'has_items' => true,  'item_type' => 'feature'],
        'cta'                 => ['label_ar' => 'بانر الدعوة للتواصل',        'has_items' => false, 'item_type' => null],
        'testimonials'        => ['label_ar' => 'آراء العملاء',               'has_items' => true,  'item_type' => 'testimonial'],
        'partners'            => ['label_ar' => 'شركاؤنا',                    'has_items' => true,  'item_type' => 'partner'],
        'contact'             => ['label_ar' => 'معلومات التواصل',            'has_items' => true,  'item_type' => 'contact'],
        'footer'              => ['label_ar' => 'تذييل الصفحة',              'has_items' => false, 'item_type' => null],
    ];

    public function index()
    {
        $pages = $this->pages;
        $sections = WebsiteSection::selectRaw('page, count(*) as count')->groupBy('page')->pluck('count', 'page');
        return view('manager.website.index', compact('pages', 'sections'));
    }

    public function showPage(string $page)
    {
        abort_unless(isset($this->pages[$page]), 404);
        $sections = WebsiteSection::where('page', $page)
            ->withCount('items')
            ->orderBy('sort_order')
            ->get();
        $pageInfo = $this->pages[$page];
        $sectionMeta = $this->sectionMeta;
        return view('manager.website.page', compact('page', 'pageInfo', 'sections', 'sectionMeta'));
    }

    public function editSection(string $page, string $key)
    {
        $section  = WebsiteSection::where('page', $page)->where('key', $key)->firstOrFail();
        $meta     = $this->sectionMeta[$key] ?? [];
        $items    = $section->items()->orderBy('sort_order')->get();
        $pageInfo = $this->pages[$page];

        // For the showcase section pass the properties list for the picker
        $properties = ($meta['has_showcase'] ?? false)
            ? \App\Models\Property::where('status', 'active')->orderBy('name')->get(['id', 'name'])
            : collect();

        return view('manager.website.edit-section', compact('section', 'meta', 'items', 'page', 'key', 'pageInfo', 'properties'));
    }

    public function updateSection(Request $request, string $page, string $key)
    {
        $section = WebsiteSection::where('page', $page)->where('key', $key)->firstOrFail();

        $data = $request->only([
            'title_ar', 'title_en', 'subtitle_ar', 'subtitle_en',
            'body_ar', 'body_en', 'button_text_ar', 'button_text_en', 'button_url',
        ]);

        // Handle extra fields
        $extra = $section->extra ?? [];
        foreach (['btn2_text_ar','btn2_text_en','btn2_url','badge_ar','badge_en','whatsapp','twitter','instagram','facebook','linkedin','showcase_property_id'] as $field) {
            if ($request->has($field)) {
                $extra[$field] = $request->input($field);
            }
        }
        $data['extra'] = $extra;

        // Image upload
        if ($request->hasFile('image')) {
            $path = $this->storeWebsiteFile($request->file('image'), 'section_' . $page . '_' . $key);
            if ($path) $data['image'] = $path;
        }

        $section->update($data);

        return redirect()->route('manager.website.section.edit', [$page, $key])
            ->with('success', 'تم حفظ محتوى القسم بنجاح');
    }

    // ── ITEMS ────────────────────────────────────────────────────────────────

    public function createItem(string $page, string $key)
    {
        $section = WebsiteSection::where('page', $page)->where('key', $key)->firstOrFail();
        $meta = $this->sectionMeta[$key] ?? [];
        return view('manager.website.items.create', compact('section', 'meta', 'page', 'key'));
    }

    public function storeItem(Request $request, string $page, string $key)
    {
        $section = WebsiteSection::where('page', $page)->where('key', $key)->firstOrFail();

        $data = array_merge(
            $request->only(['title_ar','title_en','subtitle_ar','subtitle_en','body_ar','body_en','icon','value','url','sort_order']),
            ['section_id' => $section->id, 'is_active' => $request->boolean('is_active', true)]
        );

        if ($request->hasFile('image')) {
            $path = $this->storeWebsiteFile($request->file('image'), 'item_' . $key);
            if ($path) $data['image'] = $path;
        }

        WebsiteItem::create($data);

        return redirect()->route('manager.website.section.edit', [$page, $key])
            ->with('success', 'تم إضافة العنصر بنجاح');
    }

    public function editItem(string $page, string $key, WebsiteItem $item)
    {
        $section = WebsiteSection::where('page', $page)->where('key', $key)->firstOrFail();
        abort_unless($item->section_id === $section->id, 403);
        $meta = $this->sectionMeta[$key] ?? [];
        return view('manager.website.items.edit', compact('section', 'item', 'meta', 'page', 'key'));
    }

    public function updateItem(Request $request, string $page, string $key, WebsiteItem $item)
    {
        $section = WebsiteSection::where('page', $page)->where('key', $key)->firstOrFail();
        abort_unless($item->section_id === $section->id, 403);

        $data = array_merge(
            $request->only(['title_ar','title_en','subtitle_ar','subtitle_en','body_ar','body_en','icon','value','url','sort_order']),
            ['is_active' => $request->boolean('is_active', true)]
        );

        if ($request->hasFile('image')) {
            $path = $this->storeWebsiteFile($request->file('image'), 'item_' . $key);
            if ($path) $data['image'] = $path;
        }

        $item->update($data);

        return redirect()->route('manager.website.section.edit', [$page, $key])
            ->with('success', 'تم تحديث العنصر بنجاح');
    }

    private function storeWebsiteFile(\Illuminate\Http\UploadedFile $file, string $prefix): ?string
    {
        $dir      = public_path('storage' . DIRECTORY_SEPARATOR . 'website');
        $filename = $prefix . '_' . time() . '.' . $file->getClientOriginalExtension();
        $dest     = $dir . DIRECTORY_SEPARATOR . $filename;

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Strategy 1: Laravel's move() — uses move_uploaded_file internally, falls back to rename
        try {
            $file->move($dir, $filename);
            if (file_exists($dest)) {
                return 'website/' . $filename;
            }
        } catch (\Throwable) {}

        // Strategy 2: raw copy from temp path
        $tmp = $file->getRealPath() ?: sys_get_temp_dir() . DIRECTORY_SEPARATOR . $file->getClientOriginalName();
        if ($tmp && file_exists($tmp) && copy($tmp, $dest)) {
            return 'website/' . $filename;
        }

        return null;
    }

    public function destroyItem(string $page, string $key, WebsiteItem $item)
    {
        $section = WebsiteSection::where('page', $page)->where('key', $key)->firstOrFail();
        abort_unless($item->section_id === $section->id, 403);
        $item->delete();
        return redirect()->route('manager.website.section.edit', [$page, $key])
            ->with('success', 'تم حذف العنصر');
    }
}
