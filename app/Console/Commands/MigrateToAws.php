<?php

namespace App\Console\Commands;

use App\Models\MigratedFile;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MigrateToAws extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:to-aws';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle(): void
    {
        DB::table('media')
            ->update([
                'disk' => 's3',
                'conversions_disk' => 's3'
            ]);

        $s3Disk = Storage::disk('s3');
        $localDisk = Storage::disk('public');

        $this->copyFiles(null, $localDisk, $s3Disk);
    }

    private function copyFiles(?string $path, Filesystem $fromDisk, Filesystem $toDisk)
    {
        foreach ($fromDisk->allDirectories($path) as $directory) {
            $this->copyFiles($directory, $fromDisk, $toDisk);
        }

        foreach ($fromDisk->allFiles($path) as $file) {
            if (MigratedFile::query()->where('path', $file)->exists()) continue;

            $toDisk->put($file, $fromDisk->get($file));

            MigratedFile::query()->create([
                'path' => $file
            ]);
        }
    }
}
