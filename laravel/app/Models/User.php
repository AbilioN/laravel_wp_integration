<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'wordpress_id',
        'wordpress_username',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Buscar usuário do WordPress
     */
    public function getWordPressUser()
    {
        if (!$this->wordpress_id) {
            return null;
        }

        try {
            $wordpressDb = \Illuminate\Support\Facades\DB::connection('wordpress');
            return $wordpressDb->table('users')->where('ID', $this->wordpress_id)->first();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Verificar se o usuário está sincronizado com WordPress
     */
    public function isWordPressUser()
    {
        return !is_null($this->wordpress_id);
    }

    /**
     * Sincronizar dados do WordPress
     */
    public function syncWithWordPress()
    {
        if (!$this->wordpress_id) {
            return false;
        }

        try {
            $wordpressUser = $this->getWordPressUser();
            if (!$wordpressUser) {
                return false;
            }

            $this->update([
                'name' => $wordpressUser->display_name ?: $wordpressUser->user_login,
                'email' => $wordpressUser->user_email,
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
