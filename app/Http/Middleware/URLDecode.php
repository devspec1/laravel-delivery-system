<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class URLDecode
{
    /**
     * The following method loops through all request input and decodes and remove url symbols out all tags from the request. This to ensure that users are unable to set ANY HTML within the form submissions, but also cleans up input.
     *
     * @param Request $request
     * @param callable $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
          
        if (!in_array(strtolower($request->method()), ['put', 'post'])) {
            return $next($request);
        }
        
        $input = $request->all();

        array_walk_recursive($input, function(&$input) {
            $input = html_entity_decode(strip_tags($input));
        });

        $request->merge($input);

        return $next($request);
    }
}

?>