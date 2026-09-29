<?php

namespace App\Console\Commands;

use App\Models\MachineImage;
use App\Services\ThumbnailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateMachineThumbnails extends Command
{
    protected $signature = 'machines:generate-thumbnails
                            {--force : Regenerate thumbnails even when one already exists}';

    protected $description = 'Generate thumbnails for existing machine images that do not have one yet.';

    public function handle(ThumbnailService $thumbnails): int
    {
        if (! extension_loaded('gd')) {
            $this->error('A extensão PHP GD não está instalada — não é possível gerar miniaturas.');
            $this->line('Instale/ative a extensão GD no servidor e volte a executar o comando.');

            return self::FAILURE;
        }

        $force = (bool) $this->option('force');
        $disk = Storage::disk('public');

        $created = 0;
        $skipped = 0;
        $errors = 0;

        // chunkById keeps memory flat with many images and is safe even though we
        // update the thumb_path column we filter on (it paginates by primary key).
        MachineImage::query()
            ->when(! $force, fn ($query) => $query->whereNull('thumb_path'))
            ->orderBy('id')
            ->chunkById(100, function ($images) use ($thumbnails, $disk, $force, &$created, &$skipped, &$errors): void {
                foreach ($images as $image) {
                    $path = (string) ($image->path ?? '');

                    // Ignore records whose original file no longer exists.
                    if ($path === '' || ! $disk->exists($path)) {
                        $skipped++;
                        continue;
                    }

                    // Do not redo work unless --force was given.
                    if (! $force && (string) ($image->thumb_path ?? '') !== '') {
                        $skipped++;
                        continue;
                    }

                    try {
                        $thumbPath = $thumbnails->generate($path);

                        // null = GD unsupported type or image already small enough;
                        // the original is used as-is, so this is a safe skip.
                        if ($thumbPath === null) {
                            $skipped++;
                            continue;
                        }

                        // Drop a now-stale thumbnail when regenerating.
                        $previous = (string) ($image->thumb_path ?? '');
                        if ($previous !== '' && $previous !== $thumbPath && $disk->exists($previous)) {
                            $disk->delete($previous);
                        }

                        $image->update(['thumb_path' => $thumbPath]);
                        $created++;
                    } catch (\Throwable $e) {
                        report($e);
                        $errors++;
                    }
                }
            });

        $this->info("Miniaturas — criadas: {$created}, ignoradas: {$skipped}, erros: {$errors}.");

        return self::SUCCESS;
    }
}
