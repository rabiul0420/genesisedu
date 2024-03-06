<?php

namespace App\Traits;

use App\Interfaces\RedisConstants;
use Exception;
use Illuminate\Support\Facades\Redis;

trait RedisHelper {
    protected function radis($name, $callbak, $dynamic = false)
    {
        if(!defined(RedisConstants::class . '::' . $name) && !$dynamic) {
            return $callbak();
        }

        if(!env('REDIS_STATUS', true)) {
            return $callbak();
        }

        try {
            $data = Redis::get($name) ?? null;
        } catch (Exception $exception) {
            return $callbak();
        }

        if(!$data) {
            $data = $callbak();

            Redis::set($name, json_encode($data));
        }

        return json_decode($data);
    }

    protected function radisCollection($name, $callbak, $dynamic = false)
    {
        return collect($this->radis($name, $callbak, $dynamic = false));
    }
}