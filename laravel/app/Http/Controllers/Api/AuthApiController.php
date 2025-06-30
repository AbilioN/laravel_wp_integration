<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WordPressAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthApiController extends Controller
{
    protected $wordPressAuthService;

    public function __construct(WordPressAuthService $wordPressAuthService)
    {
        $this->wordPressAuthService = $wordPressAuthService;
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
                'remember' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $credentials = $request->only(['email', 'password']);
            $remember = $request->boolean('remember', false);

            // Try Laravel authentication first
            if (Auth::attempt($credentials, $remember)) {
                $user = Auth::user();
                $token = $user->createToken('api-token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'data' => [
                        'user' => $user,
                        'token' => $token,
                        'token_type' => 'Bearer'
                    ],
                    'message' => 'Login realizado com sucesso'
                ]);
            }

            // Try WordPress authentication
            $wordPressUser = $this->wordPressAuthService->authenticate($credentials['email'], $credentials['password']);

            if ($wordPressUser) {
                // Create or update Laravel user
                $user = $this->wordPressAuthService->syncUser($wordPressUser);
                
                // Login the user
                Auth::login($user, $remember);
                $token = $user->createToken('api-token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'data' => [
                        'user' => $user,
                        'token' => $token,
                        'token_type' => 'Bearer'
                    ],
                    'message' => 'Login realizado com sucesso'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Credenciais inválidas'
            ], 401);

        } catch (\Exception $e) {
            Log::error('API Error - Login', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro no login'
            ], 500);
        }
    }

    /**
     * Register user
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create user in both Laravel and WordPress
            $userData = $request->only(['name', 'email', 'password']);
            
            $user = $this->wordPressAuthService->createUser($userData);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar usuário'
                ], 500);
            }

            // Login the user
            Auth::login($user);
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ],
                'message' => 'Usuário criado com sucesso'
            ], 201);

        } catch (\Exception $e) {
            Log::error('API Error - Register', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro no registro'
            ], 500);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user) {
                // Revoke all tokens
                $user->tokens()->delete();
                
                // Logout from WordPress if needed
                $this->wordPressAuthService->logout($user);
            }

            return response()->json([
                'success' => true,
                'message' => 'Logout realizado com sucesso'
            ]);

        } catch (\Exception $e) {
            Log::error('API Error - Logout', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro no logout'
            ], 500);
        }
    }

    /**
     * Get current user
     */
    public function user()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            // Sync with WordPress data
            $user = $this->wordPressAuthService->syncUserData($user);

            return response()->json([
                'success' => true,
                'data' => $user
            ]);

        } catch (\Exception $e) {
            Log::error('API Error - Get User', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar usuário'
            ], 500);
        }
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            // Create new token
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer'
                ],
                'message' => 'Token renovado com sucesso'
            ]);

        } catch (\Exception $e) {
            Log::error('API Error - Refresh Token', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao renovar token'
            ], 500);
        }
    }

    /**
     * Get user profile
     */
    public function profile()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            // Get WordPress profile data
            $profile = $this->wordPressAuthService->getUserProfile($user);

            return response()->json([
                'success' => true,
                'data' => $profile
            ]);

        } catch (\Exception $e) {
            Log::error('API Error - Get Profile', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar perfil'
            ], 500);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string',
                'bio' => 'nullable|string',
                'website' => 'nullable|url'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $profileData = $request->all();
            
            // Update in both Laravel and WordPress
            $updatedUser = $this->wordPressAuthService->updateUserProfile($user, $profileData);

            return response()->json([
                'success' => true,
                'data' => $updatedUser,
                'message' => 'Perfil atualizado com sucesso'
            ]);

        } catch (\Exception $e) {
            Log::error('API Error - Update Profile', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar perfil'
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
                'new_password_confirmation' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Senha atual incorreta'
                ], 422);
            }

            // Update password in both Laravel and WordPress
            $success = $this->wordPressAuthService->changePassword($user, $request->new_password);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao alterar senha'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Senha alterada com sucesso'
            ]);

        } catch (\Exception $e) {
            Log::error('API Error - Change Password', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar senha'
            ], 500);
        }
    }

    /**
     * Forgot password
     */
    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email inválido',
                    'errors' => $validator->errors()
                ], 422);
            }

            $email = $request->email;
            
            // Send reset email through WordPress
            $success = $this->wordPressAuthService->sendPasswordResetEmail($email);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email de redefinição enviado'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Email não encontrado'
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error('API Error - Forgot Password', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar email de redefinição'
            ], 500);
        }
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $resetData = $request->only(['token', 'email', 'password']);
            
            // Reset password through WordPress
            $success = $this->wordPressAuthService->resetPassword($resetData);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Senha redefinida com sucesso'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Token inválido ou expirado'
                ], 422);
            }

        } catch (\Exception $e) {
            Log::error('API Error - Reset Password', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao redefinir senha'
            ], 500);
        }
    }
} 