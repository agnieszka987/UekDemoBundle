<?php

namespace Uek\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Uek\DemoBundle\Entity\Recenzje;
use Uek\DemoBundle\Entity\Koszyk;
use Uek\DemoBundle\Form\RecenzjaType;
use Symfony\Component\HttpFoundation\Request;

class RecenzjaController extends Controller
{
	public function indexAction() 
	{
		if ($this->getUser() == null)
		{
			$uzytkownik = '';
			
		} else
		{
			$uzytkownik = $this->getUser()->getUsername();
		}
		
		$em = $this->getDoctrine()->getManager();
		$query = $em->createQuery(
			'SELECT f.idfilmu, r.idrecenzji, f.tytulfilmu, r.tresc, r.autor FROM UekDemoBundle:Recenzje r 
			JOIN UekDemoBundle:Filmy f WHERE f.idfilmu = r.idfilmu ORDER BY r.idrecenzji'
		);

		$recenzja = $query->getResult();
		
		$queryIlosc = $em->createQuery(
			'SELECT COUNT(k.idfilmu) AS ilosc FROM UekDemoBundle:Koszyk k WHERE k.uzytkownik = :uzytkownik'
		)
		->setParameter('uzytkownik', $uzytkownik);
		
		$ilosc = $queryIlosc->getResult();
	
		return $this->render(
			'UekDemoBundle:Recenzje:index.html.twig',
			array(
				'recenzja' => $recenzja,
				'ilosc' => $ilosc
				
			)
		);
	}
	
	public function createAction(Request $request)
	{	
		if ($this->getUser() == null)
		{
			$uzytkownik = '';
			$em = $this->getDoctrine()->getManager();
			$queryIlosc = $em->createQuery(
				'SELECT COUNT(k.idfilmu) AS ilosc FROM UekDemoBundle:Koszyk k WHERE k.uzytkownik = :uzytkownik'
			)
			->setParameter('uzytkownik', $uzytkownik);
			
			$ilosc = $queryIlosc->getResult();
			
			return $this->redirect($this->generateUrl('fos_user_security_login', array('ilosc' => $ilosc)));
		} else
		{
			$uzytkownik = $this->getUser()->getUsername();

			$em = $this->getDoctrine()->getManager();
			$queryIlosc = $em->createQuery(
				'SELECT COUNT(k.idfilmu) AS ilosc FROM UekDemoBundle:Koszyk k WHERE k.uzytkownik = :uzytkownik'
			)
			->setParameter('uzytkownik', $uzytkownik);
			
			$ilosc = $queryIlosc->getResult();
			
			$recenzje = new Recenzje();
			$recenzje->setAutor($this->getUser()->getUsername());
		
			$form = $this->createForm(
				new RecenzjaType(),
				$recenzje
			);
		
			if ($request->isMethod('POST')
				&& $form->handleRequest($request)
				&& $form->isValid()
				) {
					$em = $this->getDoctrine()->getManager();
					$em->persist($recenzje);
					$em->flush();
					
					return $this->redirect($this->generateUrl('uek_demo_homepage', array()));
			}
		
			return $this->render(
				'UekDemoBundle:Recenzje:create.html.twig',
				array(
					'form' => $form->createView(),
					'ilosc' => $ilosc
				)
			);
		}	
	}
	
	public function addAction(Request $request, $idfilmu)
	{	
		if ($this->getUser() == null)
		{
			$uzytkownik = "";
			$em = $this->getDoctrine()->getManager();
			$queryIlosc = $em->createQuery(
				'SELECT COUNT(k.idfilmu) AS ilosc FROM UekDemoBundle:Koszyk k WHERE k.uzytkownik = :uzytkownik'
			)
			->setParameter('uzytkownik', $uzytkownik);
			
			$ilosc = $queryIlosc->getResult();
			
			return $this->redirect($this->generateUrl('fos_user_security_login', array('ilosc' => $ilosc)));
		}
		else
		{
			$uzytkownik = $this->getUser()->getUsername();
			$dodano = "dodano";
			$recenzje = new Recenzje();
			$recenzje->setAutor($this->getUser()->getUsername());
			
			$em = $this->getDoctrine()->getManager();
			
			$queryIlosc = $em->createQuery(
				'SELECT COUNT(k.idfilmu) AS ilosc FROM UekDemoBundle:Koszyk k WHERE k.uzytkownik = :uzytkownik'
			)
			->setParameter('uzytkownik', $uzytkownik);
			
			$ilosc = $queryIlosc->getResult();
			
			$film = $em->getRepository("UekDemoBundle:Filmy")->findOneByIdfilmu($idfilmu);
			$recenzje->setIdfilmu($film);
			
			$form = $this->createForm(
				new RecenzjaType(),
				$recenzje
			);
		
			if ($request->isMethod('POST')
				&& $form->handleRequest($request)
				&& $form->isValid()
				) {
					$em = $this->getDoctrine()->getManager();
					$em->persist($recenzje);
					$em->flush();
					
					return $this->redirect($this->generateUrl('uek_demo_filmy_seeRec', array('id' => $idfilmu, 'recenzja' => $dodano)));
			}
		
			return $this->render(
				'UekDemoBundle:Recenzje:create.html.twig',
				array(
					'form' => $form->createView(),
					'ilosc' => $ilosc
				)
			);
		}	
	}
	
}