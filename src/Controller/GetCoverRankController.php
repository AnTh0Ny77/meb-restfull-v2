<?php

namespace App\Controller;

use App\Entity\Rank;
use App\Entity\User;
use App\Entity\Games;
use App\Entity\Slide;
use App\Entity\QrCode;
use App\Entity\UnlockGames;
use App\Repository\UserRepository;
use App\Repository\GamesRepository;
use App\Repository\QuestRepository;
use App\Repository\QrCodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UnlockGamesRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Stream;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GetCoverRankController extends AbstractController
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
        $rank =  $request->get('data');

        if (!$rank instanceof Rank) {
            return $this->json_response('400', 'Unknow rank');
        }
        if (empty($rank->getCoverPath())) {
            return $this->json_response('400', 'no cover for rank: ' . $rank->getName() . '');
        }

        $filesystem = new Filesystem();
        $path = substr($rank->getCoverPath(), 1);
        
        if ($filesystem->exists($path)) {
            $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
            $response = new Response();
            $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, basename($path));
            $response->headers->set('Content-Disposition', $disposition);
            $response->headers->set('Content-Type', $mime);
            $response->setContent(file_get_contents($path));
            return $response;
            // $stream  = new Stream($path);
            // $response = new BinaryFileResponse($stream);
            // return $response;
        } else {
            return $this->json_response('400', 'no cover for rank: ' . $rank->getName() . '');
        }
    }
}
