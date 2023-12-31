<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'ffmpeg'
    ];
    $ext = (new Extension('ffmpeg'))
        ->withHomePage('https://ffmpeg.org/')
        ->withLicense(
            'https://git.ffmpeg.org/gitweb/ffmpeg.git/blob/refs/heads/master:/LICENSE.md',
            Extension::LICENSE_LGPL
        )->withManual('https://ffmpeg.org/documentation.html');

    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
    $p->withReleaseArchive('ffmpeg', function (Preprocessor $p) {

        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();
        $ffmpeg_prefix = FFMPEG_PREFIX;

        $cmd = <<<EOF
                mkdir -p {$workdir}/bin/ffmpeg/
                cd {$ffmpeg_prefix}/
                pwd
                cp -rf bin {$workdir}/bin/ffmpeg/
                cd {$workdir}/bin/

                {$workdir}/bin/ffmpeg/bin/ffmpeg -h

                cd {$workdir}/bin/

EOF;
        if ($p->getOsType() == 'macos') {
            $cmd .= <<<EOF
                otool -L {$workdir}/bin/ffmpeg/bin/ffmpeg
                tar -cJvf {$workdir}/ffmpeg-vlatest-static-macos-x64.tar.xz ffmpeg
EOF;
        } else {
            $cmd .= <<<EOF
                file {$workdir}/bin/ffmpeg/bin/ffmpeg
                readelf -h {$workdir}/bin/ffmpeg/bin/ffmpeg
                tar -cJvf {$workdir}/ffmpeg-vlatest-static-linux-x64.tar.xz ffmpeg
EOF;
        }
        return $cmd;
    });
};
