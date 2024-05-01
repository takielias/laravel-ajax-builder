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
        $this->info('https://github.com/takielias/lab');
        $this->newLine(2);

    }

    protected static function updateTablarJs(): void
    {
        // File path to the 'app.js'
        $filePath = base_path('resources/js/app.js');

        // Line to be added
        $lineToAdd = "import '../../vendor/takielias/lab/resources/js/load.js';\n";

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
