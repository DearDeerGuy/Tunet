<?php

    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\StreamedResponse;

    class VideoStreamController extends Controller
    {
        public function stream(Request $request, $filename)
        {
            $videoPath = storage_path('app/videos/' . $filename);
            if (!file_exists($videoPath)) {

                abort(404, 'Файл не найден');
            }
            $fileSize = filesize($videoPath);
            $start = 0;
            $end = $fileSize - 1;

            if ($request->headers->has('Range')) {
                preg_match('/bytes=(\d+)-(\d*)/', $request->header('Range'), $matches);
                $start = intval($matches[1]);
                if (!empty($matches[2])) {
                    $end = intval($matches[2]);
                }
            }

            $length = $end - $start + 1;

            $response = new StreamedResponse(function () use ($videoPath, $start, $length) {
                $handle = fopen($videoPath, 'rb');
                fseek($handle, $start);
                $bufferSize = 1024 * 8;

                while (!feof($handle) && $length > 0) {
                    $read = min($bufferSize, $length);
                    echo fread($handle, $read);
                    flush();
                    $length -= $read;
                }

                fclose($handle);
            });

            $response->headers->set('Content-Type', 'video/mp4');
            $response->headers->set('Content-Length', $length);
            $response->headers->set('Accept-Ranges', 'bytes');
            $response->headers->set('Content-Range', "bytes $start-$end/$fileSize");
            $response->setStatusCode($request->headers->has('Range') ? 206 : 200);

            return $response;
        }
    }
