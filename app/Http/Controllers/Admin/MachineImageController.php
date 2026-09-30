<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\MachineImage;
use App\Services\ThumbnailService;
use Illuminate\Support\Facades\DB;

class MachineImageController extends Controller
{
    public function __construct(private readonly ThumbnailService $thumbnails)
    {
    }

    public function destroy(Machine $machine, MachineImage $image)
    {
        // segurança: garantir que a imagem pertence a esta máquina
        if ((int) $image->machine_id !== (int) $machine->id) {
            abort(404);
        }

        // apagar ficheiro(s) do storage (original + miniatura, se existirem)
        $this->thumbnails->deleteImageFiles($image);

        $image->delete();

        return back()->with('success', 'Imagem removida com sucesso.');
    }

    /**
     * Define esta imagem como a principal da máquina.
     *
     * Garante no máximo uma imagem principal por máquina: limpa a flag em todas
     * as imagens da máquina e marca apenas a escolhida, dentro de uma transação.
     */
    public function feature(Machine $machine, MachineImage $image)
    {
        if ((int) $image->machine_id !== (int) $machine->id) {
            abort(404);
        }

        DB::transaction(function () use ($machine, $image) {
            $machine->images()->update(['is_featured' => false]);
            $image->forceFill(['is_featured' => true])->save();
        });

        return back()->with('success', 'Imagem principal definida com sucesso.');
    }
}
