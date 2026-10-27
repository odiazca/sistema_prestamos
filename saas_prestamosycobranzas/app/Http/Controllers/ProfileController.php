<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\Cliente;
use App\Models\Empeno;
use App\Models\Prestamo;
use App\Models\User;
use App\Support\AvatarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        $actividad = Auditoria::where('user_id', $user->id)
            ->latest('id')
            ->take(10)
            ->get();

        $resumen = null;
        if ($user->esAdmin()) {
            $resumen = [
                'usuarios' => User::count(),
                'usuarios_activos' => User::where('activo', true)->count(),
                'clientes' => Cliente::count(),
                'prestamos_activos' => Prestamo::whereIn('estado', ['activo', 'mora'])->count(),
                'empenos_vigentes' => Empeno::where('estado', 'vigente')->count(),
                'acciones_hoy' => Auditoria::whereDate('created_at', now()->toDateString())->count(),
            ];
        }

        return view('perfil.show', compact('user', 'actividad', 'resumen'));
    }

    public function updatePerfil(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')->ignore($user->id)],
            'telefono' => ['nullable', 'string', 'max:30'],
        ]);

        $user->update($data);

        return redirect()->route('perfil.show')->with('ok', 'Datos de perfil actualizados correctamente.');
    }

    public function updateFoto(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:min_width=150,min_height=150'],
        ], [
            'avatar.required' => 'Selecciona una imagen.',
            'avatar.image' => 'El archivo debe ser una imagen.',
            'avatar.max' => 'La imagen no debe superar los 2 MB.',
            'avatar.dimensions' => 'La imagen debe tener al menos 150x150 píxeles.',
        ]);

        $user = auth()->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = AvatarService::store($request->file('avatar'));
        $user->update(['avatar' => $path]);

        return redirect()->route('perfil.show')->with('ok', 'Foto de perfil actualizada.');
    }

    public function deleteFoto()
    {
        $user = auth()->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return redirect()->route('perfil.show')->with('ok', 'Foto de perfil eliminada.');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.required' => 'Debes ingresar tu contraseña actual.',
            'password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        $user->update(['password' => Hash::make($data['password'])]);

        return redirect()->route('perfil.show')->with('ok', 'Contraseña actualizada correctamente.');
    }
}
