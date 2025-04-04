<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InfluencersExport;
use Illuminate\Support\Facades\Storage;

class ExportInfluencersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $fileName;

    public function __construct($data, $fileName)
    {
        $this->data = $data;
        $this->fileName = $fileName;
    }

    public function handle()
    {
        Excel::store(new InfluencersExport($this->data), "exports/{$this->fileName}", 'local');

        Storage::put("export_filename.txt", $this->fileName);
    }
}
