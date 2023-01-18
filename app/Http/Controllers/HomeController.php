<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Services\FileUpload\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function index()
    {
        $attachments = Attachment::where('type', 'image')->latest()->get();

        return view('welcome', [
            'files' => $attachments
        ]);
    }

    public function upload(Request $request)
    {

        $response = (new FileUpload)->setConfig(
            $this->_prepareConfigs($request->all())
        )->upload($request->file('files'));

        if (is_string($response) || empty($response)) {
            return redirect('/')->with('error', $response ? $response : 'File upload failed');
        }

        if (is_array($response)) {
            foreach ($response as $data) {
                if (!isset($data['path'])) {
                    continue;
                } else {
                    $attachment = Attachment::create([
                        'file_path' => $data['path'],
                        'file_name' => $data['filename'],
                        'file_mime' => $data['mime'] ?? null,
                        'file_size' => $data['size'],
                    ]);
                }
            }
        } else {
            $attachment = Attachment::create([
                'file_path' => $response['path'],
                'file_name' => $response['filename'],
                'file_mime' => $response['mime'] ?? null,
                'file_size' => $response['size'],
            ]);
        }

        if(empty($attachment) || !empty($attachment->id)) {
            return redirect('/')->with('error', 'File uploaded failed');
        }

        return redirect('/')->with('success', 'File uploaded successfully');
    }

    public function remove(Request $request)
    {
        if ($request->attachment) {
            $attachment = $request->attachment > 0 ? Attachment::find(intval($request->attachment)) : null;

            if ($attachment && !empty($attachment->file_path)) {
                FileUpload::remove($attachment->file_path);

                $attachment->delete();
            }
        } elseif ($request->removeAll) {
            FileUpload::removeDirectory(date('Ymd'));
            Attachment::truncate();

            return redirect('/')->with('success', 'All files are deleted successfully');
        }

        return redirect('/')->with('success', 'File has been deleted successfully');
    }

    private function _prepareConfigs(array $configData)
    {

        $settings = [];

        if ($configData['prefix']) {
            $settings['prefix'] = $configData['prefix'];
        }
        if ($configData['path']) {
            $settings['path'] = $configData['path'];
        }
        if ($configData['maxFileSize']) {
            $settings['validation']['maxSize'] = $configData['maxFileSize'] . "MB";
        }
        if ($configData['allowedExtensions'] != "*") {
            $settings['validation']['extensions'] = [$configData['allowedExtensions']];
        }

        return $settings;
    }

    public function showImage($id)
    {
        $attachment = is_numeric($id) ? Attachment::find(intval($id)) : null;
        if (empty($attachment) || empty($attachment->file_path)) {
            abort(404, 'Image not found');
        }

        return FileUpload::render($attachment->file_path);
    }
}
