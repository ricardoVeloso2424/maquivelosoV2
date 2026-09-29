<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Machine;
use App\Services\ThumbnailService;
use App\Support\PriceNormalizer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MachineController extends Controller
{
    private const MAX_IMAGE_UPLOAD_FILES = 8;
    private const MAX_IMAGE_UPLOAD_SIZE_KB = 5120;

    public function __construct(private readonly ThumbnailService $thumbnails)
    {
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $category = (string) $request->get('category', '');
        $status = (string) $request->get('status', '');

        $machines = Machine::query()
            ->with([
                'category:id,name',
                'firstImage:id,machine_id,path,thumb_path,sort_order',
                'featuredImage:id,machine_id,path,thumb_path,sort_order,is_featured',
            ])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('name', 'like', "%{$q}%")
                        ->orWhere('brand', 'like', "%{$q}%")
                        ->orWhere('model', 'like', "%{$q}%");
                });
            })
            ->when($category !== '', fn ($query) => $query->where('category_id', $category))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = Category::query()->orderBy('name')->get();

        return view('admin.machines.index', compact('machines', 'q', 'categories', 'category', 'status'));
    }

    public function create()
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.machines.create', [
            'machine' => new Machine(),
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateMachineData($request);

        $machine = Machine::create($data);

        $this->storeImages($machine, $request);

        return redirect()->route('admin.machines.index')->with('success', 'Máquina criada com sucesso.');
    }

    public function show(Machine $machine)
    {
        $machine->load(['category', 'images']);

        return view('admin.machines.show', compact('machine'));
    }

    public function edit(Machine $machine)
    {
        $machine->load(['category', 'images']);

        $categories = Category::query()->orderBy('name')->get();

        return view('admin.machines.edit', compact('machine', 'categories'));
    }

    public function update(Request $request, Machine $machine)
    {
        $data = $this->validateMachineData($request);

        $machine->update($data);

        $this->storeImages($machine, $request);

        return redirect()->route('admin.machines.index')->with('success', 'Máquina atualizada com sucesso.');
    }

    public function destroy(Machine $machine)
    {
        $machine->load('images');

        foreach ($machine->images as $img) {
            $this->thumbnails->deleteImageFiles($img);
        }

        $machine->delete();

        return redirect()->route('admin.machines.index')->with('success', 'Máquina removida com sucesso.');
    }

    public function updateStatus(Request $request, Machine $machine)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in($this->statusKeys())],
        ]);

        $machine->update([
            'status' => $data['status'],
        ]);

        return response()->json(['ok' => true]);
    }

    private function storeImages(Machine $machine, Request $request): void
    {
        $files = $request->file('images', []);
        if (!is_array($files) || count($files) === 0) {
            return;
        }

        $machine->loadMissing('images');

        $nextSort = (int) ($machine->images->max('sort_order') ?? -1) + 1;

        foreach ($files as $file) {
            if (!$file) continue;

            $path = $file->store('machines', 'public');

            $machine->images()->create([
                'path' => $path,
                'thumb_path' => $this->thumbnails->generate($path),
                'sort_order' => $nextSort,
            ]);

            $nextSort++;
        }
    }

    private function validateMachineData(Request $request): array
    {
        // Accept Portuguese/European price formats (e.g. "1.200,50", "1 200,50",
        // "1200,50") by normalizing to a canonical decimal string before the
        // numeric validation runs. Genuinely invalid input is left untouched so
        // the numeric rule still rejects it with a proper error message.
        $rawPrice = (string) $request->input('price', '');
        if (trim($rawPrice) !== '') {
            $request->merge([
                'price' => PriceNormalizer::normalize($rawPrice) ?? $rawPrice,
            ]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in($this->statusKeys())],
            'description' => ['nullable', 'string'],
            'featured' => ['nullable', 'boolean'],
            'negotiable' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array', 'max:' . self::MAX_IMAGE_UPLOAD_FILES],
            'images.*' => ['file', 'image', 'max:' . self::MAX_IMAGE_UPLOAD_SIZE_KB],
        ]);

        $data['featured'] = $request->boolean('featured');
        $data['negotiable'] = $request->boolean('negotiable');

        return $data;
    }

    /**
     * Chaves de estado válidas, a partir de config/machines.php.
     *
     * @return array<int, string>
     */
    private function statusKeys(): array
    {
        return array_keys((array) config('machines.statuses', []));
    }
}
