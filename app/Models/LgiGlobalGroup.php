<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LgiGlobalGroup extends Model
{
    protected $connection = 'LgiGlobal114';

    protected $table = 'Groups';

    public $timestamps = false;

    public function App()
    {
        return $this->hasOne(LgiGlobalApp::class, 'AppCode', 'AppCode');
    }
}
