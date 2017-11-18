<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Student;
use Symfony\Component\HttpFoundation\Request;

/**
*@Route("/student")
*/
class StudentController extends Controller
{
    /**
     * @Route("/index", name="student_index")
     */
    public function indexAction()
    {

      $students = $this->getdoctrine()
        ->getRepository(Student::class)
        ->findAll();

        return $this->render('AppBundle:Student:index.html.twig',array(
            'students' => $students
        ));
    }

    /**
     * @Route("/add", name="student_add")
     */
    public function addAction(Request $r)
    {
      $student = new Student();

      $form = $this->createFormBuilder($student)
      ->add('lastname',TextType::class)
      ->add('firstname',TextType::class)
      ->add('submit',SubmitType::class,array(
        'label' => 'Enregistrer',
        'attr'  => array('class' => 'btn btn-primary')
      ))
      ->getForm();

      $form->handleRequest($r);

      if($form->isSubmitted()){
        $student = $form->getData();
        $em = $this->getDoctrine()->getManager();
        $em->persist($student);
        $em->flush();

        return $this->redirectToRoute('student_index');
      }
        return $this->render('AppBundle:Student:add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/update/{id}", name="student_update")
     */
    public function updateAction(Request $r, $id)
    {
      //récupérer en bdd les info correspondant à l'id :
      $em = $this->getDoctrine()->getManager();
      $student =$em->getRepository(Student::class)->find($id);

      // générer un formulaire prérempli avec ces infos:
      $form = $this->createFormBuilder($student)
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
        $student = $form->getData();
        $em = $this->getDoctrine()->getManager();
        $em->persist($student);
        $em->flush();

        return $this->redirectToRoute('student_index');
      }

        return $this->render('AppBundle:Student:update.html.twig', array(
            'form' => $form->createView(),
            'student' => $student
        ));
    }

    /**
    *@Route("/delete/{id}", name="student_delete")
    */

    public function deleteAction($id){
      $student = $this->getDoctrine()->getRepository(Student::class)->find($id);
      //var_dump($student);
      $em = $this->getDoctrine()->getManager();
      $em->remove($student);
      $em->flush();

      return $this->redirectToRoute('student_index');
    }

}
