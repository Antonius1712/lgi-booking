<?php

namespace App\Actions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingDriverUpdateAction {
    public function handle(string $id, Request $request): void {
        try {
            DB::transaction(function() use($request) {
                
            });
        } catch (Exception $e) {
            dd(
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            );
        }
    }
}