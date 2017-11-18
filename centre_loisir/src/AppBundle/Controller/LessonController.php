<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Lesson;
use Symfony\Component\HttpFoundation\Request;

/**
*@Route("/lesson")
*/
class LessonController extends Controller
{
    /**
     * @Route("/index", name="lesson_index")
     */
    public function indexAction()
    {
      $lessons = $this->getdoctrine()
        ->getRepository(Lesson::class)
        ->findAll();

        return $this->render('AppBundle:Lesson:index.html.twig',array(
            'lessons' => $lessons
        ));
    }

    /**
     * @Route("/add", name="lesson_add")
     */
    public function addAction(Request $r)
    {
      $lesson = new Lesson();

      $form = $this->createFormBuilder($lesson)
      ->add('name',TextType::class)
      ->add('submit',SubmitType::class,array(
        'label' => 'Enregistrer',
        'attr'  => array('class' => 'btn btn-primary')
      ))
      ->getForm();

      $form->handleRequest($r);

      if($form->isSubmitted()){
        $lesson = $form->getData();
        $em = $this->getDoctrine()->getManager();
        $em->persist($lesson);
        $em->flush();

        return $this->redirectToRoute('lesson_index');
      }
        return $this->render('AppBundle:Lesson:add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/update/{id}", name="lesson_update")
     */
    public function updateAction(REquest $r, $id)
    {
      //récupérer en bdd les infos correspondant à l'id :
      $em = $this->getDoctrine()->getManager();
      $lesson =$em->getRepository(Lesson::class)->find($id);

      // générer un formulaire prérempli avec ces infos:
      $form = $this->createFormBuilder($lesson)
      ->add('name',TextType::class)
      ->add('submit',SubmitType::class,array(
        'label' => 'Mettre à jour',
        'attr'  => array('class' => 'btn btn-primary')
      ))
      ->getForm();
      $form->handleRequest($r);

      //valider et executer la mise à jour:
      if($r->getMethod() == 'POST'){
        $lesson = $form->getData();
        $em = $this->getDoctrine()->getManager();
        $em->persist($lesson);
        $em->flush();

        return $this->redirectToRoute('lesson_index');
      }

        return $this->render('AppBundle:Lesson:update.html.twig', array(
            'form' => $form->createView(),
            'lesson' => $lesson
        ));
    }

    /**
    *@Route("/delete/{id}", name="lesson_delete")
    */

    public function deleteAction($id){
      $lesson = $this->getDoctrine()->getRepository(Lesson::class)->find($id);
      //var_dump($lesson);
      $em = $this->getDoctrine()->getManager();
      $em->remove($lesson);
      $em->flush();

      return $this->redirectToRoute('lesson_index');
    }

}
