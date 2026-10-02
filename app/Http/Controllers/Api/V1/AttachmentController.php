<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Attachment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentController extends BaseApiController
{
    /**
     * POST /api/v1/attachments
     * Upload an attachment for Need/Offer/Message draft.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file'     => 'required|file|max:10240',
            'purpose'  => 'required|string|in:NEED,OFFER,MESSAGE,PROFILE',
            'need_id'  => 'nullable|uuid',
            'offer_id' => 'nullable|uuid',
        ]);

        $file = $request->file('file');
        $purpose = strtoupper($request->input('purpose'));
        $uuid = (string) Str::uuid();
        $ext = $file->getClientOriginalExtension() ?: 'bin';
        $key = 'attachments/' . $uuid . '.' . $ext;

        Storage::disk('local')->put($key, $file->get());

        $attachment = Attachment::create([
            'uploaded_by'   => $request->user()->id,
            'need_id'       => $request->input('need_id'),
            'offer_id'      => $request->input('offer_id'),
            'purpose'       => $purpose,
            'storage_disk'  => 'local',
            'storage_key'   => $key,
            'original_name' => $file->getClientOriginalName(),
            'detected_mime' => $file->getClientMimeType(),
            'byte_size'     => $file->getSize(),
            'sha256'        => hash_file('sha256', $file->getRealPath()),
            'visibility'    => 'PRIVATE',
            'scan_status'   => 'PENDING',
            'created_at'    => now(),
        ]);

        return $this->success([
            'id'            => $attachment->id,
            'original_name' => $attachment->original_name,
            'byte_size'     => $attachment->byte_size,
            'scan_status'   => $attachment->scan_status,
        ], 'Attachment uploaded.', 201);
    }
}
