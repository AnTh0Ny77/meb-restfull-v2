<?php

namespace App\Controller;

use App\Entity\BagTools;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\File\Stream;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GetToolsController extends AbstractController
{

    public function __construct(private Security $security)
    {
    }
    public function json_response(string $code, string $message)
    {
        $response = [
            "error" => $message,
        ];
        $data = new JsonResponse($response, $code);
        return $data;
    }

    public function __invoke(Request $request)
    {
        $tool =  $request->get('data');

        if (!$tool instanceof BagTools) {
            return $this->json_response('400', 'Unknow BagTools');
        }
        if (empty($tool->getCoverPath())) {
            return $this->json_response('400', 'no cover for : ' . $tool->getName() . '');
        }

        $filesystem = new Filesystem();
        $path = substr($tool->getCoverPath(), 1);
        if ($filesystem->exists($path)) {
            $stream  = new Stream($path);
            $response = new BinaryFileResponse($stream);
            return $response;
        } else {
            return $this->json_response('400', 'no cover for : ' . $tool->getName() . '');
        }
    }
}
