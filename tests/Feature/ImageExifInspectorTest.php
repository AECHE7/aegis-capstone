<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\ImageExifInspector;

class ImageExifInspectorTest extends TestCase
{
    public function test_inspect_returns_default_structure_for_non_existent_file()
    {
        $res = ImageExifInspector::inspect('/tmp/non_existent_file_xyz.jpg');
        $this->assertEquals(0.0, $res['risk_score']);
        $this->assertEmpty($res['indicators']);
        $this->assertNull($res['software']);
    }

    public function test_inspect_parses_plain_image()
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'test_img_') . '.jpg';
        if (function_exists('imagecreatetruecolor')) {
            $img = \imagecreatetruecolor(100, 100);
            \imagejpeg($img, $tempPath);
            \imagedestroy($img);
        } else {
            file_put_contents($tempPath, 'dummy_image_content');
        }

        $res = ImageExifInspector::inspect($tempPath);
        $this->assertIsArray($res);
        $this->assertArrayHasKey('risk_score', $res);
        $this->assertArrayHasKey('indicators', $res);

        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }
    }
}
