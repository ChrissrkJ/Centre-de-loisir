<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Teacher;
use Symfony\Component\HttpFoundation\Request;

/**
*@Route("/teacher")
*/
class TeacherController extends Controller
{
    /**
     * @Route("/index", name="teacher_index")
     */
    public function indexAction()
    {
      $teachers = $this->getdoctrine()
        ->getRepository(Teacher::class)
        ->findAll();

        return $this->render('AppBundle:Teacher:index.html.twig',array(
            'teachers' => $teachers
        ));
    }

    /**
     * @Route("/add", name="teacher_add")
     */
    public function addAction(Request $r)
    {
      $teacher = new Teacher();

      $form = $this->createFormBuilder($teacher)
      ->add('Lastname',TextType::class)
      ->add('Firstname',TextType::class)

      ->add('submit',SubmitType::class,array(
        'label' => 'Enregistrer',
        'attr'  => array('class' => 'btn btn-primary')
      ))
      ->getForm();

      $form->handleRequest($r);

      if($form->isSubmitted()){
        $teacher = $form->getData();
        $em = $this->getDoctrine()->getManager();
        $em->persist($teacher);
        $em->flush();

        return $this->redirectToRoute('teacher_index');
      }
        return $this->render('AppBundle:Teacher:add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/update/{id}",name="teacher_update")
     */
    public function updateAction(Request $r, $id)
    {
      //récupérer en bdd les info correspondant à l'id :
      $em = $this->getDoctrine()->getManager();
      $teacher =$em->getRepository(Teacher::class)->find($id);

      // générer un formulaire prérempli avec ces infos:
      $form = $this->createFormBuilder($teacher)
      ->add('lastname',TextType::class)
      ->add('firstname',TextType::class)
      ->add('submit',SubmitType::class,array(
        'label' => 'Mettre à jour',
        'attr'  => array('class' => 'btn btn-primary')
      ))
      ->getForm();
      $form->handleRequest($r);

      //valider et executer la mise à jour:
      if($r->getMethod() == 'POST'){
        $teacher = $form->getData();
        $em = $this->getDoctrine()->getManager();
        $em->persist($teacher);
        $em->flush();

        return $this->redirectToRoute('teacher_index');
      }

        return $this->render('AppBundle:Teacher:update.html.twig', array(
            'form' => $form->createView(),
            'teacher' => $teacher
        ));
    }

}
