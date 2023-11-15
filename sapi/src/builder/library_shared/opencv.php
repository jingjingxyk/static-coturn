<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $opencv_prefix = OPENCV_PREFIX;

    $libiconv_prefix = ICONV_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;

    $openssl_prefix = OPENSSL_PREFIX;
    $ffmpeg_prefix = FFMPEG_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $libxml2_prefix = LIBXML2_PREFIX;
    $libzstd_prefix = LIBZSTD_PREFIX;
    $liblz4_prefix = LIBLZ4_PREFIX;
    $liblzma_prefix = LIBLZMA_PREFIX;
    $jpeg_prefix = JPEG_PREFIX;
    $libtiff_prefix = LIBTIFF_PREFIX;
    $png_prefix = PNG_PREFIX;
    $gmp_prefix = GMP_PREFIX;
    $libwebp_prefix = WEBP_PREFIX;
    $freetype_prefix = FREETYPE_PREFIX;
    $gflags_prefix = GFLAGS_PREFIX;
    $openblas_prefix = OPENBLAS_PREFIX;
    $blas_prefix = BLAS_PREFIX;
    $lapack_prefix = LAPACK_PREFIX;
    $harfbuzz_prefix = HARFBUZZ_PREFIX;
    $glog_prefix = GLOG_PREFIX;
    $imath_prefix = IMATH_PREFIX;
    $libeigen_prefix = LIBEIGEN_PREFIX;
    $vtk_prefix = VTK_PREFIX;
    $fftw3_prefix = FFTW3_PREFIX;
    $libdc1394_prefix = LIBDC1394_PREFIX;
    $glog_prefix = GLOG_PREFIX;
    $libeigen_prefix = LIBEIGEN_PREFIX;
    $suitesparse_prefix = SUITESPARSE_PREFIX;
    $harfbuzz_prefix = HARFBUZZ_PREFIX;
    $libavif_prefix = LIBAVIF_PREFIX;

    $CMAKE_PREFIX_PATH = "{$openssl_prefix};";
    $CMAKE_PREFIX_PATH .= "{$zlib_prefix};";

    $CMAKE_PREFIX_PATH .= "{$libxml2_prefix};";
    $CMAKE_PREFIX_PATH .= "{$jpeg_prefix};";
    $CMAKE_PREFIX_PATH .= "{$png_prefix};";
    $CMAKE_PREFIX_PATH .= "{$libwebp_prefix};";
    $CMAKE_PREFIX_PATH .= "{$freetype_prefix};";
    # $CMAKE_PREFIX_PATH .= "{$libtiff_prefix};";

    $CMAKE_PREFIX_PATH .= "{$gmp_prefix};";

    $CMAKE_PREFIX_PATH .= "{$liblzma_prefix};";
    $CMAKE_PREFIX_PATH .= "{$libzstd_prefix};";
    $CMAKE_PREFIX_PATH .= "{$liblz4_prefix};";

    $CMAKE_PREFIX_PATH .= "{$gflags_prefix};";
    $CMAKE_PREFIX_PATH .= "{$openblas_prefix};";
    $CMAKE_PREFIX_PATH .= "{$blas_prefix};";
    $CMAKE_PREFIX_PATH .= "{$lapack_prefix};";
    # $CMAKE_PREFIX_PATH .= "{$libeigen_prefix};";
    $CMAKE_PREFIX_PATH .= "{$glog_prefix};";
    # $CMAKE_PREFIX_PATH .= "{$vtk_prefix};";
    $CMAKE_PREFIX_PATH .= "{$ffmpeg_prefix};";
    $CMAKE_PREFIX_PATH .= "{$imath_prefix};";
    $CMAKE_PREFIX_PATH .= "{$fftw3_prefix};";
    # $CMAKE_PREFIX_PATH .= "{$libdc1394_prefix};";
    $CMAKE_PREFIX_PATH .= "{$glog_prefix};";
    # $CMAKE_PREFIX_PATH .= "{$libeigen_prefix};";
    # $CMAKE_PREFIX_PATH .= "{$suitesparse_prefix};";
    $CMAKE_PREFIX_PATH .= "{$harfbuzz_prefix};";
    $CMAKE_PREFIX_PATH .= "{$libavif_prefix};";


    $workDir = $p->getWorkDir();
    $buildDir = $p->getBuildDir();
    $lib = new Library('opencv');
    $lib->withHomePage('https://opencv.org/')
        ->withLicense('https://github.com/opencv/opencv/blob/4.x/LICENSE', Library::LICENSE_APACHE2)
        ->withManual('https://github.com/opencv/opencv.git')
        ->withManual('https://docs.opencv.org/5.x/db/d05/tutorial_config_reference.html')
        ->withManual('https://github.com/opencv/opencv_contrib/tree/5.x/modules/README.md')
        ->withManual('https://github.com/opencv/opencv/blob/5.x/doc/tutorials/introduction/config_reference/config_reference.markdown')
        ->withFile('opencv-v5.x.tar.gz')
        ->withDownloadScript(
            'opencv',
            <<<EOF
        git clone -b 5.x --depth 1 --progress  https://github.com/opencv/opencv.git
        cd opencv
        git clone -b 5.x --depth 1 --progress  https://github.com/opencv/opencv_contrib.git
        cd ..
EOF
        )
        ->withPreInstallCommand(
            'debian',
            <<<EOF
            apt install ccache python3-dev
            apt install -y python3-numpy
EOF
        )
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
        apk add ccache python3-dev binaryen doxygen

        pip3 install numpy setuptools utils-misc  gapi  utils lxml beautifulsoup4 graphviz

        # apk add binaryen # WebAssembly 的优化器和编译器/工具链
