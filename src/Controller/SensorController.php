<?php

namespace App\Controller;
//Por defecto
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//Añadidos
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sensors;
use App\Repository\SensorsRepository;

#[Route('/sensor', name: 'sensors')]
class SensorController extends AbstractController
{
    #[Route('/new', name: 'make_sensor', methods: ['POST'])]
    public function SensorRegister(Request $request, EntityManagerInterface $em): Response
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        $sensor = new Sensors();

        $sensor->setName($data['name']);
        //COMPROBAR QUE EL SENSOR YA ESTA CREADO------------------------------------------------------------------------------------------------------------------------
        $em-> persist($sensor);
        $em->flush();
    
        return $this->json("The new sensor has been created correctly", Response::HTTP_CREATED);
        
    }

    #[Route('/get', name: 'sensors_get',methods: ['GET'])]
    public function SensorInfo(SensorsRepository $sensorsrep):Response
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
