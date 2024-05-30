<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoneEngToEngDigit
{
    public function handle(Request $request, Closure $next): Response
    {

        $request->merge([
            'source_card_number'      => (int)convertNoneEngToEngNumber($request->get('source_card_number')),
            'destination_card_number' => (int)convertNoneEngToEngNumber($request->get('destination_card_number')),
            'amount'                  => (int)convertNoneEngToEngNumber($request->get('amount')),
        ]);


        return $next($request);
    }
}
