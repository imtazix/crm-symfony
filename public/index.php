<?php

use App\Kernel;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\InMemory;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

// 🔧 Initialisation du registre Prometheus (OBLIGATOIRE pour que /metrics fonctionne)
CollectorRegistry::setDefault(new CollectorRegistry(new InMemory()));

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
