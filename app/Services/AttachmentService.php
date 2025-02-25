<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Models\Attachment;

class AttachmentService
{
    /**
     * Store an attachment (doc, pdf, excel).
     */
    public function storeAttachment($file,$attachableId,$attachableType)
    {
        $originalName = $file->getClientOriginalName();

        // Check for path traversal attacks and invalid file names
        if (strpos($originalName, '..') !== false || strpos($originalName, '/') !== false || strpos($originalName, '\\') !== false) {
            throw new Exception(trans('general.pathTraversalDetected'), 403);
        }

        // Validate MIME types to ensure the file is a doc, pdf, or excel file
        $allowedMimeTypes = [
            'application/pdf',  // PDF
            'application/msword', // DOC
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // DOCX
            'application/vnd.ms-excel', // XLS
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' // XLSX
        ];
        $mime_type = $file->getClientMimeType();

        if (!in_array($mime_type, $allowedMimeTypes)) {
            throw new FileException(trans('general.invalidFileType'), 403);
        }

        // Generate a safe, random file name
        $fileName = Str::random(32);
        $extension = $file->getClientOriginalExtension();
        $filePath = "attachments/{$fileName}.{$extension}";

        // Store the file securely in 'public' disk
        $path = $file->storeAs('attachment', $fileName . '.' . $extension, 'public');

        // Get the full URL path of the stored file
        $url = Storage::url($path);

        // Store attachment metadata in the database
        $attachment = Attachment::create([
            'name' => $originalName,
            'type' => $extension,
            'path' => $url,
            'mime_type' => $mime_type,
            'user_id' => auth()->id(),
            'attachable_type' => $attachableId,
            'attachable_id' => $attachableType,
        ]);

        return $attachment;
    }



    public function deleteAttachment($attachmentId)
    { 
        // البحث عن المرفق
        $attachment = Attachment::findOrFail($attachmentId);

        // استخراج المسار الفعلي للملف من التخزين
        $filePath = str_replace('/storage/', '', $attachment->path); // إزالة /storage/ من المسار

        // التحقق من وجود الملف وحذفه
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        // حذف السجل من قاعدة البيانات
        $attachment->forceDelete();

        return ['message' => 'Attachment deleted successfully'];
    }

}