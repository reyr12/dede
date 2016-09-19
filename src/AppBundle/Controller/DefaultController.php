<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\File;
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
         $archivo = '/etc/shorewall/maclist';
        $abrir = fopen($archivo,'r+');
        $contenido = fread($abrir,filesize($archivo));
        fclose($abrir);
         
        // Separar linea por linea
        $contenido = explode("\n",$contenido);
        for($i=0;$i<count($contenido);$i++){
            $datos=explode('  ', $contenido[$i]);
            if(count($datos)>2){
                $datos['id']=$i;
                $todo[]=$datos;
            }

        }
        return $this->render('default/index.html.twig', array(
            'entities' => $todo,
        ));
    }

    /**
     * @Route("/startips", name="startips")
     */
    public function startAction()
    {
        //$resultado=shell_exec('shorewall start');//echo  "hola" | sudo shorewall start 
        $proceder=shell_exec('sudo /usr/sbin/nsm_sensor_ips-start');
        $pos = strpos($proceder, 'ERROR');
        if ($pos === false) {
            $resultado=shell_exec('sudo shorewall start');
            //$result2=shell_exec('sudo /usr/sbin/nsm_sensor_ips-start');

        } else {
            $resultado=$proceder;
        }
         $body=$this->renderView('default/result.html.twig', array(
           'resultado'      => $proceder.$resultado,               
            ));
         //echo $proceder.$resultado;
           $res=json_encode(array('funciono' =>true,'elcont'=> $body));
        $response = new Response($res);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

     /**
     * @Route("/stopips", name="stopips")
     */
    public function stopAction()
    {
        //$resultado=shell_exec('shorewall start');//echo  "hola" | sudo shorewall start
        $proceder=shell_exec('sudo shorewall check');
        $pos = strpos($proceder, 'ERROR');
        if ($pos === false) {
            $result1=shell_exec('sudo shorewall clear');
            $result2=shell_exec('sudo /usr/sbin/nsm_sensor_ps-stop');
             $resultado=$result1.$result2;

        } else {
            $resultado=$proceder;
        }
        $body=$this->renderView('default/result.html.twig', array(
           'resultado'      => $proceder.$resultado,               
            ));
          $res=json_encode(array('funciono' =>true,'elcont'=> $body));
        $response = new Response($res);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/restarsh", name="restarSh")
     */
    public function restarshAction()
    {
        //$resultado=shell_exec('shorewall start');//echo  "hola" | sudo shorewall start
        $proceder=shell_exec('sudo shorewall check');
        $pos = strpos($proceder, 'ERROR');
        if ($pos === false) {
            $resultado=shell_exec('sudo shorewall restart');
           // $resultado=shell_exec('sudo /usr/sbin/nsm_sensor_ips-restart');

        } else {
            $resultado=$proceder;
        }

        $body=$this->renderView('default/result.html.twig', array(
           'resultado'      => $proceder.$resultado,               
            ));
         $res=json_encode(array('funciono' =>true,'elcont'=> $body));
        $response = new Response($res);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    /**
     * @Route("/maclist", name="maclist")
     */
    public function maclistAction()
    {
    /*
        $file = fopen("/etc/shorewall/maclist", "r") or exit("Unable to open file!");
        //Output a line of the file until the end is reached
        $i=0;
        while(!feof($file))
        {
            if($i>9){
                $datos=explode('  ', fgets($file));
                $todo[]=$datos;
            }else{
                fgets($file);
            }
            $i++;
           // echo fgets($file).'|'. $i."<br />";
        }
        fclose($file);
       */
        $archivo = '/etc/shorewall/maclist';
        $abrir = fopen($archivo,'r+');
        $contenido = fread($abrir,filesize($archivo));
        fclose($abrir);
         
        // Separar linea por linea
        $todo=array();
        $contenido = explode("\n",$contenido);
        for($i=0;$i<count($contenido);$i++){
            $datos=explode('  ', $contenido[$i]);
            if(count($datos)>2){
                $datos['id']=$i;
                $todo[]=$datos;
            }

        }
        $entity  = new File();
        $form = $this->createFormBuilder($entity)
        ->setAction($this->generateUrl('maclist_import'))
        ->add('nombre', 'choice', array(
            'choices'   => array('1' => 'Reemplazar todo', '2' => 'Al final del archivo', '3' => 'Al inicio del archivo')
            
        ))
        ->add('file')
        ->getForm();
        return $this->render('AppBundle:Etiqueta:index.html.twig', array(
            'entities' => $todo,
            'form' => $form->createView(),
        ));
    }

     /**
     * @Route("/maclist/new", name="maclist_new")
     */
    public function maclistnewAction()
    {
       return $this->render('AppBundle:Etiqueta:new.html.twig');
    }

     /**
     * @Route("/maclist/create", name="maclist_create")
     */
    public function maclistcreateAction()
    {
         $archivo = '/etc/shorewall/maclist';
        $file = fopen($archivo, "a");
         $txtAction=$this->get('request')->request->get('txtAction', '');
            $txtMac=$this->get('request')->request->get('txtMac', '');
            $txtIp=$this->get('request')->request->get('txtIp', '');
            $txtInterface=$this->get('request')->request->get('txtInterface', '');
            $contenido="\n".trim($txtAction)."  ".trim($txtInterface)."     ".trim($txtMac)."       ".trim($txtIp)."\n";
        fwrite($file, $contenido . PHP_EOL);
      //  fwrite($file, "Añadimos línea 2" . PHP_EOL);
        fclose($file);
        return $this->redirect($this->generateUrl('maclist'));
    }

     /**
     * @Route("/maclist/delete", name="maclist_delete")
     */
    public function maclistdeleteAction()
    {
         $puntero=$this->get('request')->request->get('cualid', '');
         $res=false;
        // Separar linea por linea
        if($puntero>0){ 
            $archivo = '/etc/shorewall/maclist';
            $abrir = fopen($archivo,'r+');
            $contenido = fread($abrir,filesize($archivo));
            fclose($abrir);        
            $contenido = explode("\n",$contenido);
            unset($contenido[$puntero]);
            $b = array_values($contenido);
            $otro = implode("\n",$b); 
            // Guardar Archivo
            $abrir = fopen($archivo,'w');
            fwrite($abrir,$otro);
            fclose($abrir);
            $res=true;
        }
        return $this->redirect($this->generateUrl('maclist'));
        /*$response = new Response(json_encode(array('funciono'=>$res)));
            $response->headers->set('Content-Type', 'application/json');
            return $response;*/
    }

     /**
     * @Route("/maclist/import", name="maclist_import")
     */
    public function maclistimportAction()
    {
         $entity  = new File();
        $form = $this->createFormBuilder($entity)
        ->add('nombre', 'choice', array(
            'choices'   => array('1' => 'Reemplazar todo', '2' => 'Al final del archivo', '3' => 'Al inicio del archivo'),
            'required'  => false,
        ))
        ->add('file')
        ->getForm();
//$form['file']->getData()->move('/var/www/mike/seatcrm/seatcrm/src/L2a/FileBundle/Entity/../../../../web/uploads/documents', 'algo.xls');
    if ($this->getRequest()->isMethod('POST')) {
        $form->bind($this->getRequest());
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->upload();
           // $em->persist($entity);
            chmod('/var/www/html/panelips/web/uploads/documents/'.$entity->getPath(), 0777);
            $respuesta = new response();
            $data = new \Readexcel_Readexcel();

            $data->setOutputEncoding('CP1251');

            $data->read('/var/www/panelips/web/uploads/documents/'.$entity->getPath());
//echo $entity->getUploadRootDir().'/'.$entity->getPath().$data->sheets[0]['numRows'];
            for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
		if(count($data->sheets[0]['cells'][$i])>3){
               		$contenido[]=trim($data->sheets[0]['cells'][$i][1])."  ".trim($data->sheets[0]['cells'][$i][2])."     ".trim($data->sheets[0]['cells'][$i][3])."       ".trim($data->sheets[0]['cells'][$i][4]);
		}
              if(count($data->sheets[0]['cells'][$i])<4){
                        $contenido[]=trim($data->sheets[0]['cells'][$i][1])."  ".trim($data->sheets[0]['cells'][$i][2])."     ".trim($data->sheets[0]['cells'][$i][3])."       ";
                }
 
// echo $data->sheets[0]['cells'][$i][1].'<br>';
            }
           switch ($entity->getNombre()) {
                        case 1:
                            $todo = implode("\n",$contenido); 
                            break;
                        case 2:
                            $archivo = '/etc/shorewall/maclist';
                            $abrir = fopen($archivo,'r+');
                            $content = fread($abrir,filesize($archivo));
                            fclose($abrir);                                   
                            $otro = implode("\n",$contenido); 
                            $todo =  $content .$otro;
                            break;
                        case 3:
                            $archivo = '/etc/shorewall/maclist';
                            $abrir = fopen($archivo,'r+');
                            $content = fread($abrir,filesize($archivo));
                            fclose($abrir);                                   
                            $otro = implode("\n",$contenido); 
                            $todo =  $otro.$content;
                            break;
                        
                        default:
                            # code...
                            break;
                    }   

            // Guardar Archivo
            $archivo = '/etc/shorewall/maclist';
            $abrir = fopen($archivo,'w');
            fwrite($abrir,$todo);
            fclose($abrir);      
    
        }
    }

    return $this->redirect($this->generateUrl('maclist'));
        /*return $this->render('AppBundle:Etiqueta:new.html.twig', array(
            'entities' => $todo,
        ));*/
    }

     /**
     * @Route("/maclist/export", name="maclist_export")
     */
    public function maclistexportAction()
    {       
        $archivo = '/etc/shorewall/maclist';
        $abrir = fopen($archivo,'r+');
        $content = fread($abrir,filesize($archivo));
        fclose($abrir);
         
        // Separar linea por linea
        $nombre = "maclist".date('YmdHis');
       /*         header("Content-Type: application/vnd.ms-excel");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("content-disposition: attachment;filename=".$nombre.'.xls');
               
      /*           header("Content-type: application/vnd.ms-excel");
header("Content-disposition: csv" . date("Y-m-d") . ".csv");
header( "Content-disposition: filename=".$nombre.".csv"); */
               $response = new response();

            $contenido= '<!DOCTYPE html>
                        <html>
                        <head>
                            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        </head>
                        <body>

                        <table>
                          ';
                           // echo $content;
                              $otros = explode("\n",$content);
                             // print_r($otros);
                           for($i=0;$i<count($otros);$i++){
                               //$datos=explode('  ', $otros[$i]);
                               $datos=explode('  ', $otros[$i]);
			//print_r($datos);
                          if(count($datos)>3){
			       $contenido.= '<tr><td>'.trim($datos[0]).'</td>';
                               $contenido.= '<td>'.trim($datos[1]).'</td>';
                               $contenido.= '<td>'.trim($datos[3]).'</td>';
                               $contenido.= '<td>'.trim($datos[6]).'</td></tr>';
                               $i++;
				}
                            }

                             $contenido.='
                              
                            </table>

                            </body>
                            </html>';
                            //echo  $contenido;
                       //return new Response($contenido);
                       $response->setContent($contenido);                    // the headers public attribute is a ResponseHeaderBag
                    $response->headers->set('Content-Type', 'application/vnd.ms-excel');
                    $response->headers->set('Expires', '0');
                    $response->headers->set('Cache-Control', ' must-revalidate, post-check=0, pre-check=0');
                    $response->headers->set('content-disposition', "attachment;filename=".$nombre.".xls");
                    return $response;
    }

      /**
     * @Route("/maclist/edit", name="maclist_edit")
     */
    public function maclisteditAction()
    {
        return $this->render('AppBundle:Etiqueta:new.html.twig', array(
            'entities' => $todo,
        ));
    }

     /**
     * @Route("/maclist/update", name="maclist_update")
     */
    public function maclistupdateAction()
    {
        $puntero=$this->get('request')->request->get('txtElid', '');
         $res=false;
        // Separar linea por linea
        if($puntero>0){ 
            $archivo = '/etc/shorewall/maclist';
            $abrir = fopen($archivo,'r+');
            $contenido = fread($abrir,filesize($archivo));
            fclose($abrir);        
            $contenido = explode("\n",$contenido);
            $txtAction=$this->get('request')->request->get('txtAction', '');
            $txtMac=$this->get('request')->request->get('txtMac', '');
            $txtIp=$this->get('request')->request->get('txtIp', '');
            $contenido[$puntero]=trim($txtAction)."  br0     ".trim($txtMac)."       ".trim($txtIp);
           // $b = array_values($contenido);
            $otro = implode("\n",$contenido); 
            // Guardar Archivo
            $abrir = fopen($archivo,'w');
            fwrite($abrir,$otro);
            fclose($abrir);
            $res=true;
        }
       // return $this->redirect($this->generateUrl('maclist'));
        
        $response = new Response(json_encode(array('funciono'=>$res)));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
    }

}
