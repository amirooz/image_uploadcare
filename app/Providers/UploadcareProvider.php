<?php

namespace AI\Providers;

use Silex\Application;

use Silex\ServiceProviderInterface;

class UploadcareProvider implements ServiceProviderInterface {
    
    public function register(Application $app)
    {
        $app['uploadcare'] = $app->share(function() use ($app){
            return new \Uploadcare\Api('42c1634429f566ade1ae','75e758f731e7eda1b8c0');
        });
    }
    
    public function boot(Application $app)
    {
        //
    }
}