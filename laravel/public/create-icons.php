<?php
// Script para criar ícones básicos para PWA

$sizes = [192, 512];
$iconDir = __DIR__ . '/icons/';
if (!is_dir($iconDir)) {
    mkdir($iconDir, 0755, true);
}

foreach ($sizes as $size) {
    $filename = "icon-{$size}x{$size}.png";
    $path = $iconDir . $filename;
    
    // Criar uma imagem simples
    $image = imagecreatetruecolor($size, $size);
    
    // Cores
    $blue = imagecolorallocate($image, 0, 123, 255);
    $white = imagecolorallocate($image, 255, 255, 255);
    
    // Preencher fundo
    imagefill($image, 0, 0, $blue);
    
    // Adicionar texto centralizado
    $text = 'WP';
    $font = 5; // Fonte padrão
    $textWidth = imagefontwidth($font) * strlen($text);
    $textHeight = imagefontheight($font);
    $x = ($size - $textWidth) / 2;
    $y = ($size - $textHeight) / 2;
    imagestring($image, $font, $x, $y, $text, $white);
    
    // Salvar
    imagepng($image, $path);
    imagedestroy($image);
    
    echo "Criado: {$filename}\n";
}

echo "Ícones criados com sucesso!\n";
?> 