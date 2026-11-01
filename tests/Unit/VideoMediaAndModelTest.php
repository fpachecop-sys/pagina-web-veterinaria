<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\VideoMediaStorage;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Video;

require_once __DIR__ . '/../../Modelo/Video.php';

final class VideoMediaAndModelTest extends TestCase
{
    public function testIsManagedPathAcceptsValidUploadedMediaPaths(): void
    {
        $this->assertTrue(VideoMediaStorage::isManagedPath('assets/uploads/media/a1b2c3d4e5f60718293a4b5c6d7e8f90.jpg'));
        $this->assertTrue(VideoMediaStorage::isManagedPath('assets/uploads/media/0123456789abcdef0123456789abcdef.mp4'));
        $this->assertTrue(VideoMediaStorage::isManagedPath('assets/uploads/media/ffffffffffffffffffffffffffffffff.webp'));
    }

    public function testIsManagedPathRejectsPathTraversalAndExternalFiles(): void
    {
        $this->assertFalse(VideoMediaStorage::isManagedPath('assets/uploads/media/../../index.php'));
        $this->assertFalse(VideoMediaStorage::isManagedPath('assets/img/RALAH_LOGO.png'));
        $this->assertFalse(VideoMediaStorage::isManagedPath('assets/uploads/media/archivo_corto.mp4'));
        $this->assertFalse(VideoMediaStorage::isManagedPath('assets/uploads/media/a1b2c3d4e5f60718293a4b5c6d7e8f90.php'));
    }

    public function testStoreRejectsInvalidExpectedKind(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El tipo de archivo solicitado no es válido.');
        VideoMediaStorage::store([], 'audio');
    }

    public function testStoreRejectsUploadErrorsWithDescriptiveMessage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El archivo supera el límite permitido por el servidor.');
        VideoMediaStorage::store(['error' => UPLOAD_ERR_INI_SIZE], 'image');
    }

    public function testStoreRejectsDisallowedOrMismatchedExtension(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Sube una imagen JPG, PNG, WEBP o GIF para la promoción.');
        VideoMediaStorage::store([
            'error'    => UPLOAD_ERR_OK,
            'name'     => 'video_en_lugar_de_imagen.mp4',
            'size'     => 1024,
            'tmp_name' => __FILE__,
        ], 'image');
    }

    public function testStoreRejectsOversizedImageAndVideoFiles(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('La imagen debe pesar como máximo 8 MB.');
        VideoMediaStorage::store([
            'error'    => UPLOAD_ERR_OK,
            'name'     => 'promo_pesada.png',
            'size'     => 9_000_000,
            'tmp_name' => __FILE__,
        ], 'image');
    }

    public function testVideoModelDetectsImageAndLocalVideoResources(): void
    {
        $promo = ['url_video' => 'assets/uploads/media/a1b2c3d4e5f60718293a4b5c6d7e8f90.png'];
        $videoLocal = ['url_video' => 'assets/uploads/media/a1b2c3d4e5f60718293a4b5c6d7e8f90.mp4'];
        $youtube = ['url_video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'];

        $this->assertTrue(Video::esImagenMedia($promo));
        $this->assertFalse(Video::esVideoLocal($promo));
        $this->assertTrue(Video::esRecursoLocal($promo));

        $this->assertTrue(Video::esVideoLocal($videoLocal));
        $this->assertTrue(Video::esVideoDirecto($videoLocal));
        $this->assertTrue(Video::esRecursoLocal($videoLocal));

        $this->assertFalse(Video::esImagenMedia($youtube));
        $this->assertFalse(Video::esVideoLocal($youtube));
        $this->assertFalse(Video::esRecursoLocal($youtube));
    }

    public function testVideoUrlParaEmbedTransformsYoutubeWatchAndShortLinks(): void
    {
        $this->assertSame(
            'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            Video::urlParaEmbed('https://www.youtube.com/watch?v=dQw4w9WgXcQ')
        );
        $this->assertSame(
            'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            Video::urlParaEmbed('https://youtu.be/dQw4w9WgXcQ')
        );
        $this->assertSame(
            'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            Video::urlParaEmbed('https://www.youtube.com/shorts/dQw4w9WgXcQ')
        );
    }

    public function testVideoUrlParaEmbedTransformsVimeoLinks(): void
    {
        $this->assertSame(
            'https://player.vimeo.com/video/123456789',
            Video::urlParaEmbed('https://vimeo.com/123456789')
        );
    }

    public function testVideoUrlParaEmbedPreservesDirectVideoOrLocalPaths(): void
    {
        $local = 'assets/uploads/media/a1b2c3d4e5f60718293a4b5c6d7e8f90.mp4';
        $this->assertSame($local, Video::urlParaEmbed($local));
    }
}