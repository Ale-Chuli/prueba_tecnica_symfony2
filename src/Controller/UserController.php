<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Users;
use App\Repository\UsersRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use OpenApi\Attributes as OA;

#[Route('/user', name: 'user')]
#[Nelmio\Areas(['internal'])]
#[OA\Tag('Users')]

class UserController extends AbstractController
{
    // Route definition for user registration
    #[Route('/register', name: 'register', methods: ['POST'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref:'#/components/schemas/userRegister'))]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'User Created')]

    public function userRegister(EntityManagerInterface $em, Request $request,
    UserPasswordHasherInterface $passwordHasher, UsersRepository $userrep): Response
   {
        // Get the request body content
        $body = $request-> getContent();
        // Decode the JSON content into a PHP array
        $data = json_decode($body, true);

        //Checks if the user already exists with the provided email
        if($userrep->findOneBy(["email"=> $data["username"]])){
            return $this->json("This email is already in use, try again", 
            Response::HTTP_CONFLICT);
        }
        //Create a new User and sets the request data
        $user = new Users();
        $user->setEmail($data['username']);
        $user->setName($data['name']);
        $user->setSurname($data['surname']);
        $password = $data['password'];

       //Hash the user password
        $hashedPassword = $passwordHasher->hashPassword(
           $user,
           $password
        );
        $user->setPassword($hashedPassword);
       
        //Persist the user into the database and save the changes on it
        $em-> persist($user);
        $em->flush();

        return $this->json("User has been created", 
        Response::HTTP_CREATED);
   }

   // Route definition for user login
   #[Route("/login", name:"user_login", methods: ['POST'])]
   #[OA\RequestBody(required: true, content: new OA\JsonContent(ref:'#/components/schemas/userLogin'))]
   #[OA\Response(
    response: Response::HTTP_OK,
    description: 'Login Successful')]

   public function userLogin(UsersRepository $userrep, Request $request,
    UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $jwtManager):Response
    {
        // Get the request body content
        $body = $request-> getContent();
        // Decode the JSON content into a PHP array
        $data = json_decode($body, true);

        //Find the user by the provided email
        $user = $userrep->findOneBy(["email"=> $data["username"]]);

        //If user exists, checks if the provided password is valid and it will generate a JWT for the user
        if($user){
            $password = $data['password'];
            if($passwordHasher->isPasswordValid($user,$password)){
                $token = $jwtManager->create($user, $password);
                return $this -> json([
                    'message' => "Login Successful", 
                    'token' => $token
                ], 
                Response::HTTP_OK);
            }else{
                return $this -> json("Invalid Password", 
                Response::HTTP_BAD_REQUEST);
            }
            
        }
        return $this -> json("User doesn't exists", 
        Response::HTTP_BAD_REQUEST);
    }
}
