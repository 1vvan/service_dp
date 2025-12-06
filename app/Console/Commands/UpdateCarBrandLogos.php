<?php

namespace App\Console\Commands;

use App\Models\CarBrand;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class UpdateCarBrandLogos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'car-brands:update-logos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновить пути к логотипам марок автомобилей на основе файлов в public/images/car-brands';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logosPath = public_path('images/car-brands');
        
        if (!File::exists($logosPath)) {
            $this->error("Папка {$logosPath} не существует!");
            return 1;
        }

        $files = File::files($logosPath);
        
        if (empty($files)) {
            $this->warn('В папке car-brands нет файлов логотипов.');
            $this->info('Поместите файлы логотипов в: ' . $logosPath);
            return 0;
        }

        $this->info('Найдено файлов: ' . count($files));
        $this->newLine();

        $updated = 0;
        $notFound = [];

        foreach ($files as $file) {
            $filename = $file->getFilenameWithoutExtension();
            $extension = $file->getExtension();
            $relativePath = 'images/car-brands/' . $file->getFilename();

            $brand = CarBrand::where('name', $filename)->first();

            if (!$brand) {
                $brand = CarBrand::whereRaw('LOWER(name) = ?', [Str::lower($filename)])->first();
            }

            if (!$brand) {
                $nameParts = explode('-', $filename);
                $nameParts = array_map('ucfirst', $nameParts);
                $possibleName = implode('-', $nameParts);
                
                $brand = CarBrand::where('name', $possibleName)->first();
                
                if (!$brand) {
                    $possibleName = implode(' ', $nameParts);
                    $brand = CarBrand::where('name', $possibleName)->first();
                }
            }

            if ($brand) {
                $brand->logo = $relativePath;
                $brand->save();
                $this->info("✓ Обновлен логотип для: {$brand->name}");
                $updated++;
            } else {
                $notFound[] = $filename;
                $this->warn("✗ Не найден бренд для файла: {$filename}");
            }
        }

        $this->newLine();
        $this->info("Обновлено брендов: {$updated}");

        if (!empty($notFound)) {
            $this->newLine();
            $this->warn('Файлы, для которых не найдены бренды:');
            foreach ($notFound as $file) {
                $this->line("  - {$file}");
            }
            $this->newLine();
            $this->info('Подсказка: переименуйте файлы так, чтобы они точно совпадали с названиями брендов в БД.');
        }

        return 0;
    }
}
