<?php

namespace Takielias\Lab\Commands;

use Illuminate\Console\Command;

class InstallLAB extends Command
{
    protected $signature = 'lab:install';

    protected $description = 'Install Laravel Ajax Builder';

    public function handle(): void
    {
        // Update npm packages
        if (!file_exists(base_path('package.json'))) {
            $this->error('package.json not found.');
            return;
        }

        // File path to the 'app.js'
        $filePath = base_path('resources/js/app.js');

        // Check if the file exists
        if (!file_exists($filePath)) {
            $this->error("File does not exist: {$filePath}");
            return;
        }

        self::updateTablarJs();
        self::scaffoldConfig();
        $this->newLine();
        $this->comment('LAB is now installed 🚀');
        $this->newLine();
        $this->comment('Once the installation is done, run "npm run dev"');
        $this->newLine();
        $this->line('Please Show your support ❤️ for LAB by giving us a star on GitHub ⭐️');
        $this->info('https://github.com/takielias/laravel-ajax-builder');
        $this->newLine(2);

    }

    protected static function updatePackageArray(array $packages, array $requiredPackages): array
    {
        foreach ($requiredPackages as $package => $version) {
            if (!array_key_exists($package, $packages)) {
                $packages[$package] = $version;
            }
        }

        return $packages;
    }

    protected static function updatePackages(): void
    {
        $packagesFile = json_decode(file_get_contents(base_path('package.json')), true);

        $requiredPackages = [
            "filepond" => "^4.30.4",
            "filepond-plugin-image-preview" => "^4.6.11",
            "filepond-plugin-file-encode" => "^2.1.14",
            "filepond-plugin-image-edit" => "^1.6.3",
            "filerobot-image-editor" => "^4.6.1",
            "react-filerobot-image-editor" => "^4.6.3",
            "flatpickr" => "^4.6.13",
            "jspdf" => "^2.5.1",
            "jspdf-autotable" => "^3.8.1",
            "tabulator-tables" => "^5.5.2",
            "xlsx" => "^0.18.5",
            "jodit" => "^3.24.5",
            "litepicker" => "^2.0.12",
            "tom-select" => "^2.2.2"
        ];

        // Combine existing dependencies and devDependencies
        $existingPackages = array_merge(
            $packagesFile['dependencies'] ?? [],
            $packagesFile['devDependencies'] ?? []
        );

        // Filter out the required packages that already exist
        $packagesToAdd = array_diff_key($requiredPackages, $existingPackages);

        // Update devDependencies with the filtered packages
        $packagesFile['devDependencies'] = static::updatePackageArray(
            $packagesFile['devDependencies'] ?? [],
            $packagesToAdd
        );

        ksort($packagesFile['devDependencies']);

        file_put_contents(
            base_path('package.json'),
            json_encode($packagesFile, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL
        );
    }

    protected static function updateTablarJs(): void
    {
        // File path to the 'app.js'
        $filePath = base_path('resources/js/app.js');

        // Line to be added
        $lineToAdd = "import '../../vendor/takielias/laravel-ajax-builder/resources/js/load.js';\n";

        // Check if the import has already been added (using a more robust method)
        if (!self::hasImportBeenAdded($filePath, $lineToAdd)) {
            // Append the line if it does not exist
            file_put_contents($filePath, $lineToAdd, FILE_APPEND);
        }
    }

    /**
     * Export the Config file.
     */
    protected static function scaffoldConfig(): void
    {
        copy(__DIR__ . '../../../config/lab.php', base_path('config/lab.php'));
    }

    // Helper function to check for the import more reliably
    protected static function hasImportBeenAdded($filePath, $lineToAdd): bool
    {
        $fileContent = file_get_contents($filePath);
        return str_contains($fileContent, $lineToAdd);
    }

}
