<?php

namespace App\Controller;
//Por defecto
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//Añadidos
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Users;
use App\Repository\UsersRepository;

#[Route('/user', name: 'user')]
class UserController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['POST'])]
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

       return $this->json("User has been created", Response::HTTP_CREATED);
   }

   #[Route("/login", name:"user_login", methods: ['POST'])]
   public function userLogin(UsersRepository $userrep, Request $request,
    UserPasswordHasherInterface $passwordHasher):Response
    {
        $body = $request-> getContent();
        $data = json_decode($body, true);

        $user = $userrep->findOneBy(["email"=> $data["email"]]);

        if($user){
            $password = $data['password'];
            if($passwordHasher->isPasswordValid($user,$password)){
                return $this -> json("Login In ...", Response::HTTP_CREATED);
            }else{
                return $this -> json("Invalid Password", Response::HTTP_CREATED);
            }
            
        }
        return $this -> json("User doesn't exists", Response::HTTP_CREATED);
    }
}
