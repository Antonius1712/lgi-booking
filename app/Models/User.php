<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements Authenticatable
{
    use AuthenticatableTrait;

    protected $connection = 'LgiGlobal114';

    protected $table = 'users';

    protected $primaryKey = 'NIK';

    protected $keyType = 'string';

    public $incrementing = false;

    public function initials()
    {
        // Assuming you have a Name or FullName column
        // Adjust based on your actual column names
        $name = $this->Name ?? $this->FullName ?? $this->NIK;

        $words = explode(' ', $name);
        $initials = '';

        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }

        return $initials ?: strtoupper(substr($name, 0, 2));
    }
}
