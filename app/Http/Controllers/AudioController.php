<?php

// app/Http/Controllers/AudioController.php
namespace App\Http\Controllers;

use App\Services\AudioConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AudioController extends Controller
{
    protected $audioConversionService;

    public function __construct(AudioConversionService $audioConversionService)
    {
        $this->audioConversionService = $audioConversionService;
    }

    public function convertMp3ToWav(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimetypes:audio/mpeg,audio/mp3'
        ]);

        $audio = $request->file('audio');
        $outputPath = 'converted_audio.wav';

        $this->audioConversionService->convertMp3ToWav($audio->getRealPath(), Storage::path($outputPath));

        return response()->download(Storage::path($outputPath));
    }
}
