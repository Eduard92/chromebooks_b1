
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin extends Admin_Controller {
  protected $section='chromebooks';
  public function __construct()
  {
    parent::__construct();
        
      //  $this->load->library('GService');
        $this->load->model(array('chromebook_m','asignacion_m','files/file_folders_m','emails/org_m'));
        $this->lang->load(array('chromebook','calendar'));
        $this->load->library(array('files/files'));
    }
        
   function index()
    {   
        $this->load->library('centros/centro');
         $orgs_path = array();
        
         $resume = array(
            'chromebooks' => array(),         
         );
         
         $orgs         = $this->org_m->get_all();
         $base_where   = array();
         
         if(!group_has_role('chromebooks','admin_chrome'))
        {
            $orgs_perm = Centro::GetPermissions('orgs');
          
            $orgs_path = $this->org_m->where_in('id',$orgs_perm)->dropdown('id','org_path');
                if(count($orgs_path)>0)
                 {
                    $disponibles  = $this->chromebook_m->select('chromebooks.id AS id,chromebooks.org_path,estatus')
                                                ->where_in('org_path',$orgs_path)
                                                ->where('id NOT IN(SELECT id_chromebook FROM default_chromebook_asignacion WHERE removido IS  NULL)',null)
                                               // ->where('(removido IS NULL)',NULL)
                                                //->join('chromebook_asignacion','chromebook_asignacion.id_chromebook=chromebooks.id','LEFT')
                                                //->where(array('id NOT IN(SELECT id_chromebook FROM default_chromebook_asignacion WHERE removido IS NULL)'=>null))
                                                //->group_by('default_chromebooks.id')
                                                //->order_by('chromebook_asignacion.removido','DESC')
                                                ->get_all();
                    
                    $asignados = $this->asignacion_m->select('email,chromebooks.id AS id,chromebooks.org_path,\'asignado\' AS estatus')
                                        ->where_in('org_path',$orgs_path)
                                        //->select('id_chromebook AS id,chromebook_asignacion.email,estatus,chromebooks.observaciones,org_path')
                                        ->join('chromebooks','chromebooks.id=chromebook_asignacion.id_chromebook')
                                        ->where('removido IS NULL',null)
                                        ->get_all();
                 }
            
        }
        else{
                    $disponibles  = $this->chromebook_m->select('chromebooks.id AS id,chromebooks.org_path,estatus')
                                                
                                                ->where('id NOT IN(SELECT id_chromebook FROM default_chromebook_asignacion WHERE removido IS  NULL)',null)
                                               // ->where('(removido IS NULL)',NULL)
                                                //->join('chromebook_asignacion','chromebook_asignacion.id_chromebook=chromebooks.id','LEFT')
                                                //->where(array('id NOT IN(SELECT id_chromebook FROM default_chromebook_asignacion WHERE removido IS NULL)'=>null))
                                                //->group_by('default_chromebooks.id')
                                                //->order_by('chromebook_asignacion.removido','DESC')
                                                ->get_all();
                    
                    $asignados = $this->asignacion_m->select('email,chromebooks.id AS id,chromebooks.org_path,\'asignado\' AS estatus')
                                        
                                        //->select('id_chromebook AS id,chromebook_asignacion.email,estatus,chromebooks.observaciones,org_path')
                                        ->join('chromebooks','chromebooks.id=chromebook_asignacion.id_chromebook')
                                        ->where('removido IS NULL',null)
                                        ->get_all();
                
        }
        
        $resume = array_merge($disponibles,$asignados);
        
        //print_r($resume);
        $this->template->title($this->module_details['name'])
                   ->append_metadata('<script type="text/javascript"> var orgs='.json_encode($orgs).', resume='.json_encode($resume).';</script>')
                   ->set('chromebooks',$resume)
                   ->append_js('module::chromebook.controller.js')
                   ->build('admin/chromebooks/index');
    }

    public function newChromebook()
    {
        
         $result = array(
         
            'status' => false,
            'message'=>'',
            'data'   => array()
         );

            $chromebook = $this->chromebook_m->get($this->input->post('serie')) ;

            if($chromebook)
            {

                 $result['message'] =  lang('chromebook:exist');
            }
            else
            {   
                $chromebook_ = $this->chromebook_m->create($this->input->post());

                $result['status'] = true;
                $result['data'] =  $chromebook_ ;
                $result['message'] = lang('chromebook:new_success');
            }
              

        return $this->template->build_json($result);
    }

    public function asignarOrg()
    {
        
         $result = array(
            'status' => false,
            'message'=>'',
            'data'   => array()
         );

                
            $chromebook = $this->chromebook_m->get($this->input->post('serie')) ;

            if($chromebook)
            {
                $data = array(
                'org_path'  =>  $this->input->post('org_path'));

                    if($this->chromebook_m->update($this->input->post('serie'),$data))
                    {            
                        $result['message'] = lang('chromebook:new_asigned');
                        $result['status'] = true;
                    }
                    else
                    {
                        $result['message'] = lang('chromebook:error');
                    }                
            }
            else
            {   
                $result['message'] = 'No existe el registro';
            }
              
               

        return $this->template->build_json($result);
    }

    
    public function removerOrg()
    {
        
         $result = array(
            'status' => false,
            'message'=>'',
            'data'   => array()
         );


            $chromebook = $this->chromebook_m->get($this->input->post('serie')) ;

            if($chromebook)
            {
                      $data = array(
                      'org_path'  =>  null);

                    if($this->chromebook_m->update($this->input->post('serie'),$data))
                    {            
                                $result['message'] = lang('chromebook:removed');
                                $result['status'] = true;
                    }
                    else
                    {
                        $result['message'] = lang('chromebook:error');
                    }
                 
            }
            else
            {   
                $result['message'] = 'No existe el registro';
            }
              
               

        return $this->template->build_json($result);
    }

















    public function config()
    {
        
         $result = array(
         
            'status' => false,
            'message'=>'',
            'data'   => array()
         );
           //S $chromebook = $this->chromebook_m->where(array('email IS NULL' => null, ))->get($this->input->post('id')) ;
            $chromebook  = $this->chromebook_m->where(array('id NOT IN(SELECT id_chromebook FROM default_chromebook_asignacion WHERE removido IS NULL)'=>null))
                                ->get($this->input->post('id')) ;
           
            if($chromebook)
            {
                if($this->chromebook_m->update($this->input->post('id'),array(
                      'estatus'  =>  $this->input->post('estatus'),
                      'observaciones' =>$this->input->post('observaciones')?$this->input->post('observaciones'):null
                  )))
                {
                    $result['status'] = true;
                    $result['message'] = lang('chromebook:status_change');
                }
                else
                {
                 $result['message'] = lang('chromebook:error_status_change');   
                }
            }
            else
            {   
              
                $result['message'] = lang('chromebook:asignada');
            }
              
             return $this->template->build_json($result);
    }
    public function acuse($table='asignado',$id=0)
    {
        $chromebook = $this->chromebook_asignacion_m->get($id) ;
        if(!$chromebook)
        {
            
            $this->session->set_flashdata('error',lang('global:not_found_edit'));
            
            redirect('admin/chromebooks');
        }
        
        $base_where['chromebook_asignacion.asignado is not null AND default_chromebook_asignacion.removido is null'] = NULL;
        $chromebook= $this->db->select('*')
                                ->where($base_where)
                                ->where('id_chromebook',$id)
                                ->join('chromebooks','chromebook_asignacion.id_chromebook=chromebooks.id')
                                ->join('emails','chromebook_asignacion.email=emails.email')
                                ->get('chromebook_asignacion')->row() ;

        /*$alumno = $this->select('*')
                       ->where('idalum',$chromebook->id)*/

        ini_set('max_execution_time', 300);

        $this->load->library(array('pdf'));
        
        $html2pdf = new HTML2PDF('P', 'A4', 'es');
        

        ob_clean();
       
        $output = ''; 

        $doc = 'comodato_alumno';


        $output=$this->template->set_layout(false)
          //                   ->title('Reporte ')
                               ->enable_parser(true)
            ->build('templates/'.$doc,
              array('serial'=>$chromebook->serial,
                    'responsable'=>$chromebook->responsable,
                    'plantel'=>substr($chromebook->org_path,9),
                    'email'=>$chromebook->email,
                    'alumno'=>$chromebook->full_name),true);
           
        $html2pdf->writeHTML($output);
        $html2pdf->Output($doc.'_'.now().'.pdf','D');
     
    }

    public function acuse_f($tipo= '',$id_history = 0)
    {
         ini_set('max_execution_time', 0);
      $this->load->library('curl');
      $asignacion = $this->asignacion_m->select('*, chromebook_asignacion.id AS folio')
                                       ->join('emails','emails.email=chromebook_asignacion.email')
                                       ->get_by(array(
                                            'chromebook_asignacion.id'=>$id_history,
                                            //'emails.email' => $this->input->get('email')
                                        ));
                                       
      
       if($asignacion->table && $asignacion->table == 'alumnos' && ($tipo == 'comodato' || $tipo == 'devolucion' ))
        {
           $idalum  = $asignacion->table_id;
           
            
          
            
           $alumno_json = $this->curl->set_user('cobacam','1psk2355')->get('https://rk.cobacam.edu.mx/api/wsalumnos/'.$idalum.'?type=json');
           
           if(!$alumno_json)
           {
                    $this->session->set_flashdata('error',sprintf(lang('alumno:not_found'),$asignacion->full_name));
                    redirect('admin/chromebooks/asignaciones');
           }
          
           $centros_json = $this->curl->init()->get('https://rk.cobacam.edu.mx/api/wscentros/?type=json');
           
           $ctrl = 0;
           $centros = json_decode(trim($centros_json));
           $centro  = false;
         
           while($ctrl<count($centros))
           {
               
              
               
               $asignacion->org_path = str_replace('/Alumnos/','',$asignacion->org_path);
               $asignacion->org_path = str_replace('-','',$asignacion->org_path);
               $asignacion->org_path = replace_string($asignacion->org_path);
               
               $centros[$ctrl]->nombre = replace_string($centros[$ctrl]->nombre);
               
               //print_r($centros[$ctrl]->nombre.':'.strlen($centros[$ctrl]->nombre).'-'.$asignacion->org_path.':'.strlen($asignacion->org_path).'<br/>');
               
               if(strtolower($centros[$ctrl]->nombre) == strtolower($asignacion->org_path))
               {
                    //print_r($centros[$ctrl]->nombre.' : '.strlen($centros[$ctrl]->nombre).' - '.$nombre_centro.' : '.strlen($nombre_centro).'<br/>');
                   $centro = $centros[$ctrl];
                   $ctrl   = count($centros);
               }
               $ctrl++;
           }
            
           $alumno = json_decode($alumno_json);
              
              if(!$centro)
              {
                    $this->session->set_flashdata('error',lang('centro:not_found'));
                    redirect('admin/chromebooks/asignaciones');
              }
              
              //$centro = json_decode($centro_json);
              
              //$director = $this->db->where(array('id_centro' => $id_centro ,'activo' => 1,'user_id IS NOT NULL' => NULL ))->get('directores')->row();
              
              $director_json = $this->curl->init()->set_user('cobacam','1psk2355')->get('https://rk.cobacam.edu.mx/api/wsempleados/'.$centro->director.'?type=json');
              
              
              $director = $director_json?json_decode($director_json):array();
              
              $data['director'] = $director->nombre;
              if($tipo == 'comodato')
              {
                $datetime = new DateTime($asignacion->asignado);
                
                $doc = 'comodato_alumno';
                $dia =$datetime->format('d');
                $mes = month_long($datetime->format('m'));// strftime ("%B",strtotime(date("M")));
                $anio = $datetime->format('Y');
                
                $dia_string = $this->numtoletras($dia);
                $anio_string = $this->numtoletras($anio);
                    $data = array('serial'=>$asignacion->id_chromebook,
                    //'responsable'=>$asignacion->responsable,
                    'plantel'=>$centro->nombre,
                    'email'=>$asignacion->email,
                    'alumno'=>$asignacion->full_name,
                    'folio'=>$asignacion->folio,
                    'responsable'=>$alumno->tutor?$alumno->tutor:$asignacion->full_name,
                    'domicilio'=>$alumno->dom_tutor?$alumno->dom_tutor:'CONOCIDO',
                    'grado'=>$alumno->grado,
                    'grupo'=>$alumno->grupo,
                    'director'=>$director,
                    'dia'=>$dia,
                    'mes'=>$mes,
                    'anio'=>$anio,
                    'dia_string'=>strtolower($dia_string),
                    'anio_string'=>strtolower($anio_string));
              }
              elseif ($tipo == 'devolucion') 
              {
                
                if(!$asignacion->removido)
                {
                    $this->session->set_flashdata('error',lang('chromebook:error_doc'));
            
                     redirect('admin/chromebooks/asignaciones');
                }
                
                $datetime = new DateTime($asignacion->removido);
                // $centro = $this->db->select('localidad , municipio, ')
                  //      ->where('id',$id_centro)
                    //    ->get('default_centros')->row(); 
                    
               
                if($centro->localidad == $centro->municipio)
                {
                    //$fecha= $centro->municipio.', Campeche, '.strftime(" %d de %B del %Y", strtotime($datetime->format('Y-m-d')))
                   $fecha= $centro->municipio.', Campeche, a '.$datetime->format('d').' de '.month_long($datetime->format('m')).' de '.$datetime->format('Y');  
                }     
                else 
                {
                    //$fecha= $centro->localidad.', '.$centro->municipio.', Campeche, '.strftime(" %d de %B del %Y", strtotime($datetime->format('Y-m-d')));
                    $fecha= $centro->localidad.', '.$centro->municipio.', Campeche, a '.$datetime->format('d').' de '.month_long($datetime->format('m')).' del '.$datetime->format('Y');
                } 
               // $fecha = 'San Francisco de Campeche, Campeche, '.strftime(" %d de %B del %Y", strtotime($datetime->format('Y-m-d')));
                $doc = 'devolucion_chrome';
                $data = array('serial'=>$asignacion->id_chromebook,
                    //'responsable'=>$asignacion->responsable,
                    'plantel'=> $centro->nombre,//substr($asignacion->org_path,9),
                    'alumno'=>$asignacion->full_name,
                    'matricula'=>$alumno->matricula,
                    'fecha'=>$fecha,
                    'observaciones'=>$asignacion->observaciones);# code...
              }
   
        }
        else
        {
           $this->session->set_flashdata('error',lang('chromebook:error_doc'));
            
            redirect('admin/chromebooks/asignaciones');
        }   
        
        
                  
       
        $this->load->library(array('pdf'));
        
        $html2pdf = new HTML2PDF('P', 'A4', 'es');
        
        ob_clean();
       
        $output = ''; 
        $output=$this->template->set_layout(false)
                               ->enable_parser(true)
            ->build('templates/'.$doc,$data,true);
         
        $html2pdf->writeHTML($output);
        $html2pdf->Output($doc.'_'.now().'.pdf','I');
        
     
    }
       
    
        
    
    
    
    
    public function report()
    {
        $estatus = $_GET["estatus"];
        $org =     $_GET["org"];
        $base_where =  array();
        if(is_numeric($estatus)&& $estatus==0)
        {
            if(empty($org) == false)
            {
              $base_where['org_path'] = $org;
              $plantel = explode("/",$org);  
                      $plantel  =  str_replace('/','',$plantel[count($plantel)-1]);    
              $title = 'Relación de Chromebooks Disponibles '.$plantel; 
 
            }
                 $chromebooks  = $this->chromebook_m->where($base_where)
                                ->where('id NOT IN(SELECT id_chromebook FROM default_chromebook_asignacion WHERE removido IS NULL)',null)
                                ->get_all();
                if(empty($chromebooks) == true)
                {
                  $title = $plantel.' No Cuenta con Chromebooks Disponibles'; 
                } else
                {
                 //$title = 'Relación de Chromebooks Disponibles';         
                     $table = '<tbody>';
                     $table_header = '<tr>';
                    $count = count($chromebooks)<9?count($chromebooks):9;
                    for ($i = 1; $i <= $count; $i++) 
                    {
                     $table_header .='<th width="63"; align="center" style="border-bottom: #a6ce39 2px solid;padding: 3px; font-size: 10px;">Serial</th>';
                    }
                     $table_header .= '</tr>';
                    $c=0;
                    $count = 0;
                    foreach ($chromebooks as $chromebook)
                    {        
                         if($c == 0)
                        {
                             $table .= '<tr>';                                
                        }    
                                    
                        $table .='<td  width="63"; align="left" style="padding: 3px;vertical-align: middle;font-size: 10px; border-bottom: #7A7A7A 1px solid;">'.$chromebook->id.'</td>';
                        $c++;
                        $count++;
                        if($c == 9)
                        {
                             $table .= '</tr>'; 
                             $c = 0;
                        }
                    }
                 
                    if($c == 0){
                        $table .='</tbody>';
                    }
                    else{
                        $table .='</tr></tbody>';
                    } 
                     $total= 'Total: '.$count;      
                }           
          
        }
        elseif(is_numeric($estatus)&& $estatus==1)
        {   
            $base_where   = array();         
            $chromebooks = $this->asignacion_m->where($base_where)
                                ->select('responsable,full_name,observaciones, org_path,chromebook_asignacion.id AS id,chromebook_asignacion.email,asignado,id_chromebook')
                                
                                ->join('emails','emails.email=chromebook_asignacion.email')
                                ->where('removido IS NULL',null)->where('org_path',$org)->get_all();
                    $count = 0;
                    if(empty($chromebooks)==false){
                      $plantel = explode("/",$org);  
                      $plantel  =  str_replace('/','',$plantel[count($plantel)-1]);
                     $title = 'Relación de Chromebooks Asignadas a/al '.$plantel;
                     $table_header = '<tr>';
                     $table_header .='<th width="63"; align="center" style="border-bottom: #a6ce39 2px solid;padding: 3px; font-size: 10px;">Serial</th>';
                     $table_header .='<th width="200"; align="center" style="border-bottom: #a6ce39 2px solid;padding: 3px; font-size: 10px;">Nombre</th>';
                     $table_header .='<th width="170"; align="center" style="border-bottom: #a6ce39 2px solid;padding: 3px; font-size: 10px;">Org</th>';
                     $table_header .='<th width="200"; align="center" style="border-bottom: #a6ce39 2px solid;padding: 3px; font-size: 10px;">Email</th>';
                     $table_header .= '</tr>';
                    $table = '<tbody>';
                  
                    foreach ($chromebooks as $chromebook)
                    {        
                        $count++;
                        $table .= '<tr>';  
                                    
                        $table .='<td  width="63"; align="left" style="padding: 3px;vertical-align: middle;font-size: 10px; border-bottom: #7A7A7A 1px solid;">'.$chromebook->id_chromebook.'</td>';
                        $table .='<td  width="200"; align="left" style="padding: 3px;vertical-align: middle;font-size: 10px; border-bottom: #7A7A7A 1px solid;">'.$chromebook->full_name.'</td>';
                        $table .='<td  width="170"; align="center" style="padding: 3px;vertical-align: middle;font-size: 10px;border-bottom: #7A7A7A 1px solid;"> '.$chromebook->org_path.'</td>';
                        $table .='<td  width="200"; align="center" style="padding: 3px;vertical-align: middle;font-size: 10px; border-bottom: #7A7A7A 1px solid;">'.$chromebook->email.'</td>';
                        $table .= '</tr>'; 
                    }
                    $table .= '</tbody>';
                    $total= 'Total Asignadas: '.$count;
                }
                else{
                    $table .= '<tr>';  
                                    
                        $table .='<td  width="650"; align="center" style="padding: 3px;vertical-align: middle;font-size: 14px;"> '.$org.' NO Cuenta con Chromebooks Asignadas</td>';
                        $table .= '</tr>'; 
                }
        }
        else{
            if(is_string($estatus) && $estatus != 'general')
            {
                //$base_where['org_path'] = '/Dirección General';
                if($estatus == 'reparacion')
                {
                    $base_where['estatus'] = 'reparacion';
                }
                elseif($estatus == 'baja')
                {
                    $base_where['estatus'] = 'baja';
                }
                elseif($estatus == 'extraviado')
                {
                    $base_where['estatus'] = 'extraviado';
                }
                elseif($estatus == 'disponible')
                {
                    $base_where['estatus'] = 'disponible';
                }
                else
                {
                   $this->session->set_flashdata('error',lang('chromebook:error_doc'));
            
                    redirect('admin/chromebooks');
                }
                $chromebooks  = $this->chromebook_m->where($base_where)
                                ->where('id NOT IN(SELECT id_chromebook FROM default_chromebook_asignacion WHERE removido IS NULL)',null)
                                ->get_all();
                    if(empty($chromebooks) == true)
                    {
                      $title = ' No se existen Chromebooks con el Estatus: '.ucwords($estatus); 
                    } 
                    else
                    {
                         $title = 'Relación de Chromebooks con Estatus: '.ucwords($estatus);          
                         $table = '<tbody>';
                         $table_header = '<tr>';
                        $count = count($chromebooks)<9?count($chromebooks):9;
                        for ($i = 1; $i <= $count; $i++) 
                        {
                         $table_header .='<th width="63"; align="center" style="border-bottom: #a6ce39 2px solid;padding: 3px; font-size: 10px;">Serial</th>';
                        }
                        $table_header .= '</tr>';
                        $c=0;
                        $count = 0;
                        foreach ($chromebooks as $chromebook)
                        {        
                             if($c == 0)
                            {
                                 $table .= '<tr>';                                
                            }    
                                        
                            $table .='<td  width="63"; align="left" style="padding: 3px;vertical-align: middle;font-size: 10px; border-bottom: #7A7A7A 1px solid;">'.$chromebook->id.'</td>';
                            $c++;
                            $count ++;
                            if($c == 9)
                            {
                                 $table .= '</tr>'; 
                                 $c = 0;
                            }
                            
                        }
                     
                        if($c == 0){
                            $table .='</tbody>';
                        }
                        else{
                            $table .='</tr></tbody>';
                        }      
                  }   
                  $total= 'Total: '.$count;      
            }
            elseif(is_string($estatus) && $estatus == 'general')
            {
                $title = 'Reporte De Chromebooks'; 
                $count = 0; 
                $asignados = $this->asignacion_m->select('COUNT(estatus) as cantidad,estatus,org_path')
                                ->join('chromebooks','chromebooks.id=chromebook_asignacion.id_chromebook')
                                ->where('removido IS NULL',null)
                                ->group_by('estatus,org_path')
                                ->order_by('org_path','ASC')
                                ->get_all();
                     $table_header = '<tr>';
                     $table_header .='<th width="250"; align="center" style="border-bottom: #a6ce39 2px solid;padding: 3px; font-size: 10px;">Plantel/Centro</th>';
                     $table_header .='<th width="200"; align="center" style="border-bottom: #a6ce39 2px solid;padding: 3px; font-size: 10px;">Estatus</th>';
                     $table_header .='<th width="200"; align="center" style="border-bottom: #a6ce39 2px solid;padding: 3px; font-size: 10px;">Total</th>';
                     $table_header .= '</tr>';
                    $table = '<tbody>';
                   
                    foreach ($asignados as $asignado)
                    {        
                         $org_path_explod = explode("/",$asignado->org_path);  
                        $asignado->org_path  =  str_replace('/','',$org_path_explod[count($org_path_explod)-1]);
                        $count++;
                        $table .= '<tr>';  
                                    
                        $table .='<td  width="250"; align="left" style="padding: 3px;vertical-align: middle;font-size: 10px; border-bottom: #7A7A7A 1px solid;">'.$asignado->org_path.'</td>';
                        $table .='<td  width="200"; align="center" style="padding: 3px;vertical-align: middle;font-size: 10px;border-bottom: #7A7A7A 1px solid;"> Asignados </td>';
                        $table .='<td  width="200"; align="center" style="padding: 3px;vertical-align: middle;font-size: 10px; border-bottom: #7A7A7A 1px solid;">'.$asignado->cantidad.'</td>';
                        $table .= '</tr>'; 
                    }
                    $chromebooks  = $this->chromebook_m->select('COUNT(estatus) as cantidad,estatus,org_path')
                               ->where(array('id NOT IN(SELECT id_chromebook FROM default_chromebook_asignacion WHERE removido IS NULL)'=>null))
                                ->group_by('estatus,org_path')
                                ->order_by('estatus','ASC')
                                ->get_all();
                    foreach ($chromebooks as $chromebook)
                    {        
                         $org_path_explod = explode("/",$chromebook->org_path);  
                        $chromebook->org_path  =  str_replace('/','',$org_path_explod[count($org_path_explod)-1]);
                        $count++;
                        $chromebook->org_path = $chromebook->org_path?$chromebook->org_path:'Almacen';
                        $table .= '<tr>';  
                                    
                        $table .='<td  width="250"; align="left" style="padding: 3px;vertical-align: middle;font-size: 10px; border-bottom: #7A7A7A 1px solid;">'.$chromebook->org_path.'</td>';
                        $table .='<td  width="200"; align="center" style="padding: 3px;vertical-align: middle;font-size: 10px;border-bottom: #7A7A7A 1px solid;"> '.ucwords($chromebook->estatus).' </td>';
                        $table .='<td  width="200"; align="center" style="padding: 3px;vertical-align: middle;font-size: 10px; border-bottom: #7A7A7A 1px solid;">'.$chromebook->cantidad.'</td>';
                        $table .= '</tr>'; 
                    }
                    $table .= '</tbody>';
                $total_asignados = $this->asignacion_m->select('COUNT(estatus) as cantidad')
                                ->join('chromebooks','chromebooks.id=chromebook_asignacion.id_chromebook')
                                ->where('removido IS NULL',null)
                                ->get_all();
                 $total_disponibles  = $this->chromebook_m->select('COUNT(estatus) as cantidad')
                               ->where(array('id NOT IN(SELECT id_chromebook FROM default_chromebook_asignacion WHERE removido IS NULL)'=>null,'estatus' => 'disponible'))
                                ->get_all();
                $total_reparacion  = $this->chromebook_m->select('COUNT(estatus) as cantidad')
                               ->where(array('id NOT IN(SELECT id_chromebook FROM default_chromebook_asignacion WHERE removido IS NULL)'=>null,'estatus' => 'reparacion'))
                                ->get_all();
                 $total_baja  = $this->chromebook_m->select('COUNT(estatus) as cantidad')
                               ->where(array('id NOT IN(SELECT id_chromebook FROM default_chromebook_asignacion WHERE removido IS NULL)'=>null,'estatus' => 'baja'))
                                ->get_all();
                 $total_extraviado  = $this->chromebook_m->select('COUNT(estatus) as cantidad')
                               ->where(array('id NOT IN(SELECT id_chromebook FROM default_chromebook_asignacion WHERE removido IS NULL)'=>null,'estatus' => 'extraviado'))
                                ->get_all();
            
           $totales =  $total_asignados['0']->cantidad + $total_disponibles['0']->cantidad + $total_reparacion['0']->cantidad + $total_baja['0']->cantidad + $total_extraviado['0']->cantidad;
            $total_gral= 'Total Asignadas: '.$total_asignados['0']->cantidad.' 
            <br /> Total Disponibles:'.$total_disponibles['0']->cantidad.' 
            <br /> Total Reparación:'.$total_reparacion['0']->cantidad.' 
            <br />Total Bajas:'.$total_baja['0']->cantidad.'
            <br />Total Extraviadas:'.$total_extraviado['0']->cantidad.'
            <br />Total Chromebooks:'.$totales;  
                     
            }
            else
            {
              $this->session->set_flashdata('error',lang('chromebook:error_doc'));
            
              redirect('admin/chromebooks');
            }
        }
        $fecha= 'Generado: '.date('d/m/Y');
        ini_set('max_execution_time', 300);
        $this->load->library(array('pdf'));
        
        $html2pdf = new HTML2PDF('P', 'A4', 'es');
        
        ob_clean();
       
        $output = ''; 
        $doc = 'reporte_chrome';
        $output=$this->template->set_layout(false)
          //                   ->title('Reporte ')
                               ->enable_parser(true)
            ->build('templates/'.$doc,
              array('table'=>$table,
                    'fecha'=>$fecha,
                    'table_header'=>$table_header,
                    'title'=>$title,
                    'total'=>$total,
                    'total_gral'=>$total_gral),true);
           
        $html2pdf->writeHTML($output);
        $html2pdf->Output($doc.'_'.now().'.pdf');
        
     
    }
     function numtoletras($xcifra)
    {
        $xarray = array(0 => "Cero",
            1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
            "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
            "VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
            100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
        );
    //
        $xcifra = trim($xcifra);
        $xlength = strlen($xcifra);
        $xpos_punto = strpos($xcifra, ".");
        $xaux_int = $xcifra;
        $XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
        $xcadena = "";
        for ($xz = 0; $xz < 3; $xz++) {
            $xaux = substr($XAUX, $xz * 6, 6);
            $xi = 0;
            $xlimite = 6; // inicializo el contador de centenas xi y establezco el límite a 6 dígitos en la parte entera
            $xexit = true; // bandera para controlar el ciclo del While
            while ($xexit) {
                if ($xi == $xlimite) { // si ya llegó al límite máximo de enteros
                    break; // termina el ciclo
                }
                $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
                $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres dígitos)
                for ($xy = 1; $xy < 4; $xy++) { // ciclo para revisar centenas, decenas y unidades, en ese orden
                    switch ($xy) {
                        case 1: // checa las centenas
                            if (substr($xaux, 0, 3) < 100) { // si el grupo de tres dígitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas
                                
                            } else {
                                $key = (int) substr($xaux, 0, 3);
                                if (TRUE === array_key_exists($key, $xarray)){  // busco si la centena es número redondo (100, 200, 300, 400, etc..)
                                    $xseek = $xarray[$key];
                                    $xsub = $this->subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
                                    if (substr($xaux, 0, 3) == 100)
                                        $xcadena = " " . $xcadena . " CIEN " . $xsub;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                    $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                                }
                                else { // entra aquí si la centena no fue numero redondo (101, 253, 120, 980, etc.)
                                    $key = (int) substr($xaux, 0, 1) * 100;
                                    $xseek = $xarray[$key]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                                    $xcadena = " " . $xcadena . " " . $xseek;
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 0, 3) < 100)
                            break;
                        case 2: // checa las decenas (con la misma lógica que las centenas)
                            if (substr($xaux, 1, 2) < 10) {
                                
                            } else {
                                $key = (int) substr($xaux, 1, 2);
                                if (TRUE === array_key_exists($key, $xarray)) {
                                    $xseek = $xarray[$key];
                                    $xsub = $this->subfijo($xaux);
                                    if (substr($xaux, 1, 2) == 20)
                                        $xcadena = " " . $xcadena . " VEINTE " . $xsub;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                    $xy = 3;
                                }
                                else {
                                    $key = (int) substr($xaux, 1, 1) * 10;
                                    $xseek = $xarray[$key];
                                    if (20 == substr($xaux, 1, 1) * 10)
                                        $xcadena = " " . $xcadena . " " . $xseek;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " Y ";
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 1, 2) < 10)
                            break;
                        case 3: // checa las unidades
                            if (substr($xaux, 2, 1) < 1) { // si la unidad es cero, ya no hace nada
                                
                            } else {
                                $key = (int) substr($xaux, 2, 1);
                                $xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
                                $xsub = $this->subfijo($xaux);
                                $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                            } // ENDIF (substr($xaux, 2, 1) < 1)
                            break;
                    } // END SWITCH
                } // END FOR
                $xi = $xi + 3;
            } // ENDDO
            if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
                $xcadena.= " DE";
            if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
                $xcadena.= " DE";
            // ----------- esta línea la puedes cambiar de acuerdo a tus necesidades o a tu país -------
    // ENDIF (trim($xaux) != "")
            // ------------------      en este caso, para México se usa esta leyenda     ----------------
            $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
            $xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
            $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
        } // ENDFOR ($xz)
        return trim($xcadena);
    }
    function subfijo($xx)
    { // esta función regresa un subfijo para la cifra
        $xx = trim($xx);
        $xstrlen = strlen($xx);
        if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
            $xsub = "";
        //
        if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
            $xsub = "MIL";
        //
        return $xsub;
        
     }
    
      
    
    
 }