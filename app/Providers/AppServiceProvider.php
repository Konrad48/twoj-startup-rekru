<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                // Add security scheme
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
                
                // Add API information
                $openApi->info->title = 'Chat API Documentation';
                $openApi->info->version = '1.0.0';
                $openApi->info->description = 'API documentation for the Chat application';
            });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
