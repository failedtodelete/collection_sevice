<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CustomException extends Exception
{

    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        // ...
    }

    /**
     * @param Request $request
     * @param $e
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function render(Request $request, $e)
    {
        if($request->ajax()) {
            return Response::json(['status' => false, 'message' => $e->getMessage()], $e->code);
        } else {
            return \response()->view('errors.500', ['exception' => $e], 500);
        }
    }

}
