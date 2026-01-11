<?php
namespace App\Http\Controllers;

use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\QueryException;

class ProductVariantController extends Controller
{
    private $cacheDuration = 1440; // 24 ساعة

    /**
     * عرض القائمة مع الكاش الذكي
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $lowStock = $request->has('low_stock') && $request->low_stock;

        // في حالة البحث أو الفلتر، لا نستخدم الكاش لضمان دقة النتائج
        if ($search || $lowStock) {
            $variants = $this->buildQuery($request)->paginate(25)->withQueryString();
            return view('product.index', compact('variants'));
        }

        // كاش للصفحات الأساسية فقط
        $page = $request->input('page', 1);
        $cacheKey = "variants.index.page.{$page}";

        $variants = Cache::remember($cacheKey, $this->cacheDuration, function () use ($request) {
            return $this->buildQuery($request)->paginate(25)->withQueryString();
        });

        return view('product.index', compact('variants'));
    }

    /**
     * بناء الاستعلام المشترك
     */
    private function buildQuery(Request $request)
    {
        return ProductVariant::query()
            ->select(['id', 'sku', 'name', 'size', 'color', 'price', 'cost', 'stock_qty', 'reorder_level'])
            ->when($request->low_stock, function ($q) {
                return $q->whereColumn('stock_qty', '<=', 'reorder_level');
            })
            ->when($request->input('search'), function ($q, $search) {
                return $q->where(function ($sub) use ($search) {
                    $sub->where('sku', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('color', 'like', "%{$search}%")
                        ->orWhere('size', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id');
    }

    public function create()
    {
        return view('product.create');
    }

    /**
     * التخزين: الـ SKU والباركود والكاش يتم معالجتهم في الـ Observer
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'nullable|string|max:255',
            'color'         => 'nullable|string|max:50',
            'size'          => 'nullable|string|max:10',
            'cost'          => 'required|numeric|min:0',
            'price'         => 'required|numeric|min:0',
            'stock_qty'     => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
        ]);

        $variant = ProductVariant::create($data);

        return redirect()
            ->route('variants.edit', $variant)
            ->with('success', 'تم إنشاء الصنف بنجاح بنظام التوليد التلقائي');
    }

    public function edit(ProductVariant $variant)
    {
        // محاولة جلب البيانات من الكاش لسرعة الاستجابة
        $variant = Cache::get("variant.{$variant->id}") ?? $variant;
        return view('product.edit', compact('variant'));
    }

    /**
     * التحديث: الـ SKU محمي في الـ Observer ضد التلاعب
     */
    public function update(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'name'          => 'nullable|string|max:255',
            'color'         => 'nullable|string|max:50',
            'size'          => 'nullable|string|max:10',
            'cost'          => 'required|numeric|min:0',
            'price'         => 'required|numeric|min:0',
            'stock_qty'     => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
        ]);

        $variant->update($validated);

        return redirect()
            ->route('variants.edit', $variant)
            ->with('success', 'تم تحديث البيانات وتحديث الكاش تلقائياً');
    }


 public function printLabelsForm(ProductVariant $variant)

    {

        // جيب من الكاش

        $variant = $this->getCachedVariant($variant->id) ?? $variant;



        return view('product.labels', compact('variant'));

    }



    public function printLabels(Request $request, ProductVariant $variant)

    {

        $stock = (int) ($variant->stock_qty ?? 0);

        if ($stock <= 0) {

            return back()->with('error', 'لا يوجد مخزون لهذا المنتج!');

        }



        $barcodes = \App\Services\BarcodeService::syncToStock($variant);



        return view('barcodes.preview', [

            'variant' => $variant,

            'barcodes' => $barcodes,

        ]);

    }



    public function printInventory(Request $request)

    {

        $request->validate([

            'selected' => 'required|array',

            'selected.*' => 'exists:product_variants,id'

        ]);



        $ids = $request->selected;



        return redirect()->route('inventory.print', ['ids' => $ids])->with('_blank', true);

    }



    public function destroy(ProductVariant $variant)
    {
        try {
            $variant->delete();
            return back()->with('success', 'تم حذف الصنف بنجاح');
        } catch (QueryException $e) {
            if ($e->getCode() == '23000') {
                return back()->with('error', 'لا يمكن حذف الصنف لارتباطه بحركات مخزنية أو فواتير.');
            }
            return back()->with('error', 'حدث خطأ غير متوقع.');
        }
    }

    /**
     * البحث السريع (Live Search)
     */
    public function liveSearch(Request $request)
    {
        $search = $request->get('search', '');
        
        // جلب نتائج سريعة (كاش لمدة 5 دقائق فقط للبحث المتكرر)
        $cacheKey = "search.live." . md5($search);
        
        $variants = Cache::remember($cacheKey, 5, function () use ($search) {
            return ProductVariant::query()
                ->where('sku', 'like', "%$search%")
                ->orWhere('name', 'like', "%$search%")
                ->orderByDesc('id')
                ->limit(50)
                ->get();
        });

        return view('product.partials.rows', compact('variants'))->render();
    }
/**
 * عرض صفحة التعديل الجماعي
 */
public function bulkEdit()
{
    // جلب المنتجات مرتبة حسب الـ SKU لسهولة الوصول
    $variants = ProductVariant::select('id', 'sku', 'name', 'stock_qty')
        ->orderBy('id','asc')
        ->get();

/*
dd([
        'total_count' => $variants->count(), // عدد العناصر في الكولكشن
    ]);
*/


    return view('product.bulk-edit', compact('variants'));
}


public function bulkUpdate(Request $request)
{
    $data = $request->input('variants', []);

    foreach ($data as $id => $qty) {
        if ($qty === null || $qty === '') continue;

        // تحديث الكمية
        $variant = ProductVariant::find($id);
        if ($variant) {
            $variant->update(['stock_qty' => (int) $qty]);
            // الكاش سيتم تحديثه تلقائياً عن طريق الـ Observer الذي قمنا بإنشائه سابقاً
        }
    }

    // الرد بناءً على نوع الطلب
    if ($request->ajax() || $request->has('is_ajax')) {
        return response()->json(['success' => true, 'message' => 'تم التحديث']);
    }

    return redirect()->route('variants.bulk.edit')->with('success', 'تم تحديث الكميات بنجاح');
}
}