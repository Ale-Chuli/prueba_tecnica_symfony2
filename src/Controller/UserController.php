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
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use OpenApi\Attributes as OA;

#[Route('/user', name: 'user')]
#[Nelmio\Areas(['internal'])]
#[OA\Tag('Users')]

class UserController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['POST'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref:'#/components/schemas/userRegister'))]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'User Created')]

    public function userRegister(EntityManagerInterface $em, Request $request,
    UserPasswordHasherInterface $passwordHasher): Response
   {
       $body = $request-> getContent();
       $data = json_decode($body, true);

       //FALTA COMPROBAR QUE EL MAIL NO SE USE--------------------------------------------------------------------------------------------------------------------------------------------------

       $user = new Users();
       $user->setEmail($data['email']);
       $user->setName($data['name']);
       $user->setSurname($data['surname']);
       $password = $data['password'];

       //https://symfony.com/doc/current/security/passwords.html
       $hashedPassword = $passwordHasher->hashPassword(
           $user,
           $password
       );
       $user->setPassword($hashedPassword);
       
       $em-> persist($user);
       $em->flush();

       return $this->json("User has been created", 
       Response::HTTP_CREATED);
   }

   #[Route("/login", name:"user_login", methods: ['POST'])]
   #[OA\RequestBody(required: true, content: new OA\JsonContent(ref:'#/components/schemas/userLogin'))]
   #[OA\Response(
    response: Response::HTTP_OK,
    description: 'Login Successful')]

   public function userLogin(UsersRepository $userrep, Request $request,
    UserPasswordHasherInterface $passwordHasher):Response
    {
        $body = $request-> getContent();
        $data = json_decode($body, true);

        $user = $userrep->findOneBy(["email"=> $data["email"]]);

        if($user){
            $password = $data['password'];
            if($passwordHasher->isPasswordValid($user,$password)){
                return $this -> json("Login Successful", 
                Response::HTTP_OK);
            }else{
                return $this -> json("Invalid Password", 
                Response::HTTP_UNAUTHORIZED);
            }
            
        }
        return $this -> json("User doesn't exists", 
        Response::HTTP_UNAUTHORIZED);
    }
}
