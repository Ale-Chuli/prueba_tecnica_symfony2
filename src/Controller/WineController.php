<?php

namespace App\Controller;
//CALLAO
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//Nuevos
use App\Repository\MeditionsRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\SensorsRepository;
use App\Repository\WinesRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Meditions;



#[Route('/medition', name: 'medition')]
class WineController extends AbstractController
{
    #[Route('/get', name: 'get_wine',methods: ['GET'])]
    public function GetWinesInfo(MeditionsRepository $meditionsrep): Response
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
        return $this->json(['wines' => $winesOrdered]);
    }

    #[Route('/new', name: 'new', methods: ['POST'])]
    public function NewMedition(Request $request, SensorsRepository $sensorsrep,
    WinesRepository $winesrep, EntityManagerInterface $em): JsonResponse
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

            return $this->json("New medition has been created");
        
        }else if($sensorsrep->findBy(["id"=> $data["sensor_id"]]) && !$winesrep->findBy(["id"=> $data["wine_id"]])){
            return $this->json("Can't create the new medition, error on inputed ID_wine");
        }else if(!$sensorsrep->findBy(["id"=> $data["sensor_id"]]) && $winesrep->findBy(["id"=> $data["wine_id"]])){
            return $this->json("Can't create the new medition, error on inputed ID_sensor");
        }else{
            return $this->json("Can't create the new medition, error on inputed IDs");
        }
    }
}
