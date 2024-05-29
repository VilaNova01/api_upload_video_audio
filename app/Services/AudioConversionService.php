<?php
// app/Services/AudioConversionService.php
namespace App\Services;

use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Wav;

class AudioConversionService
{
    protected $ffmpeg;

    public function __construct()
    {
        $this->ffmpeg = FFMpeg::create();
    }

    public function convertMp3ToWav($inputPath, $outputPath)
    {
        $audio = $this->ffmpeg->open($inputPath);
        $audio->save(new Wav(), $outputPath);
    }
}
