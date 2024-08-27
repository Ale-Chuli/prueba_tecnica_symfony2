<?php

namespace App\Controller;
//Por defecto
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//Añadidos
use App\Repository\MeditionsRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\SensorsRepository;
use App\Repository\WinesRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Meditions;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use OpenApi\Attributes as OA;



#[Route('/medition', name: 'medition')]
#[Nelmio\Areas(['internal'])]
#[OA\Tag('Meditions')]
class MeditionsController extends AbstractController
{
    #[Route('/get', name: 'get_wine_medition',methods: ['GET'])]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'All wines and it meditions')]
    public function GetWinesMeditions(MeditionsRepository $meditionsrep): Response
    {
        $meditions = $meditionsrep->findAllWithWineName();

        $winesOrdered = [];
        foreach ($meditions as $meditionData) {
        
            $medition = $meditionData[0];
            $wine = $meditionData['wine_Name'];
        
        $winesOrdered[] = [
            'Wine_Name' => $wine, 
            'Year' => $medition->getYear(), 
            'Color' => $medition->getColor(), 
            'Temperature' => $medition->getTemperature(), 
            'Graduation' => $medition->getGraduation(), 
            'Ph' => $medition->getPh() 
        ];
    }
        return $this->json(['wines' => $winesOrdered], Response::HTTP_OK);
    }

    #[Route('/new', name: 'new_medition', methods: ['POST'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref:'#/components/schemas/newMedition'))]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Medition Created')]
    public function NewMedition(Request $request, SensorsRepository $sensorsrep,
    WinesRepository $winesrep, EntityManagerInterface $em): Response
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        if($sensorsrep->findBy(["id"=> $data["sensor_id"]]) && $winesrep->findBy(["id"=> $data["wine_id"]])){
            $medition = new Meditions();

            $sensor = $sensorsrep->findOneBy(["id"=> $data["sensor_id"]]);
            $wine = $winesrep->findOneBy(["id"=> $data["wine_id"]]);

            $medition->setYear($data["year"]);
            $medition->setSensor($sensor);
            $medition->setWine($wine);
            $medition->setColor($data["color"]);
            $medition->setTemperature($data["temperature"]);
            $medition->setGraduation($data["graduation"]);
            $medition->setPh($data["ph"]);

            $em-> persist($medition);
            $em->flush();

            return $this->json(["New medition has been created"], Response::HTTP_CREATED);
        
        }else if($sensorsrep->findBy(["id"=> $data["sensor_id"]]) && !$winesrep->findBy(["id"=> $data["wine_id"]])){
            return $this->json(["Can't create the new medition, error on inputed wine_id"], Response::HTTP_UNAUTHORIZED);
        }else if(!$sensorsrep->findBy(["id"=> $data["sensor_id"]]) && $winesrep->findBy(["id"=> $data["wine_id"]])){
            return $this->json(["Can't create the new medition, error on inputed sensor_id"], Response::HTTP_UNAUTHORIZED);
        }else{
            return $this->json(["Can't create the new medition, error on inputed IDs"],Response::HTTP_UNAUTHORIZED);
        }
    }
}