EOF
        )
        ->withPreInstallCommand(
            'ubuntu',
            <<<EOF
        apt install -y libstdc++-12-dev
        apt install -y libavif-dev
        apt install -y libvtk9-dev
        apt install -y libogre-1.12-dev
        apt install -y doxygen
        apt install -y python3-flake8
        apt install -y apt install -y libgflags-dev
EOF
        )
        ->withPrefix($opencv_prefix)
        ->withBuildLibraryHttpProxy(true)
        ->withBuildScript(
            <<<EOF


        mkdir -p build
        cd  build

        cmake .. \
        -G Ninja \
        -DCMAKE_INSTALL_PREFIX={$opencv_prefix} \
        -DOPENCV_EXTRA_MODULES_PATH="../opencv_contrib/modules" \
        -DCMAKE_CXX_STANDARD=14 \
        -DCMAKE_C_STANDARD=11 \
        -DCMAKE_BUILD_TYPE=Release \
        -DBUILD_STATIC_LIBS=OFF \
        -DBUILD_SHARED_LIBS=ON \
        -DOPENCV_DOWNLOAD_PATH={$buildDir}/opencv-download-cache \
        -DOpenCV_STATIC=OFF \
        -DENABLE_PIC=ON \
        -DWITH_FFMPEG=ON \
        -DOPENCV_GENERATE_PKGCONFIG=ON \
        -DBUILD_TESTS=OFF \
        -DBUILD_PERF_TESTS=OFF \
        -DBUILD_EXAMPLES=OFF \
        -DBUILD_opencv_apps=ON \
        -DBUILD_opencv_js=OFF \
        -DBUILD_JAVA=OFF \
        -DBUILD_CUDA_STUBS=OFF  \
        -DBUILD_FAT_JAVA_LIB=OFF  \
        -DBUILD_ANDROID_SERVICE=OFF \
        -DBUILD_OBJC=OFF \
        -DBUILD_KOTLIN_EXTENSIONS=OFF \
        -DINSTALL_C_EXAMPLES=ON \
        -DINSTALL_PYTHON_EXAMPLES=ON \
        -DBUILD_DOCS=ON \
        -DOPENCV_ENABLE_NONFREE=ON \
        -DWITH_AVIF=ON \
        -DWITH_GTK=ON \
        -DWITH_CUDA=OFF \

        ninja

        ninja install
EOF
        )
        //->withDependentLibraries('opencl', 'ffmpeg')
        ->withPkgName('opencv5')
        ->withBinPath($opencv_prefix . '/bin/')
        ->withLdflags(" -L" . $opencv_prefix . '/lib/opencv5/3rdparty/ ')
    ;

    $p->addLibrary($lib);
};

/*
 * https://github.com/opencv/ade.git
 */

/*
 *  Automatically Tuned Linear Algebra Software (ATLAS)
 *  https://math-atlas.sourceforge.net/
 */

/*
 * libmv  运动轨迹重建
 * https://github.com/opencv/opencv_contrib/tree/master/modules/sfm
 */

/*
 * WebNN
 */