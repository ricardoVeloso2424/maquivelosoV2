<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function index()
    {
        $machines = Machine::query()
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.machines.index', compact('machines'));
    }

    public function create()
    {
        $machine = new Machine();

        return view('admin.machines.create', compact('machine'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'brand'       => ['nullable', 'string', 'max:255'],
            'model'       => ['nullable', 'string', 'max:255'],
            'price'       => ['nullable', 'numeric', 'min:0'],
            'status'      => ['required', 'in:active,inactive'],
            'description' => ['nullable', 'string'],
        ]);

        Machine::create($data);

        return redirect()
            ->route('admin.machines.index')
            ->with('success', 'Máquina criada com sucesso.');
    }

    public function show(Machine $machine)
    {
        return view('admin.machines.show', compact('machine'));
    }

    public function edit(Machine $machine)
    {
        return view('admin.machines.edit', compact('machine'));
    }

    public function update(Request $request, Machine $machine)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'brand'       => ['nullable', 'string', 'max:255'],
            'model'       => ['nullable', 'string', 'max:255'],
            'price'       => ['nullable', 'numeric', 'min:0'],
            'status'      => ['required', 'in:active,inactive'],
            'description' => ['nullable', 'string'],
        ]);

        $machine->update($data);

        return redirect()
            ->route('admin.machines.index')
            ->with('success', 'Máquina atualizada com sucesso.');
    }

    public function destroy(Machine $machine)
    {
        $machine->delete();

        return redirect()
            ->route('admin.machines.index')
            ->with('success', 'Máquina eliminada com sucesso.');
    }
}
