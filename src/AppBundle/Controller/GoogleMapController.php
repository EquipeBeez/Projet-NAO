<?php
/**
 * Created by PhpStorm.
 * User: Frs
 * Date: 24/05/2017
 * Time: 08:32
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Entity\Taxrefv10;
use AppBundle\Entity\Observation;
use AppBundle\Form\ObservationFrontType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\ContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class GoogleMapController extends Controller
{
    /**
     * @param $listobs
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/googleMapView/{listobs}", name="google_map")
     * @Method({"GET"})
     */
    public function googleMapViewAction($listobs)
    {
        $em = $this->getDoctrine()->getManager();
        if ($listobs instanceof Observation)
        {
            $query = $em->createQuery('SELECT c FROM AppBundle:Observation c WHERE c.id = ?1');
            $query->setParameter(1, $listobs->getId());
        }
        elseif ($listobs instanceof Taxrefv10)
        {
            $query = $em->createQuery('SELECT o, t FROM AppBundle:Observation o JOIN o.espece t WHERE t.id = ?1');
            $query->setParameter(1, $listobs->getId());
        }
        $listObservations = $query->getArrayResult();
        return $this->render('AppBundle:Front:googleMapView.html.twig', array(
            'listObservations' => json_encode($listObservations, JSON_UNESCAPED_UNICODE)
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/googleMapForm", name="google_map_form")
     */
    public function googleMapFormAction()
    {

        return $this->render('AppBundle:Front:googleMapForm.html.twig');
    }

}