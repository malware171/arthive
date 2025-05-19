<?php

namespace Config;

class App
{
    public static array $middlewareAliases = [
        'auth' => \App\Middleware\Authenticate::class,
        'artist' => \App\Middleware\ArtistAuthenticate::class,
        'client' => \App\Middleware\ClientAuthenticate::class
    ];
}
