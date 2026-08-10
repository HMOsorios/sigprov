<?php

namespace App\Services;

use App\Services\Contracts\PaymentGatewayInterface;
use InvalidArgumentException;

class PaymentGatewayManager
{
    protected array $gateways = [];
    protected string $default;

    public function __construct()
    {
        $this->default = config('gateways.default', 'asaas');
    }

    public function driver(?string $name = null): PaymentGatewayInterface
    {
        $name ??= $this->default;

        if (!isset($this->gateways[$name])) {
            $class = config("gateways.{$name}.class");

            if (!$class || !class_exists($class)) {
                throw new InvalidArgumentException("Gateway [{$name}] not configured or class not found.");
            }

            $this->gateways[$name] = app($class);
        }

        return $this->gateways[$name];
    }

    public function __call(string $method, array $parameters): mixed
    {
        return $this->driver()->$method(...$parameters);
    }
}
