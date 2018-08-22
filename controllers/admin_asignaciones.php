
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin_Asignaciones extends Admin_Controller {
  protected $section='chromebooks';
  public function __construct()
  {
    parent::__construct();
        
      //  $this->load->library('GService');
        $this->load->model(array('chromebook_m','asignacion_m','files/file_folders_m','emails/org_m'));
        $this->lang->load('chromebook');
        $this->load->library(array('files/files'));
        $this->load->library('centros/centro');
        $this->config->load('files/files');
        $this->_path = FCPATH.rtrim($this->config->item('files:path'), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;


         $this->validation_rules = array(
             array(
                'field' => 'full_name',
                'label' => 'Nombre',
                'rules' => 'trim'
            ),
             array(
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'trim'
            ),
             array(
                'field' => 'org_path',
                'label' => 'Org',
                'rules' => 'trim'
            ),
             array(
                'field' => 'serial',
                'label' => 'Serial',
                'rules' => 'trim'
            ),
             array(
                'field' => 'cargo',
                'label' => 'Cargo',
                'rules' => 'trim'
            ),
             array(
                'field' => 'observaciones',
                'label' => 'Observaciones',
                'rules' => 'trim'
            ),
            array(
                'field' => 'responsable',
                'label' => 'Responsable',
                'rules' => 'trim'
            ),
        );
    }
    function load($table='asignaciones',$page=1)
    {
        
         $result = array(
         
            'status' => false,
            'data'   => array()
         );
         
         $q            = $this->input->get('q');
         $base_where   = array();
         
         
         if($q)
         {
            $base_where['(id_chromebook LIKE \'%'.$q.'%\'  OR default_chromebook_asignacion.email LIKE \'%'.$q.'%\')'] = null;
         }
         

          if(!group_has_role('chromebooks','admin_asignaciones'))
         {
            $orgs_perm = Centro::GetPermissions('orgs');
          
            $orgs_path = $this->org_m->where_in('id',$orgs_perm)->dropdown('id','org_path');

            
         }
         
         if(count($orgs_path)>0)
         {
            $total_asignaciones = $this->asignacion_m->where_in('org_path',$orgs_path)->where($base_where)
                                ->join('emails','emails.email=chromebook_asignacion.email')
                                
                                ->count_by('removido IS NULL',null);
         }
         else
         {
             $total_asignaciones = $this->asignacion_m->where($base_where)
                                ->join('emails','emails.email=chromebook_asignacion.email')
                                
                                ->count_by('removido IS NULL',null);
         }
                                
         if($total_asignaciones)
         {
            $data = array(
                'pagination' => false,
                'rows'       => array()
            );
            
            $pagination = create_pagination('admin//chromebooks/asignaciones/'.$anio, $total_asignaciones,20,5);
         
         if(!group_has_role('chromebooks','admin_asignaciones'))
         {
           $asignaciones = $this->asignacion_m->where_in('org_path',$orgs_path)->where($base_where)
                                ->select('responsable,full_name,observaciones, org_path,chromebook_asignacion.id AS id,chromebook_asignacion.email,asignado,id_chromebook')
                                ->limit($pagination['limit'],$pagination['offset'])//->select('*,chromebook_asignacion.id AS id')
                                ->join('emails','emails.email=chromebook_asignacion.email')
                                ->where('removido IS NULL',null)->get_all();

            
         }else
         {
             $asignaciones = $this->asignacion_m->where($base_where)
                                ->select('responsable,full_name,observaciones, org_path,chromebook_asignacion.id AS id,chromebook_asignacion.email,asignado,id_chromebook')
                                ->limit($pagination['limit'],$pagination['offset'])//->select('*,chromebook_asignacion.id AS id')
                                ->join('emails','emails.email=chromebook_asignacion.email')
                                ->where('removido IS NULL',null)->get_all();

         }
           
            
                                
            $result['status']   = true;
            $data['rows']       = $asignaciones;
            $data['pagination'] = $pagination;
                                
            $result['data'] = $data;
         }
         
         return $this->template->build_json($result);
    }
    function index()
    {
   
        $orgs_path = array();

         $resume = array(
            'chromebooks' => array(),
            'asignaciones'    => array()
         
         );
         
         //$orgs         = $this->org_m->get_all();
         $q            = $this->input->get('q');
         $base_where   = array();
         
         if($q)
         {
            $base_where['id_chromebook'] = $q;
         }

          if(!group_has_role('chromebooks','admin_asignaciones'))
         {
            $orgs_perm = Centro::GetPermissions('orgs');
            
            $org = $this->org_m->where_in('id',$orgs_perm)->get_all();          
            $orgs_path = $this->org_m->where_in('id',$orgs_perm)->dropdown('id','org_path');

    
         }
         else
         {
            $org         = $this->org_m->get_all();
         }
         
         if(count($orgs_path)>0)
         {
         $chromebooks  = $this->chromebook_m->where_in('org_path',$orgs_path)
                                        ->where(array('id NOT IN(SELECT id_chromebook FROM default_chromebook_asignacion WHERE removido IS NULL)'=>null,'estatus'=>'disponible'))
                                        ->get_all();
        
         $total_asignaciones = $this->asignacion_m->where_in('org_path',$orgs_path)->where($base_where)
                                ->join('emails','emails.email=chromebook_asignacion.email')
                                
                                ->count_by('removido IS NULL',null);
                                
          
         $pagination = create_pagination('admin/chromebooks/asignaciones/', $total_asignaciones,20);
         
         
         
         $asignaciones = $this->asignacion_m->where_in('org_path',$orgs_path)->where($base_where)
                                ->select('full_name,observaciones, org_path,chromebook_asignacion.id AS id,chromebook_asignacion.email,asignado,id_chromebook')
                                ->limit($pagination['limit'],$pagination['offset'])//->select('*,chromebook_asignacion.id AS id')
                                ->join('emails','emails.email=chromebook_asignacion.email')
                                ->where('removido IS NULL',null)->get_all();   

         }
        else{

        $chromebooks  = $this->chromebook_m 
                                ->where(array('id NOT IN(SELECT id_chromebook FROM default_chromebook_asignacion WHERE removido IS NULL)'=>null,'estatus'=>'disponible'))
                                ->get_all();
        
         $total_asignaciones = $this->asignacion_m->where($base_where)
                                ->join('emails','emails.email=chromebook_asignacion.email')
                                
                                ->count_by('removido IS NULL',null);
                                
          
         $pagination = create_pagination('admin/chromebooks/asignaciones/', $total_asignaciones,20);
         
         
         
         $asignaciones = $this->asignacion_m->where($base_where)
                                ->select('full_name,observaciones, org_path,chromebook_asignacion.id AS id,chromebook_asignacion.email,asignado,id_chromebook')
                                ->limit($pagination['limit'],$pagination['offset'])//->select('*,chromebook_asignacion.id AS id')
                                ->join('emails','emails.email=chromebook_asignacion.email')
                                ->where('removido IS NULL',null)->get_all();
         
        }

                                
                                
         if($this->input->is_ajax_request())
         {
            
                return $this->template->build_json($asignaciones);
           
         }
         
        
         foreach($chromebooks as $chromebook)
         {
             $resume['chromebooks'][] = $chromebook;
         }
         
         foreach($asignaciones as $chromebook)
         {
            $resume['asignaciones'][] = $chromebook; 
         }
     
        $this->template->title($this->module_details['name'])
                   //->set('chromebooks',$chromebooks)
                   //->set('status',$status)
                   ->set('total_asignaciones',$total_asignaciones)
                   ->append_metadata('<script type="text/javascript"> var orgs='.json_encode($org).', resume='.json_encode($resume).';</script>')
                   ->set('resume',$resume)
                   ->append_js('module::chromebook.controller.js')
                   ->build('admin/index');
    }
    public function history($id=0)
    {
        $result = array(
            'status' => false,
            'data'   => array()
        );
        $historial = $this->asignacion_m->where('id_chromebook',$id)->get_all();
        
        if($historial)
        {
            $result['data']   = $historial;
            $result['status'] = true;
        }

      return $this->template->build_json($result);
    }
    public function asignar($id=0)
    {
        $result = array(
            'status' => false,
            'message' => '',
            'data'    => false
        );
        

        $asignacion = $this->asignacion_m->get_by(array(
            'id_chromebook' => $id,
            'removido IS NULL'      =>NULL, 
        ));
        
        if($asignacion)
        {
            $result['message'] = lang('chromebook:pre_asignado'); 
        }
        
        else
        {
            $email = $this->input->post('email');
            $insert = array(
                'responsable' => $email['full_name'],
                'email'       => $email['email'],
                'id_chromebook' => $id,
                'asignado'      => date('Y-m-d H:i:s'),
                'observaciones' => $this->input->post('observaciones')
            );

            
                if($result_id = $this->asignacion_m->insert($insert))
                {
                    $insert['id'] = $result_id ;
                    $result['message'] = lang('chromebook:asignado'); 
                    $result['data']   = $insert;
                    $result['status']   = true;
                        
                        if($this->input->post('org_distinct') == true)
                             { 
                                $this->chromebook_m->update($this->input->post('id_chromebook'),array(
                                'org_path' =>  $email['org_path'], ));
                             }

                }
                else
                {
                    $result['message'] = lang('chromebook:error'); 
                }            
            
        }
        
       
        return $this->template->build_json($result);
    }
    public function remover($id=0)
    {
        $result = array(
            'status' => false,
            'message' => '',
            'data'    => false
        );
        $asignacion = $this->asignacion_m->where('removido IS NULL',null)->get_by('id_chromebook',$id) ;
        if(!$asignacion)
        {
            
            $result['message'] = lang('global:not_found_edit');
            
            
        }
        
        else
        {
            $this->asignacion_m->update($asignacion->id,array(
                'removido' => date('Y-m-d H:i:s'),
                'observaciones' => $this->input->post('observaciones')
            ));

            $org = $this->input->post('org');

            $this->chromebook_m->update($id,array(
                                'org_path' =>  $org['org_path']));

            $result['id_history']= $asignacion->id;
            $result['message'] = lang('chromebook:removido');
            $result['status'] = true;
        }
        return $this->template->build_json($result);

    }
        public function details($id)
    {

        $orgs = $this->db->select('count(id), org_path')
        ->where('chromebook is null',null)->or_where('chromebook',0)->group_by('org_path')->get('emails')->result();
        $chromebook=$this->db->select('*, default_emails.id As id_email')->where('default_chromebooks.id',$id)
                            ->join('chromebooks','emails.email = chromebooks.email')
                            ->join('default_chromebook_historial','emails.email = chromebook_historial.email_log')
                            ->get('emails')->row() or redirect('admin/chromebooks');


         $this->template->title($this->module_details['name'])
                ->set('chromebook',$chromebook)
                ->set('orgs',array_for_select($orgs,'org_path','org_path'))
                ->build('admin/form'); 
    }
    function get_emails()
    {
        
        $org_path = $this->input->post('org_path');
        
        /*$result = $this->db->select('*')
                        ->order_by('emails.full_name','ASC')
                        ->where('default_emails.email NOT IN (
                            SELECT default_chromebook_asignacion.email FROM `default_chromebook_asignacion` 
                            JOIN `default_chromebooks` ON    `default_chromebook_asignacion`.`id_chromebook` =    `default_chromebooks`.`id` 
                            JOIN `default_emails`      ON    `default_chromebook_asignacion`.`email`      =    `default_emails`.`email`
                            WHERE default_chromebook_asignacion.asignado is not null AND default_chromebook_asignacion.removido is null)',null)
                        ->where('default_emails.org_path',$org_path)
                        ->get('emails')
                        ->result();*/
                        
          $result = $this->db->where('org_path',$org_path)
                             ->order_by('email')
                            ->get('emails')
                           
                            ->result();
          return $this->template->build_json($result);      
        //if($result)echo json_encode($result);
    }

    public function inicializar()
    {
        $base_where['email IS NOT NULL'] = null;
        $chromebooks= $this->chromebook_m
            ->where($base_where)
            ->get_all();

       foreach ($chromebooks as $chromebook) 
        {
           $data = array(
            'id_chromebook' => $chromebook->id,
            'email'         => $chromebook->email,
            'asignado'          => date('Y-m-d H:i:s', now()),
           );
           $this->db->insert('default_chromebook_asignacion',$data);
        }
    }

    public function getOrgChrome()
    {
         $result = array(
            'status' => false,
            'message' => '',
        );

        $org_path = $this->input->post('org_path');
        $serial = $this->input->post('id_chromebook');

        $chromebook = $this->chromebook_m->get_by('id' , $serial );

        if($chromebook->org_path == $org_path)
        {
             $result['status'] = true;
        }
        else{
            $result['message'] = lang('chromebook:org_distinct');
        }


     return $this->template->build_json($result);


    }

    function upload()
    {
        ini_set('max_execution_time', 0); 
        $this->load->model(array(
            'files/file_folders_m'
        ));
        $this->load->library('files/files');
        
        $result = array(
            'message' =>  '',
            'status' => true,
            'data'   => array()
        );
        
        $folder        = $this->file_folders_m->get_by_path('otros') OR show_error('Error al buscar la carpeta');
        $file_result   = Files::upload($folder->id,false,'file',false,false,false,'csv');
        
        
       
        if($file_result['status'])
        {
           
            $file_path = $this->_path.'/'.$file_result['data']['filename'];           
              
            $file   = fopen($file_path, 'r');

            $max_line_length = defined('MAX_LINE_LENGTH') ? MAX_LINE_LENGTH : 10000;
            
            $header          = fgetcsv($file,$max_line_length); 
            
           
            while (($line = fgetcsv($file)) !== FALSE) {
              $csv_array[] =  array_combine($header,$line);
            }
           
           
            fclose($file);
            
            //Borramos archivo
            
            Files::delete_file($file_result['data']['id']);
             

            foreach($csv_array as &$csv)
            {
                
                $csv['serial']  = utf8_encode($csv['serial']);

                if( $csv['serial'])
                {               
                    if( $csv['observaciones'])
                    {

                /*    if(!group_has_role('chromebooks','admin_asignaciones'))
                    {
                        $orgs_perm = Centro::GetPermissions('orgs');
          
                        $orgs_path = $this->org_m->where_in('id',$orgs_perm)->dropdown('id','org_path');
                   }


                   /* if(!group_has_role('chromebooks','admin_asignaciones'))
                    {
                        $asignado = $this->asignacion_m->select('chromebook_asignacion.id,id_chromebook, org_path,asignado,removido')
                            ->join('chromebooks','chromebooks.id=chromebook_asignacion.id_chromebook')
                            //->where_in('org_path',$orgs_path)
                            ->where('removido IS NULL',null)->where('asignado IS NOT NULL',null)->get_by('id_chromebook',$csv['serial']) ;
                      //  $asignado = $this->asignacion_m->where_in('org_path',$orgs_path)->where('removido IS NULL',null)->join('emails','emails.email=chromebook_asignacion.email')->get_by('id_chromebook',$csv['serial']) ;
                    }else
                    {*/
                         $asignado = $this->asignacion_m->select('chromebook_asignacion.id,id_chromebook, org_path,asignado,removido')
                            ->join('chromebooks','chromebooks.id=chromebook_asignacion.id_chromebook')
                           ->where( array('removido IS NULL' => NULL ,'asignado IS NOT NULL' => NULL ))->get_by('id_chromebook',$csv['serial']) ;
                      //  $asignado = $this->asignacion_m->where('removido IS NULL',null)->get_by('id_chromebook',$csv['serial']) ;

                   // }

                        if(!$asignado)
                        {
                            $csv['message'] = 'No se encuetra Asignada';
                            $csv['icon']    = 'fa fa-warning';
                            $csv['status']= false;
                            continue;
                        } 

                        else
                        {

                            if ($asignado->org_path != $this->input->post('org'))
                                {
                                    $csv['message'] = 'La Chromebook tiene otra ORG Registrada';
                                    $csv['icon']    = 'fa fa-warning';
                                    $csv['status']= false;
                                    continue;
                                }
                            else{
                                    if($this->asignacion_m->update($asignado->id,array(
                                        'removido' => date('Y-m-d H:i:s'),
                                        'observaciones' => $csv['observaciones'])))
                                    {        
                                        $csv['status']= true;
                                        $csv['message']='Removido';

                                        
                                    }
                                    else
                                    {
                                       $csv['status']= false;
                                       $csv['message'] = 'Error al remover la chromebook '.$csv['serial'].' comuniquese con el Administrador';
                                       $csv['icon']    = 'fa fa-warning';
                                        continue;
                                    }  
                                }             

                        }
                    }
                     else
                    {
                        $result['message']='Falta la columna observaciones en el archivo cargado';
                        $result['status'] = false;
                        
                    }
                }
                else
                {
                    $result['message']='Falta la columna serie en el archivo cargado';
                    $result['status'] = false;
                    
                }
                
               
                
            }
 
           
            $result['data']   = $csv_array;
            return $this->template->build_json($result);
             //echo  json_encode($result);
            //return $this->template->build_json($result);
            
        }
        
         
        return $this->template->build_json($result);
    }


      
    
    
 }