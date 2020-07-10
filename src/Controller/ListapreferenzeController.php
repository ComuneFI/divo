<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Cdf\BiCoreBundle\Controller\FiController;
use Cdf\BiCoreBundle\Utils\Tabella\ParametriTabella;
use App\Entity\Listapreferenze;
use App\Form\ListapreferenzeType;
use Doctrine\Common\Collections\Expr\Comparison;

use App\Service\RTDivoDataMiner;
                
/**
* Listapreferenze controller.
*
*/

class ListapreferenzeController extends FiController {


    private function retrieveIdListe() {
        $divoMiner = new RTDivoDataMiner( $this->getDoctrine()->getManager() );
        $liste_arr = $divoMiner->readCandidatiListe();

        $results = [];
        foreach($liste_arr as $item ) {
            $results = array_merge( $results, $item['driver_lists'] );
        }
        return $results;
    }

    /**
     * Lists all tables entities.
     */
    public function index(Request $request, \Symfony\Component\Asset\Packages $assetsmanager) {

        $arr_listid = $this->retrieveIdListe();

        $bundle = $this->getBundle();
        $controller = $this->getController();
        $idpassato = $request->get('id');
        if (!$this->getPermessi()->canRead($controller)) {
            throw new AccessDeniedException('Non si hanno i permessi per visualizzare questo contenuto');
        }
        $template = $bundle . ':' . $controller . ':' . $this->getThisFunctionName() . '.html.twig';
        if (!$this->get('twig')->getLoader()->exists($template)) {
            $template = $controller . '/Crud/' . $this->getThisFunctionName() . '.html.twig';
        }

        $entityclassnotation = $this->getEntityClassNotation();
        $entityclass = $this->getEntityClassName();
        $formclass = str_replace('Entity', 'Form', $entityclass);

        $modellocolonne = array(
            array('nometabella' => $controller, 'nomecampo' => "$controller.lista_desc", 'etichetta' => 'Lista', 'ordine' => 10, 'larghezza' => 50, 'escluso' => false),
            array('nometabella' => $controller, 'nomecampo' => "$controller.id_target", 'etichetta' => 'ID '.getenv('SYSTEM_DEST'), 'ordine' => 20, 'larghezza' => 20, 'escluso' => false),
            array('nometabella' => $controller, 'nomecampo' => "$controller.id", 'escluso' => true),
            array('nometabella' => $controller, 'nomecampo' => "$controller.id_source", 'etichetta' => 'ID '.getenv('SYSTEM_SOURCE'), 'ordine' => 30, 'larghezza' => 20, 'escluso' => false),
            array('nometabella' => $controller, 'nomecampo' => "$controller.off", 'escluso' => true),
            //array('nometabella' => $controller, 'nomecampo' => 'Pippo', 'etichetta' => 'Pippo', 'ordine' => 40, 'larghezza' => 20, 'escluso' => false),
            
           
           // array('nometabella' => $controller, 'nomecampo' => "$controller.datanascita", 'etichetta' => 'Data di nascita', 'ordine' => 20, 'larghezza' => 12, 'escluso' => false),
           // array('nometabella' => $controller, 'nomecampo' => "$controller.saluto", 'etichetta' => 'Salutami', 'ordine' => 30, 'tipocampo' => 'string', 'campoextra' => true),
                //, "escluso" => false, "larghezza" => 15, "association" => false, "tipocampo"=>"string", "editabile"=>false
        );

        $colonneordinamento = array($controller . '.lista_desc' => 'ASC');

        $filtri = array(
                /* array("nomecampo" => "Listapreferenze.id",
                  "operatore" => Comparison::IN,
                  "valore" => $arr_listid
                  ), */
                /*  array("nomecampo" => "Cliente.nominativo",
                  "operatore" => Comparison::CONTAINS,
                  "valore" => "DegL'"
                  ), */
                /* array("nomecampo" => "Cliente.datanascita",
                  "operatore" => Comparison::EQ,
                  "valore" => array("date" => new DatetimeTabella("1980-02-05T00:00:00+00:00"))
                  ), */
                /* array("nomecampo" => "Cliente.datanascita",
                  "operatore" => Comparison::EQ,
                  "valore" => "1980-02-05")
                  , */

                /* array("nomecampo" => "Cliente.datanascita",
                  "operatore" => Comparison::GTE,
                  "valore" => "1980-02-04")
                  ,
                  array("nomecampo" => "Cliente.datanascita",
                  "operatore" => Comparison::LTE,
                  "valore" => "1980-02-06")
                  , */

                /* array("nomecampo" => "Cliente.datanascita",
                  "operatore" => Comparison::GTE,
                  "valore" => array("date" => new DatetimeTabella("1978-08-19 23:59:59"))
                  ),
                  array("nomecampo" => "Cliente.datanascita",
                  "operatore" => Comparison::LTE,
                  "valore" => array("date" => new DatetimeTabella("1978-08-20 00:00:00"))
                  ), */
                /* array("nomecampo" => "Cliente.iscrittoil",
                  "operatore" => Comparison::GTE,
                  "valore" => array("date" => new DatetimeTabella("1999-01-01 13:16:00"))
                  ), */
        );
        $prefiltri = array(
            array("nomecampo" => "Listapreferenze.id",
            "operatore" => Comparison::IN,
            "valore" => $arr_listid
            ),
        );
        //dump(json_encode($filtri));exit;
        $parametritabella = array('em' => ParametriTabella::setParameter('default'),
            'tablename' => ParametriTabella::setParameter($controller),
            'nomecontroller' => ParametriTabella::setParameter($controller),
            'bundle' => ParametriTabella::setParameter($bundle),
            'entityname' => ParametriTabella::setParameter($entityclassnotation),
            'entityclass' => ParametriTabella::setParameter($entityclass),
            'formclass' => ParametriTabella::setParameter($formclass),
            'modellocolonne' => ParametriTabella::setParameter(json_encode($modellocolonne)),
            'permessi' => ParametriTabella::setParameter(json_encode($this->getPermessi()->toJson($controller))),
            'urltabella' => ParametriTabella::setParameter($assetsmanager->getUrl('/') . $controller . '/' . 'tabella'),
            'baseurl' => ParametriTabella::setParameter($assetsmanager->getUrl('/')),
            'idpassato' => ParametriTabella::setParameter($idpassato),
            'titolotabella' => ParametriTabella::setParameter('Elenco ' . $controller),
            'multiselezione' => ParametriTabella::setParameter('1'),
            'editinline' => ParametriTabella::setParameter('0'),
            'paginacorrente' => ParametriTabella::setParameter('1'),
            'paginetotali' => ParametriTabella::setParameter(''),
            'righetotali' => ParametriTabella::setParameter('0'),
            'righeperpagina' => ParametriTabella::setParameter('15'),
            'estraituttirecords' => ParametriTabella::setParameter(0),
            'colonneordinamento' => ParametriTabella::setParameter(json_encode($colonneordinamento)),
            'filtri' => ParametriTabella::setParameter(json_encode($filtri)),
            'prefiltri' => ParametriTabella::setParameter(json_encode($prefiltri)),
            'traduzionefiltri' => ParametriTabella::setParameter(''),
        );

        return $this->render(
                        $template,
                        array(
                            'parametritabella' => $parametritabella,
                        )
        );
    }

}