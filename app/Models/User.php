<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    use HasFactory, Notifiable, InteractsWithMedia;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function blogPosts()
    {
        return $this->hasMany(BlogPost::class, 'author_id');
    }

    public function blogComments()
    {
        return $this->hasMany(BlogComment::class);
    }

    /**
     * Get the roles associated with this user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * Get all permissions associated with this user through roles.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role')
            ->through('role_user')
            ->withPivot('role_id')
            ->distinct();
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles()->where('name', $role)->exists();
        }
        return $this->roles()->where('role_id', $role->id)->exists();
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole($roles): bool
    {
        if (is_string($roles)) {
            return $this->hasRole($roles);
        }
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all given roles.
     */
    public function hasAllRoles($roles): bool
    {
        if (is_string($roles)) {
            return $this->hasRole($roles);
        }
        foreach ($roles as $role) {
            if (!$this->hasRole($role)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission($permission): bool
    {
        if (is_string($permission)) {
            // Check permissions directly or through roles
            return $this->roles()
                ->whereHas('permissions', function ($query) use ($permission) {
                    $query->where('name', $permission);
                })
                ->exists();
        }
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('permission_id', $permission->id);
            })
            ->exists();
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission($permissions): bool
    {
        if (is_string($permissions)) {
            return $this->hasPermission($permissions);
        }
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all given permissions.
     */
    public function hasAllPermissions($permissions): bool
    {
        if (is_string($permissions)) {
            return $this->hasPermission($permissions);
        }
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Assign a role to this user.
     */
    public function assignRole($role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }
        
        if ($role && !$this->hasRole($role)) {
            $this->roles()->attach($role);
        }
    }

    /**
     * Revoke a role from this user.
     */
    public function revokeRole($role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }
        
        if ($role) {
            $this->roles()->detach($role);
        }
    }

    /**
     * Get the user's primary role (first role assigned).
     */
    public function getRole()
    {
        return $this->roles()->first();
    }

    /**
     * Get the display name of user's primary role.
     */
    public function getRoleDisplayName()
    {
        $role = $this->getRole();
        return $role ? $role->display_name : 'No Role';
    }

    public function getCartOrCreate()
    {
        return $this->cart ?: $this->cart()->create();
    }

    public function getWishlistIdsAttribute()
    {
        return $this->wishlists()->pluck('product_id')->toArray();
    }

    public function isInWishlist($productId)
    {
        return $this->wishlists()->where('product_id', $productId)->exists();
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return $this->avatar;
        }
        return 'https://ui-avatars.com/api/?background=6366f1&color=fff&name=' . urlencode($this->name);
    }

    public function getInitialAttribute()
    {
        return strtoupper(substr($this->name, 0, 1));
    }
}
