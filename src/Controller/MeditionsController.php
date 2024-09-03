<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MeditionsRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\SensorsRepository;
use App\Repository\WinesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Meditions;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use OpenApi\Attributes as OA;



#[Route('/medition', name: 'medition')]
#[Nelmio\Areas(['internal'])]
#[OA\Tag('Meditions')]

class MeditionsController extends AbstractController
{
    // Route definition for getting all wine meditions
    #[Route('/get', name: 'get_wine_medition',methods: ['GET'])]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'All wines and it meditions')]

    public function GetWinesMeditions(MeditionsRepository $meditionsrep): Response
    {
        //Receives all meditions asociated with the wine names
        $meditions = $meditionsrep->findAllWithWineName();

        //Initialize an array to store the meditions and wines
        $winesOrdered = [];
        //Extract all the relevant information into the initialized array
        foreach ($meditions as $meditionData) {
        
            $medition = $meditionData[0];
            $wine = $meditionData['wine_Name'];
        
            $winesOrdered[] = [
                'Wine_Name' => $wine, 
                'Year' => $medition->getYear(), 
                'Color' => $medition->getColor(), 
                'Temperature' => $medition->getTemperature(), 
                'Graduation' => $medition->getGraduation(), 
                'Ph' => $medition->getPh()];

        }

        return $this->json(['wines' => $winesOrdered], Response::HTTP_OK);
    }

    //Route definition for creating a new medition
    #[Route('/new', name: 'new_medition', methods: ['POST'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref:'#/components/schemas/newMedition'))]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Medition Created')]

    public function NewMedition(Request $request, SensorsRepository $sensorsrep,
    WinesRepository $winesrep, EntityManagerInterface $em, MeditionsRepository $meditionsrep): Response
    {
        // Get the request body content
        $body = $request-> getContent();
        // Decode the JSON content into a PHP array
        $data = json_decode($body, true);

        //Checks if theres a sensor/wine with the respect ID
        $sensor = $sensorsrep->findOneBy(["id"=> $data["sensor_id"]]);
        $wine = $winesrep->findOneBy(["id"=> $data["wine_id"]]);
        $existingMedition = $meditionsrep->findOneBy([
            'sensor' => $sensor,
            'wine' => $wine,
            'year' => $data["year"]]);
            
        //If the sensor and wine exists
        if($sensor && $wine){
            //Checks if the medition already exists 
            if($existingMedition){
                return $this->json(
                    ["This medition already exists."],
                    Response::HTTP_CONFLICT);
            }

            //Create a new meditions and set its properties
            $medition = new Meditions();

            $medition->setYear($data["year"]);
            $medition->setSensor($sensor);
            $medition->setWine($wine);
            $medition->setColor($data["color"]);
            $medition->setTemperature($data["temperature"]);
            $medition->setGraduation($data["graduation"]);
            $medition->setPh($data["ph"]);

            // Persist the new medition entity to the database
            $em->persist($medition);
            // Save the changes to the database
            $em->flush();

            return $this->json(["New medition has been created."],
            Response::HTTP_CREATED);
        
        }else if($sensor && !$wine){
            return $this->json(["Can't create the new medition, error on inputed wine_id."],
            Response::HTTP_BAD_REQUEST);

        }else if(!$sensor && $wine){
            return $this->json(["Can't create the new medition, error on inputed sensor_id."],
            Response::HTTP_BAD_REQUEST);

        }else{
            return $this->json(["Can't create the new medition, error on inputed IDs."],
            Response::HTTP_BAD_REQUEST);
        }
    }
}
