<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    use LogsAuditTrail;
    public function generate(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'size' => 'nullable|integer|min:100|max:1000',
            'format' => 'nullable|string|in:png,svg,jpg',
        ]);

        $size = $request->size ?? 300;
        $format = $request->format ?? 'png';
        $url = $request->url;

        // Generate QR code
        $qrCode = QrCode::format($format)
            ->size($size)
            ->margin(2)
            ->generate($url);

        // Save QR code to storage
        $fileName = 'qr_' . time() . '.' . $format;
        $filePath = 'qr-codes/' . $fileName;
        
        \Storage::disk('public')->put($filePath, $qrCode);

        // Log audit trail
        $this->logAuditTrail(
            action: 'create',
            description: 'Generated QR code',
            modelType: null,
            modelId: null,
            newValues: ['url' => $url, 'size' => $size, 'format' => $format, 'file_name' => $fileName]
        );

        return response()->json([
            'success' => true,
            'qr_code_url' => asset('storage/' . $filePath),
            'download_url' => route('admin.qr-codes.download', $fileName),
        ]);
    }

    public function download($fileName)
    {
        $filePath = storage_path('app/public/qr-codes/' . $fileName);
        
        if (!file_exists($filePath)) {
            abort(404);
        }

        return response()->download($filePath);
    }

    public function bulkGenerate(Request $request)
    {
        $request->validate([
            'forms' => 'required|array',
            'forms.*' => 'exists:forms,id',
        ]);

        $generated = [];
        
        foreach ($request->forms as $formId) {
            $form = Form::find($formId);
            $formUrl = route('public.forms.show', $form->slug);
            
            // Generate QR code
            $qrCode = QrCode::format('png')
                ->size(300)
                ->margin(2)
                ->generate($formUrl);

            // Save QR code to storage
            $fileName = 'form_' . $form->id . '_' . time() . '.png';
            $filePath = 'qr-codes/' . $fileName;
            
            \Storage::disk('public')->put($filePath, $qrCode);

            // Update form with QR code info
            $form->update([
                'qr_code' => $fileName,
                'qr_code_url' => $formUrl,
            ]);

            $generated[] = [
                'form_id' => $form->id,
                'form_title' => $form->title,
                'qr_code_url' => asset('storage/' . $filePath),
                'form_url' => $formUrl,
            ];
        }

        // Log audit trail
        $this->logAuditTrail(
            action: 'create',
            description: 'Bulk generated QR codes for ' . count($generated) . ' form(s)',
            modelType: Form::class,
            modelId: null,
            newValues: ['forms_count' => count($generated), 'form_ids' => $request->forms]
        );

        return response()->json([
            'success' => true,
            'generated' => $generated,
            'message' => count($generated) . ' QR codes generated successfully!',
        ]);
    }
}