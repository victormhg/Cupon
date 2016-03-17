<?php

/*
 * (c) Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * Este archivo pertenece a la aplicación de prueba Cupon.
 * El código fuente de la aplicación incluye un archivo llamado LICENSE
 * con toda la información sobre el copyright y la licencia.
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SitioController extends Controller
{
    /**
     * @Route("/contacto", defaults={ "_locale"="es" }, name="contacto")
     * Muestra el formulario de contacto y también procesa el envío de emails
     */
    public function contactoAction(Request $request)
    {
        // Se crea un formulario "in situ", sin clase asociada
        $formulario = $this->createFormBuilder()
            ->add('remitente', 'email')
            ->add('mensaje', 'textarea')
            ->getForm()
        ;

        $formulario->handleRequest($request);

        if ($formulario->isValid()) {
            $datos = $formulario->getData();

            $contenido = sprintf(" Remitente: %s \n\n Mensaje: %s \n\n Navegador: %s \n Dirección IP: %s \n",
                $datos['remitente'],
                htmlspecialchars($datos['mensaje']),
                $request->server->get('HTTP_USER_AGENT'),
                $request->server->get('REMOTE_ADDR')
            );

            $mensaje = \Swift_Message::newInstance()
                ->setSubject('Contacto')
                ->setFrom($datos['remitente'])
                ->setTo('contacto@cupon')
                ->setBody($contenido)
            ;

            $this->container->get('mailer')->send($mensaje);

            $this->get('session')->setFlash('info',
                'Tu mensaje se ha enviado correctamente.'
            );

            return $this->redirect($this->generateUrl('portada'));
        }

        return $this->render('sitio/contacto.html.twig', array(
            'formulario' => $formulario->createView(),
        ));
    }
}
