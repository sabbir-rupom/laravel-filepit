<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function index()
    {
        return view('file-upload');
    }

    public function upload(Request $request)
    {
        $multiple = explode(',', $request->inputMultiple);

        return view('file-upload', [
            'formData' => [
                'multiple' => $multiple,
                'single' => $request->inputSingle,
                'document' => $request->inputDoc
            ]
        ]);
    }

}
