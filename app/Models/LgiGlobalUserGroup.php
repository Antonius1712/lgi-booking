<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LgiGlobalUserGroup extends Model
{
    protected $connection = 'LgiGlobal114';

    protected $table = 'UserGroup';

    protected $fillable = [
        'UserId',
        'GroupCode',
        'Status',
        'UserCreated',
        'DateCreated',
        'UserModified',
        'DateModified',
    ];

    public $timestamps = false;

    public function Group()
    {
        return $this->hasOne(LgiGlobalGroup::class, 'GroupCode', 'GroupCode');
    }
}
