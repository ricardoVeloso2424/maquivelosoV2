<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\MachineImage;
use Illuminate\Support\Facades\Storage;

class MachineImageController extends Controller
{
    public function destroy(Machine $machine, MachineImage $image)
    {
        // segurança: garantir que a imagem pertence a esta máquina
        if ((int) $image->machine_id !== (int) $machine->id) {
            abort(404);
        }

        // apagar ficheiro do storage (se existir)
        if ($image->path) {
            Storage::disk('public')->delete($image->path);
        }

        $image->delete();

        return back()->with('success', 'Imagem removida com sucesso.');
    }
}
