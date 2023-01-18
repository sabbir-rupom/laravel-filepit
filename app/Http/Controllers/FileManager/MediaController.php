<?php

namespace App\Http\Controllers\FileManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attachment;
use App\Services\FileUpload\FileUpload;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        # code...
    }

    public function upload(Request $request)
    {
        $fileUpload = FileUpload::getInstance();

        $response = $fileUpload->upload($request->file('file'));

        if (is_string($response) || empty($response)) {
            return response()->json([
                'success' => false,
                'message' => $response ? $response : 'Upload failed',
                'data' => []
            ]);
        }

        $uploadCount = 0;

        if (isset($response[0]) && !empty($response[0])) {
            foreach ($response as $data) {
                if (!isset($data['path'])) {
                    continue;
                } else {
                    Attachment::create([
                        'file_path' => $data['path'],
                        'file_name' => $data['filename'],
                        'file_mime' => $data['mime'] ?? null,
                        'file_size' => $data['size'],
                        'type' => Attachment::fileType($data['path'])
                    ]);

                    $uploadCount++;
                }
            }
        } else {
            Attachment::create([
                'file_path' => $response['path'],
                'file_name' => $response['filename'],
                'file_mime' => $response['mime'] ?? null,
                'file_size' => $response['size'],
                'type' => Attachment::fileType($response['path'])
            ]);
            $uploadCount++;
        }

        if ($uploadCount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Media attachment failed',
                'data' => []
            ]);
        }

        return $this->filter($request, 'Media upload successful');
    }

    public function filter(Request $request, string $message = '')
    {
        $attachment = new Attachment();

        $attachments = $attachment->filter($request->all());

        $html = Blade::render('<x-filemanager.image-list :attachments="$attachments" />', ['attachments' => $attachments]);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $html,
            'links' => $attachments,
        ]);
    }

    public function showImage($id, $any = null)
    {
        $attachment = is_numeric($id) ? Attachment::find(intval($id)) : null;
        $fileType = 'document';

        if ($attachment && !empty($attachment->file_path) && FileUpload::exists($attachment->file_path)) {
            if ($attachment->type === 'image') {
                return FileUpload::render($attachment->file_path);
            }

            $fileType = in_array($attachment->type, ['video', 'excel', 'word', 'pdf']) ?
                $attachment->type : 'document';
        } else {
            $fileType = in_array($id, ['video', 'excel', 'word', 'pdf', 'document']) ? $id : 'no-image-found';
        }


        $rFile = public_path("assets/images/filemanager/{$fileType}.png");

        if (!file_exists($rFile)) {
            abort(404, 'Image not found');
        }

        return $this->renderFile($rFile);
    }

    private function renderFile(string $filepath)
    {
        $file = File::get($filepath);
        $type = File::mimeType($filepath);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);
        return $response;
    }
}
