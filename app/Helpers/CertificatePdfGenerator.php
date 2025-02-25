<?php

namespace App\Helpers;

use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

final class CertificatePdfGenerator
{
    private string $templatePath;
    private string $amountFontPath;
    private string $codeFontPath;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->templatePath   = storage_path('app/gift_card_template.jpg');
        $this->amountFontPath = storage_path('app/Montserrat-Bold.ttf');
        $this->codeFontPath   = storage_path('app/Montserrat-Regular.ttf');

        if ( ! file_exists($this->templatePath)) {
            throw new Exception('Template not exists');
        }
        if ( ! file_exists($this->amountFontPath)) {
            throw new Exception('Amount Font not exists');
        }
        if ( ! file_exists($this->codeFontPath)) {
            throw new Exception('Code Font not exists');
        }
    }

    /**
     * @throws Exception
     */
    public function generate(Certificate $certificate): string
    {
        if ( ! $certificate->relationLoaded('currency')) {
            $certificate->load('currency');
        }

        if (empty($certificate->amount)) {
            throw new Exception('Empty certificate amount');
        }

        if (empty($certificate->currency->code)) {
            throw new Exception('Empty currency code');
        }

        $amount = number_format(num: $certificate->amount, thousands_separator: ' ') . ' ' . Str::upper($certificate->currency->code);
        $code = $certificate->code;

        $img = Image::make($this->templatePath);

        $img->text($amount, 170, 580, function($font) {
            $font->file($this->amountFontPath);
            $font->size(55);
            $font->color('#ffffff');
        });

        $img->text($code, 275, 677, function($font) {
            $font->file($this->codeFontPath);
            $font->size(45);
            $font->color('#000000');
        });

        $imagePath = storage_path("app/gift_card_{$certificate->id}.jpg");

        $img->save($imagePath);

        $data = [
            'title' => 'Ventourio Travel Gift Card',
            'image' => [
                'src' => $imagePath,
                'alt' => 'Ventourio Travel Gift Card'
            ]
        ];

        # 12cm x 8cm
        $customPaper = array(0,0,226.80,340.20);

        $pdf = Pdf::loadView('certificate.pdf_template', $data)
            ->setPaper($customPaper, 'landscape');

        $pdfPath = storage_path("app/certificate_{$certificate->id}.pdf");

        $pdf->save($pdfPath);

        unlink($imagePath);

        return $pdfPath;
    }

    /**
     * @throws Exception
     */
    public static function make(Certificate $certificate): string
    {
        return (new self())->generate($certificate);
    }
}
