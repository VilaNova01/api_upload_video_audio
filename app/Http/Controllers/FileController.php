<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use Illuminate\Http\Request;
use App\Models\File;
use App\Models\ConvertedAudio;
use App\Models\Thumbnails;
use Illuminate\Support\Facades\Storage;
use getID3;

use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Wav;
use FFMpeg\Format\Video\X264;
use FFMpeg\Format\Audio\Mp3;
use FFMpeg\Coordinate\TimeCode;

class FileController extends Controller
{
    //
    public function upload(FileRequest $request){

        $input=$request->validated();

        $file=$input['file'];

        $name=$file->getClientOriginalName();
        $path=$file->store('files','public');

        //gerar metadata
        $getid3=new getID3();
        $metadata=$getid3->analyze($file->getRealPath());
        // Obter o formato do arquivo
        $fileformat = $metadata['fileformat'] ?? 'unknown';
        
        // Obter a duração do arquivo
        $duration = $metadata['playtime_seconds'] ?? 0;

        // Obter o tamanho do arquivo
        $filesize = $metadata['filesize'] ?? 0;


        $ffmpeg = FFMpeg::create();

        // Gerar Thumbnail
         // Abrir o vídeo
         $video = $ffmpeg->open("storage/".$path);

         // Definir o tempo para capturar a miniatura (exemplo: no segundo 10)
         $frameTime = 10;
 
         // Capturar o quadro e salvar como imagem
         $video->frame(TimeCode::fromSeconds($frameTime))
               ->save("storage/files/" . time() . "_thumb.jpg");



                // Extrair trecho
               $inputFile = $file; // O arquivo enviado pelo usuário
      
               $startTime = 00; // Tempo inicial
               $duration = 2; // Duração em segundos
       
                // Carregue o arquivo de mídia
                $media = $ffmpeg->open($inputFile->getPathname());
                // Defina os formatos de saída (ajuste conforme necessário)
                if($fileformat=="mp4"){
                    $format = new X264('libmp3lame'); // Para vídeo
                }else if($fileformat=="mp3"){
              
                    $format = new Mp3(); // Para áudio
                }
        
                // Extrair
                $nameExtrat=uniqid();
                $media->filters()->clip(TimeCode::fromSeconds($startTime),TimeCode::fromSeconds($duration));

                if($fileformat=="mp4"){

                   $media->save($format, "storage/files/{$nameExtrat}trecho.mp4");

                }else if($fileformat=="mp3"){
                    $media->save($format, "storage/files/{$nameExtrat}trecho.mp3");
                
                }
                /// CONVERTER AUDIO PARA WAV
                if($fileformat=="mp3"){
                    $audio = $ffmpeg->open("storage/".$path);
                    $nomeFileConvert=uniqid();
                    $audio->save(new Wav(),"storage/files/{$nomeFileConvert}AUDIO_CONVERTIDO.wav");
                }
                
                //////////////////
                $file=File::query()->create([
                    'name'=>$name,
                    'path'=>$path,
                    'daration'=>$duration,
                    'fileformat'=>$fileformat,
                    'filesize'=>$filesize
                ]);

                //file::find("id")->

                ConvertedAudio::query()->create([
                    'file_id'=>$file->id
                    
                ]);
                Thumbnails::query()->create([
                    'file_id'=>$file->id
                    
                ]);
         dd($ffmpeg);
      //  dd($path);


    }

    public function show_files(){

        return response()->json(File::all());
    }

    public function download(File $file){

        return Storage::disk('public')->download($file->path);
    }

    public function show_thumbnail(Thumbnails $file){

        return Storage::disk('public')->get($file->path);
       
    }
    public function show_converted_audio(ConvertedAudio $audio){

        return response()->json($audio->all());
       
    }
}
