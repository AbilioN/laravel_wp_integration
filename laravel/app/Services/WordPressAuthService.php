<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WordPressAuthService
{
    /**
     * Authenticate user with WordPress credentials
     */
    public function authenticate($email, $password)
    {
        try {
            $db = DB::connection('wordpress');
            
            $user = $db->table('users')
                ->where('user_email', $email)
                ->where('user_status', 0)
                ->first();

            if (!$user) {
                return null;
            }

            // Verify password using WordPress hash
            if (!$this->verifyWordPressPassword($password, $user->user_pass)) {
                return null;
            }

            return $user;
        } catch (\Exception $e) {
            Log::error('WordPressAuthService Error - Authenticate', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Sync WordPress user with Laravel user
     */
    public function syncUser($wordPressUser)
    {
        try {
            $user = User::where('email', $wordPressUser->user_email)->first();

            if (!$user) {
                // Create new Laravel user
                $user = User::create([
                    'name' => $wordPressUser->display_name ?: $wordPressUser->user_login,
                    'email' => $wordPressUser->user_email,
                    'password' => Hash::make(Str::random(16)), // Random password for Laravel
                    'wordpress_id' => $wordPressUser->ID,
                    'wordpress_username' => $wordPressUser->user_login
                ]);
            } else {
                // Update existing user
                $user->update([
                    'name' => $wordPressUser->display_name ?: $wordPressUser->user_login,
                    'wordpress_id' => $wordPressUser->ID,
                    'wordpress_username' => $wordPressUser->user_login
                ]);
            }

            return $user;
        } catch (\Exception $e) {
            Log::error('WordPressAuthService Error - Sync User', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Create user in both WordPress and Laravel
     */
    public function createUser($userData)
    {
        try {
            $db = DB::connection('wordpress');
            
            // Create WordPress user
            $wordPressUserId = $db->table('users')->insertGetId([
                'user_login' => $userData['email'],
                'user_pass' => $this->hashWordPressPassword($userData['password']),
                'user_nicename' => $this->generateNicename($userData['name']),
                'user_email' => $userData['email'],
                'user_url' => '',
                'user_registered' => now(),
                'user_activation_key' => '',
                'user_status' => 0,
                'display_name' => $userData['name']
            ]);

            // Get the created WordPress user
            $wordPressUser = $db->table('users')->where('ID', $wordPressUserId)->first();

            // Create Laravel user
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'wordpress_id' => $wordPressUserId,
                'wordpress_username' => $wordPressUser->user_login
            ]);

            return $user;
        } catch (\Exception $e) {
            Log::error('WordPressAuthService Error - Create User', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Logout user from WordPress
     */
    public function logout($user)
    {
        try {
            // Clear WordPress session if needed
            $db = DB::connection('wordpress');
            
            // Delete user sessions from WordPress
            $db->table('usermeta')
                ->where('user_id', $user->wordpress_id)
                ->where('meta_key', 'session_tokens')
                ->delete();

            return true;
        } catch (\Exception $e) {
            Log::error('WordPressAuthService Error - Logout', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Sync user data with WordPress
     */
    public function syncUserData($user)
    {
        try {
            if (!$user->wordpress_id) {
                return $user;
            }

            $db = DB::connection('wordpress');
            
            $wordPressUser = $db->table('users')
                ->where('ID', $user->wordpress_id)
                ->first();

            if ($wordPressUser) {
                $user->update([
                    'name' => $wordPressUser->display_name ?: $wordPressUser->user_login
                ]);
            }

            return $user;
        } catch (\Exception $e) {
            Log::error('WordPressAuthService Error - Sync User Data', ['error' => $e->getMessage()]);
            return $user;
        }
    }

    /**
     * Get user profile from WordPress
     */
    public function getUserProfile($user)
    {
        try {
            if (!$user->wordpress_id) {
                return $user;
            }

            $db = DB::connection('wordpress');
            
            $wordPressUser = $db->table('users')
                ->where('ID', $user->wordpress_id)
                ->first();

            if (!$wordPressUser) {
                return $user;
            }

            // Get user meta data
            $userMeta = $db->table('usermeta')
                ->where('user_id', $user->wordpress_id)
                ->pluck('meta_value', 'meta_key')
                ->toArray();

            return [
                'id' => $user->id,
                'name' => $wordPressUser->display_name ?: $wordPressUser->user_login,
                'email' => $wordPressUser->user_email,
                'username' => $wordPressUser->user_login,
                'url' => $wordPressUser->user_url,
                'registered' => $wordPressUser->user_registered,
                'bio' => $userMeta['description'] ?? '',
                'first_name' => $userMeta['first_name'] ?? '',
                'last_name' => $userMeta['last_name'] ?? '',
                'nickname' => $userMeta['nickname'] ?? '',
                'website' => $wordPressUser->user_url,
                'wordpress_id' => $user->wordpress_id
            ];
        } catch (\Exception $e) {
            Log::error('WordPressAuthService Error - Get User Profile', ['error' => $e->getMessage()]);
            return $user;
        }
    }

    /**
     * Update user profile in both WordPress and Laravel
     */
    public function updateUserProfile($user, $profileData)
    {
        try {
            $db = DB::connection('wordpress');
            
            // Update Laravel user
            $laravelUpdates = [];
            if (isset($profileData['name'])) {
                $laravelUpdates['name'] = $profileData['name'];
            }
            if (isset($profileData['email'])) {
                $laravelUpdates['email'] = $profileData['email'];
            }
            
            if (!empty($laravelUpdates)) {
                $user->update($laravelUpdates);
            }

            // Update WordPress user
            if ($user->wordpress_id) {
                $wordPressUpdates = [];
                
                if (isset($profileData['name'])) {
                    $wordPressUpdates['display_name'] = $profileData['name'];
                }
                if (isset($profileData['email'])) {
                    $wordPressUpdates['user_email'] = $profileData['email'];
                }
                if (isset($profileData['website'])) {
                    $wordPressUpdates['user_url'] = $profileData['website'];
                }

                if (!empty($wordPressUpdates)) {
                    $db->table('users')
                        ->where('ID', $user->wordpress_id)
                        ->update($wordPressUpdates);
                }

                // Update user meta
                $metaUpdates = [
                    'first_name' => $profileData['first_name'] ?? '',
                    'last_name' => $profileData['last_name'] ?? '',
                    'nickname' => $profileData['nickname'] ?? '',
                    'description' => $profileData['bio'] ?? ''
                ];

                foreach ($metaUpdates as $key => $value) {
                    if (isset($profileData[$key])) {
                        $db->table('usermeta')->updateOrInsert(
                            ['user_id' => $user->wordpress_id, 'meta_key' => $key],
                            ['meta_value' => $value]
                        );
                    }
                }
            }

            return $this->getUserProfile($user);
        } catch (\Exception $e) {
            Log::error('WordPressAuthService Error - Update User Profile', ['error' => $e->getMessage()]);
            return $user;
        }
    }

    /**
     * Change password in both WordPress and Laravel
     */
    public function changePassword($user, $newPassword)
    {
        try {
            // Update Laravel password
            $user->update(['password' => Hash::make($newPassword)]);

            // Update WordPress password
            if ($user->wordpress_id) {
                $db = DB::connection('wordpress');
                
                $db->table('users')
                    ->where('ID', $user->wordpress_id)
                    ->update(['user_pass' => $this->hashWordPressPassword($newPassword)]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('WordPressAuthService Error - Change Password', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Send password reset email through WordPress
     */
    public function sendPasswordResetEmail($email)
    {
        try {
            $db = DB::connection('wordpress');
            
            $user = $db->table('users')
                ->where('user_email', $email)
                ->where('user_status', 0)
                ->first();

            if (!$user) {
                return false;
            }

            // Generate reset key
            $resetKey = $this->wp_generate_password(20, false);
            
            // Update user with reset key
            $db->table('users')
                ->where('ID', $user->ID)
                ->update(['user_activation_key' => $resetKey]);

            // Here you would send the email with reset link
            // For now, we'll just log it
            Log::info('Password reset email would be sent', [
                'email' => $email,
                'reset_key' => $resetKey
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('WordPressAuthService Error - Send Password Reset Email', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Reset password using WordPress reset key
     */
    public function resetPassword($resetData)
    {
        try {
            $db = DB::connection('wordpress');
            
            $user = $db->table('users')
                ->where('user_email', $resetData['email'])
                ->where('user_activation_key', $resetData['token'])
                ->where('user_status', 0)
                ->first();

            if (!$user) {
                return false;
            }

            // Update password
            $db->table('users')
                ->where('ID', $user->ID)
                ->update([
                    'user_pass' => $this->hashWordPressPassword($resetData['password']),
                    'user_activation_key' => ''
                ]);

            // Update Laravel user if exists
            $laravelUser = User::where('email', $resetData['email'])->first();
            if ($laravelUser) {
                $laravelUser->update(['password' => Hash::make($resetData['password'])]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('WordPressAuthService Error - Reset Password', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Verify WordPress password hash
     */
    private function verifyWordPressPassword($password, $hash)
    {
        // WordPress uses different hashing methods
        if (strpos($hash, '$P$') === 0 || strpos($hash, '$2y$') === 0) {
            // Modern WordPress uses PHPass or bcrypt
            return password_verify($password, $hash);
        } else {
            // Old WordPress uses MD5
            return md5($password) === $hash;
        }
    }

    /**
     * Hash password for WordPress
     */
    private function hashWordPressPassword($password)
    {
        // Use WordPress password hashing function
        return $this->wp_hash_password($password);
    }

    /**
     * Generate WordPress nicename
     */
    private function generateNicename($name)
    {
        $nicename = strtolower($name);
        $nicename = preg_replace('/[^a-z0-9\s-]/', '', $nicename);
        $nicename = preg_replace('/[\s-]+/', '-', $nicename);
        $nicename = trim($nicename, '-');
        
        return $nicename;
    }

    /**
     * WordPress password hashing function (simplified)
     */
    private function wp_hash_password($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * WordPress password generation function (simplified)
     */
    private function wp_generate_password($length = 12, $special_chars = true)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        if ($special_chars) {
            $chars .= '!@#$%^&*()_+-=[]{}|;:,.<>?';
        }
        
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return $password;
    }
} 