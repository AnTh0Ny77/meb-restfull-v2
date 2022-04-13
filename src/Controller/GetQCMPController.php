<?php

namespace App\Controller;

use ZipArchive;
use App\Entity\Slide;
use App\Entity\BagTools;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\File\Stream;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GetQCMPController extends AbstractController
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
        $slide =  $request->get('data');

        if (!$slide instanceof Slide) {
            return $this->json_response('400', 'Unknow Slide');
        }
        $type = $slide->getTypeSlide();
        if ($type->getId() != 5) {
            return $this->json_response('400', 'only for slide type : '. $type->getName().'');
        }
       
        $filesystem = new Filesystem();
        $Response = $slide->getResponse();
        $Response =  explode(';', $Response);

        
       
        foreach ($Response as $image) {
            $path = substr($image, 1);
           
            if ($filesystem->exists($path)){
            }
        }
       
        
      
        if ($zip!= null){
        
        }else{
            return $this->json_response('500', 'wrong database configuration: slide type qcmp with empty response');
        }
    }
}
