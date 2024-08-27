<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sensors;
use App\Repository\SensorsRepository;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use OpenApi\Attributes as OA;

#[Route('/sensor', name: 'sensors')]
#[Nelmio\Areas(['internal'])]
#[OA\Tag('Sensors')]

class SensorController extends AbstractController
{
    #[Route('/new', name: 'new_sensor', methods: ['POST'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref:'#/components/schemas/newSensor'))]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Sensor Created')]

    public function new(Request $request, EntityManagerInterface $em,SensorsRepository $sensorsrep): Response
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        $sensorName = $data['name'];

        if($sensorsrep-> findOneBy(['name'=> $sensorName])){
            return $this->json(
                ["This sensor already exists."],
                Response::HTTP_CONFLICT);
        }

        $sensor = new Sensors();

        $sensor->setName($sensorName);

        $em-> persist($sensor);
        $em->flush();
    
        return $this->json("The new sensor has been created correctly", Response::HTTP_CREATED);
        
    }

    #[Route('/get', name: 'sensors_get',methods: ['GET'])]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'All Sensors Ordered By Name')]

    public function getOrderedByName(SensorsRepository $sensorsrep):Response
    {
        $sensors = $sensorsrep->findAllOrderedByName();
        
        $orderedSensors = [];
        foreach ($sensors as $sensor) {
            $orderedSensors[] = [
                'ID' => $sensor->getId(),
                'Name' => $sensor->getName()
        ];
    }
        return $this->json(['All Sensors Ordered by Name'=>$orderedSensors]);
    }
}
