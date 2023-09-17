<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $options = '--enable-gd --with-jpeg --with-freetype --with-webp ';
    $depends = ['libjpeg', 'freetype', 'libwebp', 'libpng', 'libgif'];

    if ($p->getInputOption('with-libavif')) {
        $options .= ' --with-avif ';
        $depends[] = 'libavif';

        $p->withExportVariable('AVIF_CFLAGS', '$(pkg-config  --cflags --static libavif libbrotlicommon libbrotlidec libbrotlienc SvtAv1Enc SvtAv1Dec aom dav1d libgav1)');
        $p->withExportVariable('AVIF_LIBS', '$(pkg-config    --libs   --static libavif libbrotlicommon libbrotlidec libbrotlienc SvtAv1Enc SvtAv1Dec aom dav1d libgav1)');
        $p->withExportVariable('FREETYPE2_CFLAGS', '$(pkg-config  --cflags --static libavif libbrotlicommon libbrotlidec libbrotlienc freetype2 zlib libpng)');
        $p->withExportVariable('FREETYPE2_LIBS', '$(pkg-config    --libs   --static libavif libbrotlicommon libbrotlidec libbrotlienc freetype2 zlib libpng)');

    }

    $ext = (new Extension('gd'))
        ->withHomePage('https://www.php.net/manual/zh/book.image.php')
        ->withOptions($options);
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
};
