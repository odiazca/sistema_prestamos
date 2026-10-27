<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->query('q');

        $clientes = Cliente::query()
            ->when($buscar, function ($query) use ($buscar) {
                $query->where('nombres', 'like', "%{$buscar}%")
                    ->orWhere('apellidos', 'like', "%{$buscar}%")
                    ->orWhere('documento', 'like', "%{$buscar}%")
                    ->orWhere('codigo', 'like', "%{$buscar}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('clientes.index', compact('clientes', 'buscar'));
    }

    public function create()
    {
        return view('clientes.form', ['cliente' => new Cliente()]);
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        $data['codigo'] = $this->generarCodigo();
        Cliente::create($data);

        return redirect()->route('clientes.index')
            ->with('ok', 'Cliente registrado correctamente.');
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.form', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $cliente->update($this->validar($request, $cliente->id));

        return redirect()->route('clientes.index')
            ->with('ok', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('ok', 'Cliente eliminado.');
    }

    private function validar(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'nombres' => ['required', 'string', 'max:120'],
            'apellidos' => ['required', 'string', 'max:120'],
            'tipo_documento' => ['required', 'string', 'max:20'],
            'documento' => ['nullable', 'string', 'max:30'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:120'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'ocupacion' => ['nullable', 'string', 'max:120'],
            'ingreso_mensual' => ['nullable', 'numeric', 'min:0'],
            'estado' => ['required', 'in:activo,inactivo,moroso'],
            'observaciones' => ['nullable', 'string'],
        ]);
    }

    private function generarCodigo(): string
    {
        $ultimo = Cliente::max('id') + 1;

        return 'CLI-'.str_pad($ultimo, 4, '0', STR_PAD_LEFT);
    }
}
