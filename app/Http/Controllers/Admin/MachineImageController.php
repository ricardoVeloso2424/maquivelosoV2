<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\MachineImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MachineImageController extends Controller
{
    public function destroy(Machine $machine, MachineImage $image)
    {
        // segurança: garantir que a imagem pertence a esta máquina
        if ((int) $image->machine_id !== (int) $machine->id) {
            abort(404);
        }

        // apagar ficheiro(s) do storage (original + miniatura, se existirem)
        $disk = Storage::disk('public');
        foreach ([$image->path, $image->thumb_path] as $path) {
            $path = (string) ($path ?? '');
            if ($path !== '' && $disk->exists($path)) {
                $disk->delete($path);
            }
        }

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
