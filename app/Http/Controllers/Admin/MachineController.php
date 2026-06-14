<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Machine;
use App\Models\MachineImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MachineController extends Controller
{
    private const MAX_IMAGE_UPLOAD_FILES = 8;
    private const MAX_IMAGE_UPLOAD_SIZE_KB = 5120;
    private const THUMB_MAX_WIDTH = 600;

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
            $this->deleteImageFiles($img);
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
                'thumb_path' => $this->generateThumbnail($path),
                'sort_order' => $nextSort,
            ]);

            $nextSort++;
        }
    }

    private function validateMachineData(Request $request): array
    {
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

    /**
     * Apaga o ficheiro original e a respetiva miniatura (se existirem).
     */
    private function deleteImageFiles(MachineImage $image): void
    {
        $disk = Storage::disk('public');

        foreach ([$image->path, $image->thumb_path] as $path) {
            $path = (string) ($path ?? '');
            if ($path !== '' && $disk->exists($path)) {
                $disk->delete($path);
            }
        }
    }

    /**
     * Gera uma miniatura redimensionada (máx. THUMB_MAX_WIDTH de largura) usando
     * a extensão GD nativa do PHP. Devolve o caminho da miniatura ou null quando:
     *  - a extensão GD não está disponível;
     *  - o ficheiro não é uma imagem válida/suportada;
     *  - a imagem já é mais pequena do que a largura máxima (usa-se o original).
     *
     * Quando devolve null, o accessor thumb_url faz fallback para o original,
     * por isso nunca há páginas sem imagem por causa disto.
     */
    private function generateThumbnail(string $originalPath): ?string
    {
        if (!extension_loaded('gd')) {
            return null;
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($originalPath)) {
            return null;
        }

        $fullPath = $disk->path($originalPath);

        $info = @getimagesize($fullPath);
        if ($info === false) {
            return null;
        }

        [$width, $height] = $info;
        $type = $info[2] ?? null;

        if (!$width || !$height || $width <= self::THUMB_MAX_WIDTH) {
            return null;
        }

        $source = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($fullPath),
            IMAGETYPE_PNG => @imagecreatefrompng($fullPath),
            IMAGETYPE_GIF => @imagecreatefromgif($fullPath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($fullPath) : false,
            default => false,
        };

        if (!$source) {
            return null;
        }

        $newWidth = self::THUMB_MAX_WIDTH;
        $newHeight = (int) round($height * ($newWidth / $width));

        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        // Fundo branco para achatar transparências (miniaturas saem como JPEG).
        $white = imagecolorallocate($thumb, 255, 255, 255);
        imagefilledrectangle($thumb, 0, 0, $newWidth, $newHeight, $white);
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        ob_start();
        imagejpeg($thumb, null, 80);
        $contents = ob_get_clean();

        imagedestroy($source);
        imagedestroy($thumb);

        if ($contents === false || $contents === '') {
            return null;
        }

        $thumbPath = 'machines/thumbs/' . pathinfo($originalPath, PATHINFO_FILENAME) . '.jpg';
        $disk->put($thumbPath, $contents);

        return $thumbPath;
    }
}
