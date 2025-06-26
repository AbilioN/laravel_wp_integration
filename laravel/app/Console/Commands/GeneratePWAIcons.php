<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GeneratePWAIcons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pwa:generate-icons {--base-icon=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate PWA icons from a base image';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Generating PWA icons...');

        $sizes = [16, 32, 72, 96, 128, 144, 152, 192, 384, 512];
        $iconsDir = public_path('icons');

        // Create icons directory if it doesn't exist
        if (!File::exists($iconsDir)) {
            File::makeDirectory($iconsDir, 0755, true);
        }

        // Create a simple placeholder icon for now
        foreach ($sizes as $size) {
            $this->createPlaceholderIcon($size, $iconsDir);
        }

        $this->info('PWA icons generated successfully!');
        $this->info('Icons saved in: ' . $iconsDir);

        return 0;
    }

    /**
     * Create a placeholder icon
     */
    private function createPlaceholderIcon($size, $iconsDir)
    {
        $filename = "icon-{$size}x{$size}.png";
        $path = $iconsDir . '/' . $filename;

        // Create a simple colored square as placeholder
        $image = imagecreatetruecolor($size, $size);
        
        // Set colors
        $blue = imagecolorallocate($image, 0, 123, 255);
        $white = imagecolorallocate($image, 255, 255, 255);
        
        // Fill background
        imagefill($image, 0, 0, $blue);
        
        // Add text if size is large enough
        if ($size >= 64) {
            $text = 'WP';
            $fontSize = max(8, $size / 8);
            $fontFile = 5; // Built-in font
            
            // Calculate text position
            $bbox = imagettfbbox($fontSize, 0, $fontFile, $text);
            $textWidth = $bbox[4] - $bbox[0];
            $textHeight = $bbox[1] - $bbox[5];
            
            $x = ($size - $textWidth) / 2;
            $y = ($size + $textHeight) / 2;
            
            imagestring($image, $fontFile, $x, $y, $text, $white);
        }
        
        // Save image
        imagepng($image, $path);
        imagedestroy($image);
        
        $this->line("Created: {$filename}");
    }
} 