<?php

namespace App\Models;

use App\Services\FileUpload\FileUpload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{

    protected $fillable = [
        'added_by',
        'user_id',
        'type',
        'caption',
        'alt_text',
        'file_path',
        'file_name',
        'file_mime',
        'file_size',
        'thumb_image',
        'external_link'
    ];

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = (int)$this->file_size;
        $precision = 2;
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $exp = floor(log($bytes, 1024)) | 0;
        return (int)round($bytes / (pow(1024, $exp)), $precision) . ' ' . $units[$exp];
    }

    public function getUrlAttribute()
    {
        return FileUpload::getUrl($this->file_path);
    }

    public function getExtentionAttribute()
    {
        $file_mime = explode('/', $this->file_mime);
        $extention = $file_mime[1];
        return $extention;
    }

    public function filter(array $filter)
    {
        $attachments = self::query();

        $sort = $filter['sort'];
        if ($sort === 'newest') {
            $attachments = $attachments->latest();
        } elseif ($sort === 'oldest') {
            $attachments = $attachments->oldest();
        } elseif ($sort === 'smallest') {
            $attachments->orderBy('file_size', 'asc');
        } elseif ($sort === 'largest') {
            $attachments->orderBy('file_size', 'desc');
        }

        if (isset($filter['search']) && is_string($filter['search']) && trim($filter['search']) !== '') {
            $search = $filter['search'];
            $attachments->where('caption', 'like', "%{$search}%")
                ->orWhere(function ($query) use ($search) {
                    $query
                        ->where('file_name', 'like', "{$search}%")
                        ->whereNull('caption')
                        ->orWhere('caption', '!=', '');
                });
        }

        if (isset($filter['selected']) && $filter['selected']) {
            $selected = $filter['selected'];
            if (is_string($selected)) {
                $selected = explode(',', trim($selected));
            }
            $attachments->whereIn('id', $selected);
        }

        if (isset($filter['type']) && in_array($filter['type'], [
            'image',
            'document',
            'excel',
            'word',
            'pdf',
            'video'
        ])) {

            if ($filter['type'] === 'image') {
                $attachments->where('type', 'image');
            } else {
                $attachments->where('type', '!=', 'image');
            }
        }

        $attachments = $attachments->paginate(18);

        return $attachments;
    }

    public function getImageAttribute()
    {
        if ($this->type === 'image') {
            return FileUpload::getUrl($this->file_path);
        }

        $fileType = in_array($this->type, ['video', 'excel', 'word', 'pdf']) ? $this->type : 'document';

        return url("assets/images/filemanager/{$fileType}.png");
    }

    public function getNameAttribute()
    {
        return $this->caption ? $this->caption : ($this->alt_text ? $this->alt_text : $this->file_name
        );
    }

    public static function fileType(string $filepath): string
    {
        $mimeDataList = [
            'image' => [
                'image/bmp',
                'image/gif',
                'image/ief',
                'image/jpeg',
                'image/pipeg',
                'image/svg+xml',
                'image/tiff',
                'image/x-icon',
                'image/x-portable-bitmap',
                'image/x-portable-graymap',
                'image/x-portable-pixmap',
                'image/x-rgb',
            ],
            'video' => [
                'audio/basic',
                'audio/aac',
                'audio/mpeg',
                'audio/ogg',
                'audio/opus',
                'audio/wav',
                'audio/x-wav',
                'audio/webm',
                'audio/3gpp',
                'video/x-msvideo',
                'video/mp4',
                'video/mpeg',
                'video/ogg',
                'video/mp2t',
                'video/webm',
                'video/3gpp',
                'video/3gpp',
                'video/quicktime',
            ],
            'excel' => [
                'application/vnd.oasis.opendocument.spreadsheet',
                'application/vnd.ms-excel',
                'application/vnd.ms-excel',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
                'application/vnd.ms-excel.sheet.macroEnabled.12',
                'application/vnd.ms-excel.template.macroEnabled.12',
                'application/vnd.ms-excel.addin.macroEnabled.12',
                'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
            ],
            'pdf' => [
                'application/pdf',
                'application/vnd.sealedmedia.softseal.pdf',
                'application/vnd.sealedmedia.softseal.pdf',
                'application/vnd.sealedmedia.softseal.pdf',
            ],
            'word' => [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
                'application/vnd.ms-word.document.macroEnabled.12',
                'application/vnd.ms-word.template.macroEnabled.12',
                'text/plain'
            ]
        ];

        $mimeType = Storage::mimeType($filepath);

        foreach ($mimeDataList as $type => $data) {
            if (in_array($mimeType, $data)) {
                return $type;
            }
        }

        return 'document';
    }
}
