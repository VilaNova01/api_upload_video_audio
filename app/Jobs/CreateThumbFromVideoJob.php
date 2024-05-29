<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use FFMpeg;
use FFMpeg\Format\Video\X264;

class CreateThumbFromVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $video;
    /**
     * Create a new job instance.
     */
    public function __construct(Video $video)
    {
        //
        $this->video=$video;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //

        $thumb=$this->video->code.'/FrameAt10sec.png';

        FFMpeg::fromDisk('videos')
        ->open($this->video->video)
        ->getFrameFromSecunds(10)
        ->export()
        ->toDisk('public')
        ->save($thumb);

        $this->video->update(
           [
            'thumb'=>$thumb
           ]
           );

    }
}
