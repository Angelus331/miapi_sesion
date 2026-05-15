<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash; // ESTA LÍNEA ES EL VERIFICADOR

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validar que los datos lleguen desde Flutter
        $request->validate([
            'email' => 'required|email|unique:users,email|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        // 2. Buscar al usuario en la base de datos
        $user = User::where('email', $request->email)->first();

        // 3. EL VERIFICADOR CRÍTICO:
        // Hash::check compara la clave que envías con la que está cifrada en la DB
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Login incorrecto'
            ], 401); // Si falla, manda el 401 que ves en Flutter
        }

        // 4. Si pasa el verificador, crear el Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }
}