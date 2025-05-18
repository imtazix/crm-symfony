<?php

namespace App\Controller;

use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MetricsController
{
    #[Route('/metrics', name: 'metrics')]
    public function metrics(): Response
    {
        $registry = CollectorRegistry::getDefault();
        $renderer = new RenderTextFormat();
        return new Response(
            $renderer->render($registry->getMetricFamilySamples()),
            200,
            ['Content-Type' => RenderTextFormat::MIME_TYPE]
        );
    }
}
