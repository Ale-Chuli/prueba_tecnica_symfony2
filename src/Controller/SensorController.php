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
    // Route definition for creating a sensor
    #[Route('/new', name: 'new_sensor', methods: ['POST'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref:'#/components/schemas/newSensor'))]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Sensor Created')]

    public function newSensor(Request $request, EntityManagerInterface $em,SensorsRepository $sensorsrep): Response
    {
        // Get the request body content
        $body = $request-> getContent();
        // Decode the JSON content into a PHP array
        $data = json_decode($body, true);

        //Extract the sensor name from the request
        $sensorName = $data['name'];

        //Checks if a sensor with the same name exists to avoid duplicated sensors
        if($sensorsrep-> findOneBy(['name'=> $sensorName])){
            return $this->json(
                ["This sensor already exists."],
                Response::HTTP_CONFLICT);
        }
        //Create a new sensor and sets the name
        $sensor = new Sensors();

        $sensor->setName($sensorName);

        //Persist the sensor into the database and save the changes on it
        $em-> persist($sensor);
        $em->flush();
    
        return $this->json("The new sensor has been created correctly", Response::HTTP_CREATED);
        
    }

    // Route definition for returning all sensors ordered by name
    #[Route('/get', name: 'sensors_get',methods: ['GET'])]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'All Sensors Ordered By Name')]

    public function getOrderedByName(SensorsRepository $sensorsrep):Response
    {
        //Receive all sensors from the repository ordered by the name
        $sensors = $sensorsrep->findAllOrderedByName();
        
        //Initialize an array to store the sensors
        $orderedSensors = [];
        //Extracts all sensors information into the initialized array
        foreach ($sensors as $sensor) {
            $orderedSensors[] = [
                'ID' => $sensor->getId(),
                'Name' => $sensor->getName()
        ];
    }
        return $this->json(['All Sensors Ordered by Name'=>$orderedSensors]);
    }
}
