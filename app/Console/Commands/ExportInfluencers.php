<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InfluencersExport;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ExportInfluencersJob;

class ExportInfluencers extends Command
{
    protected $signature = 'export:influencers';
    protected $description = 'Exports influencers data to an Excel file via cron job';


    public function handle()
    {
        $data = json_decode(Storage::get('export_data.json'), true);

        if (!$data) {
            $this->error("No valid data found.");
            return;
        }

        ExportInfluencersJob::dispatch($data);
        $this->info("Export job dispatched.");
    }

}
