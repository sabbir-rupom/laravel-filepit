<?php

namespace App\Services\FileUpload;

use App\Services\FileUpload\Abstracts\FileUploadAbstract;
use App\Services\FileUpload\Traits\Singleton;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class FileUpload extends FileUploadAbstract
{
    use Singleton;

    private $file;
    private $multiple = false;

    public function upload($file)
    {
        if ($file instanceof UploadedFile) {

            $this->file = $file;

            if ($this->uploadValidate()) {
                $this->response = $this->store();
            }
        } elseif (is_array($file)) {

            $this->multiple = true;
            $this->uploadArray($file);
        } else {

            $this->uploadError = 'Invalid file provided';
        }

        return $this->result();
    }

    protected function uploadValidate()
    {
        if (!$this->file->isValid()) {

            $this->uploadError = $this->file->getErrorMessage();
        } else {

            $validation = new UploadValidation($this->file);

            foreach ($this->defaults['validation'] as $key => $value) {

                switch ($key) {
                    case 'maxSize':
                        if ($value && intval($value) > 10) {
                            $validation->setRules('max:' . $value);
                            $validation->setMessages('max', "File size cannot exceed {$this->defaults['validation']['maxSize']} kilobytes");
                        }
                        break;
                    case 'minSize':
                        if ($value && intval($value) > 10) {
                            $validation->setRules('min:' . $value);
                            $validation->setMessages('min', "File size too small. Expected {$this->defaults['validation']['minSize']} kilobytes");
                        }
                        break;
                    case 'extensions':
                        $extAllowedStr = is_string($value) ? $value : (is_array($value) ? join(',', $value) : '');

                        if ($extAllowedStr) {
                            $validation->setRules('mimes:' . $extAllowedStr);
                            $validation->setMessages('mimes', "Extension {$this->getExtension()} is not allowed");
                        }
                        break;

                    default:
                        break;
                }
            }

            $this->uploadError = $validation->validate();
        }

        return $this->uploadError ? false : true;
    }

    public function uploadArray(array $files)
    {
        foreach ($files as $file) {
            $this->file = $file;

            if ($this->uploadValidate()) {
                array_push($this->response, $this->store());
            }
        }
    }

    public function store()
    {

        $result = Storage::putFileAs($this->uploadPath(), $this->file, $this->generateName());

        if (!$result) {
            return [
                'error' => "File upload failed [{$this->getOriginalName()}]"
            ];
        } else {
            return [
                'filename' => $this->getOriginalName(),
                'mime' => $this->getMimeType(),
                'extention' => $this->getExtension(),
                'path' => $result,
                'url' => Storage::url($result),
                'size' => $this->getFileSize()
            ];
        }
    }

    public function result()
    {
        return empty($this->response) ? $this->uploadError : $this->response;
    }

    protected function getFileSize(): int
    {
        return $this->file->getSize();
    }

    protected function getExtension(): string
    {
        return strtolower($this->file->getClientOriginalExtension());
    }

    protected function getMimeType(): string
    {
        return $this->file->getMimeType();
    }

    protected function getOriginalName(): string
    {
        return pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
    }

    protected function generateName(): string
    {
        return (!empty($this->defaults['prefix']) && is_string($this->defaults['prefix']) ?  $this->defaults['prefix'] . '_'  : '')
            . Str::random(8)
            . time()
            . '.' . $this->getExtension();
    }

    protected function uploadPath(): string
    {
        return   date('Ymd') . ($this->defaults['path'] ? DIRECTORY_SEPARATOR . $this->defaults['path'] : '');
    }
}
