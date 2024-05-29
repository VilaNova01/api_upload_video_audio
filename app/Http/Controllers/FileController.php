<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use getID3;

use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Wav;
use FFMpeg\Format\Video\X264;
use FFMpeg\Format\Audio\Mp3;
use FFMpeg\Coordinate\TimeCode;
class FileController extends Controller
{
    public function upload(FileRequest $request){

        $input=$request->validated();

        $file=$input['file'];

        $name=$file->getClientOriginalName();
       
        $path=$file->store('files','public');


        $ffmpeg = FFMpeg::create();

        
      
      //extrair trecho
        $inputFile = $file; // O arquivo enviado pelo usuário
      
        $startTime = 00; // Tempo inicial
        $duration = 30; // Duração em segundos

         // Carregue o arquivo de mídia
         $media = $ffmpeg->open($inputFile->getPathname());
         // Defina os formatos de saída (ajuste conforme necessário)
         $format = new X264('libmp3lame'); // Para vídeo
         $format = new Mp3(); // Para áudio
 
         $nomeFileExtract=uniqid();
         $media->filters()->clip(TimeCode::fromSeconds($startTime),TimeCode::fromSeconds($duration));
         $media->save($format, "storage/files/{$nomeFileExtract}.mp4");

        //converter audio   

        $audio = $ffmpeg->open("storage/".$path);
        $nomeFileConvert=uniqid();
        $audio->save(new Wav(),"storage/files/{$nomeFileConvert}.wav");
 

       File::query()->create([
        'name'=>$name,
        'path'=>$path,
        'daration'=>$duration,
        'fileformat'=>$format
       ]);
        
      
       // dd($ffmpeg);
      //  dd($path);
    }

    public function show_files(){

        return Storage::disk('public')->allFiles("storage/files/");
       
    }
    public function download(File $file){

        return Storage::disk('public')->download($file->path);
       
    }

    public function show_thumbnail(File $file){

        return Storage::disk('public')->get($file->path);
       
    }

    public function show_converted_audio(File $file){

        return Storage::disk('public')->get($file->path);
       
    }


   
    //
}
