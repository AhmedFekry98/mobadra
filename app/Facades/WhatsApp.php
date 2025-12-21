<?php

namespace App\Facades;

use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Facade;

class WhatsApp extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return WhatsAppService::class;
    }
}
