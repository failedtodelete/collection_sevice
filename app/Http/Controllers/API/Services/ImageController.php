<?php

namespace App\Http\Controllers\API\Services;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use App\Facades\Main\MainImageServiceFacade as ImageService;

class ImageController extends Controller
{

    /**
     * Компрессия изображения
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function compression(Request $request)
    {

        try {

            $rules = [
                'file'          => 'required|image|mimes:jpeg,png,jpg',
                'quality'       => 'required|numeric|min:1|max:100',
                'width'         => 'nullable|numeric',
                'height'        => 'nullable|numeric',
                'extension'     => 'nullable|required_with:jpg,png'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) throw new ValidationException($validator);

            // Установка лимита ожидания запроса
            set_time_limit(8000000);

            // Преобразование изображения
            $image = ImageService::compression($request->file, $request->width, $request->height, $request->quality, $request->extension);

        } catch (\Exception $e) {
            throw $e;
        }

        // Возврат в виде бинарной строки.
        return Response::make($image, 200, [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'attachment'
        ]);

    }
}
