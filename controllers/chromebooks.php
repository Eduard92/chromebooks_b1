<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * The public controller for the Pages module.
 *
 * @author		PyroCMS Dev Team
 * @package		PyroCMS\Core\Modules\Pages\Controllers
 */
class Chromebooks extends Public_Controller
{

	/**
	 * Constructor method
	 */
	public function __construct()
	{
		parent::__construct();
    }
    public function index()
    {
         $email = null;

     /*  $total_asignados =  $this->db->select('org_path, COUNT(*) AS numrows')
          ->join('chromebooks','chromebooks.email=emails.email')
          ->group_by('org_path')
          ->get('emails')
          ->result();
      //  print_r($total_asignados);

                    */


    /*$asignado =  $this->db                                             
                         ->join('chromebooks','chromebooks.email=emails.email')
                         ->count_all_results('emails');

    $chromebook =  $this->db->count_all_results('chromebooks');

    $disponibles = $chromebook - $asignado;


           $query1 =  $this->db->select('org_path, COUNT(*) AS numrows')
          ->group_by('org_path')
          ->get('emails')
          ->result();
          // print_r($query1);

            
          $data;


          foreach($query1 as $elemento)
          {
            
            $org_path =  $elemento->org_path;
            $total = $elemento->numrows;
           

            foreach ($total_asignados as $alumnos) 
            {

              if ($org_path == $alumnos->org_path) 
              {

                  $alumnos->alumnos=   $total;
              
              } 
             
            
              

            }



              
 
          }
*/


 $this->template->set_layout(false)
            ->enable_parser(true)
            //->set('total_asignados',$total_asignados)
            //->set('asignados',$asignados)
           // ->set('data',$data)
          //  ->set('disponibles',$disponibles)
            ->build('index');   
    }

    public function remover()
    {
        $result   = false;
        $message  = '';      
        $asignado   = false; 
      
         $message = '<div class="alert alert-warning">01</div>';
        if($_POST)
        {
        

           $chromebook =  $this->db->where('serial',$this->input->post('serial'))->get('chromebooks')->row();
           

           if(!$chromebook)
           {
               $message = '<div class="alert alert-warning">No hay registro de la CHROMEBOOK</div>';
           }
           else{
                
               //$inc = 0;
               
               if($chromebook->email)
               {
                 
                    $asignado = $this->db->where('emails.email',$chromebook->email)
                                    ->join('chromebooks','chromebooks.email=emails.email')
                                    ->get('emails')->row();
                     $data = array(
                                'id_chromebook' => $chromebook->id,
                                'log'           => 'Removida',
                                'date'          => date('Y-m-d H:i:s', now()),
                                'email_log'     => $chromebook->email

                               );

                    $this->db->insert('default_chromebook_historial',$data); 
                  
                   $baja = array('email' => null,'updated_on'=>now());
                   
                   $this->db->where('serial',$chromebook->serial)
                                            ->set($baja)
                                            ->update('chromebooks');
                            
                    $message = '<div class="alert alert-success">La CHROMEBOOK con No. de Serie '.$chromebook->serial.' ha sido removida</div>';  

    
                    /*if(!$asignado)
                    {
                      $this->db->where('serial',$chromebook->serial)
                                            ->set($baja)
                                            ->update('chromebooks');

                        $message = '<div class="alert alert-danger"> CHROMEBOOK desvinculado al email: '.$chromebook->email.'</div>';
                    }
                    else
                    {  
                        $this->db->where('serial',$chromebook->serial)
                                            ->set($baja)
                                            ->update('chromebooks');


                        $message = '<div class="alert alert-danger">CHROMEBOOK desvinculado al email : '.$chromebook->email.'</div>';
                    }*/

               }
               else
               {
                     $message = '<div class="alert alert-info">La CHROMEBOOK con No. de Serie '.$chromebook->serial.' anteriormente ya habia sido removida.</div>';        
               }

           }
           
            
        
                     
           
        }

                      $total_asignados =  $this->db->where(array(
                             'org_path'=>$this->input->post('org_path')))
                      ->count_all_results('chromebooks');


        $this->template->set_layout(false)
            ->enable_parser(true)
            ->set('asignado',$asignado)
            ->set('message',$message)
            ->build('form_remover');   
    }

    public function agregar ()
    {


        //$this->load->helper('cookie');
        
        $result   = false;
        $message  = '';
        $org_path = $this->input->post('org_path');
        $serial = $this->input->post('serial');
        
        $asignado   = false; 
        $total_asignados = 0;
        
        
        if(!$_POST)
        {
            delete_cookie('total_asignados');
        }
        
        if($_POST && $org_path)
        {
            
            
            //set_cookie('org_path',$this->input->post('org_path'));
            
                
                
             
            
           
           $chromebook =  $this->db->where(array('id' => $serial,/* 'org_path' => $org_path*/))->get('Chromebooks')->row();

           /*
           $list =  $this->db->select('full_name,emails.org_path,emails.email AS email')
                    ->where(array(
                         'emails.org_path'=>$this->input->post('org_path')
                       
                     ))->order_by('grado,grupo')
                     ->join('alumnos','alumnos.idalum=emails.table_id')
                     //->join('chromebooks','chromebooks.email=emails.email','LEFT')
                     ->get('emails')->result();*/

            $list = $this->db->where(array(
                                'org_path'=>$org_path,
                                '(email NOT IN(SELECT email FROM default_chromebook_asignacion WHERE removido IS NULL))' => null))
                             ->order_by('full_name')
                             
                             ->get('emails')
                             ->result();

        
       // print_r($chromebook);
            
           
           if(!$chromebook)
           {
               $message = '<div class="alert alert-warning">No hay registro de la CHROMEBOOK</div>';
           }
           elseif ($chromebook->org_path != $org_path) {
               $message = '<div class="alert alert-warning">LA CHROMEBOOK NO PERTENECE AL CENTRO SELECCIONADO</div>';

           }
           else{
               $inc = 0;
               if($chromebook->org_path == $org_path)
               {
                    $asignado = $this->db->where(array('id_chromebook' => $serial,
                                           'removido IS NULL' =>NULL,))
                                    ->join('emails','emails.email=chromebook_asignacion.email')
                                    ->get('chromebook_asignacion')->row();

                   // print_r($asignado);       
                    if($asignado && !$asignado->email)
                    {
                        $message = '<div class="alert alert-danger"> CHROMEBOOK asignada pero no existe el beneficiado: '.$asignado->email.'</div>';
                    }
                    elseif($asignado)
                    {
                        $message = '<div class="alert alert-info">CHROMEBOOK asignada : '.$asignado->id_chromebook.'  / '.$asignado->email.'</div>';
                    }
                   //$asignado = array(
                     // 'email' => $chromebook->email,
                   //);
             //  }
               //else{
                   while(!$asignado && $chromebook->estatus == 'disponible')
                   {
                        foreach($list as $alum)
                        {

                            $insert = array(
                                'responsable' => $alum->full_name,
                                'email'       => $alum->email,
                                'id_chromebook' => $chromebook->id,
                                'asignado'      => date('Y-m-d H:i:s'),
                            );
                                       // print_r($insert);

                                                                 /* if(!$alum->serial)
                            {
                               
                               $data = array(
                                'id_chromebook' => $chromebook->id,
                                'log'           => 'asignado',
                                'date'          => date('Y-m-d H:i:s', now()),
                                'log_email'     => $chromebook->email

                               );
*/
                               //$this->db->insert('default_chromebook_historial',$data);
                                                    
                                
                            //$asignado = array('email' => $alum->email,'updated_on'=>now());
                                
                                if($asignado = $this->db->insert('chromebook_asignacion',$insert))
                                {
                                    //$asignado =  array(
                                      //  'org_path'  =>,
                                        //'full_name' =>,
                                        //'serial'    =>
                                  print_r($asignado);
                                    
                                    //);
                                    /*$this->db->where('emails.email',$alum->email)
                                    ->join('chromebooks','chromebooks.email=emails.email')
                                    ->get('emails')->row();*/
                                    //$asignado['full_name']  = $alum->full_name;
                                    //$asignado['given_name'] = $alum->given_name;
                                    //$asignado['family_name'] = $alum->family_name;
                                  //  $asignado['serial']    = $chromebook->id;
                                  //  $asignado['full_name'] = $alum->full_name;
                                  //  $asignado['org_path']  = $alum->org_path;
                                   // $asignado = (Object)$asignado;
                                    
                                    break;
                               }
                            //}
                        }
                        if(!$asignado)
                        {
                             $message = '<div class="alert alert-danger"> Se hay llegado al limite de registros</div>';
                             $asignado = true;
                        }
                        
                       
                        /*if(count($list) == $inc)
                        {
                            $asignado = true;
                        }*/
                        //$inc ++;
                        
                   }
               }
           }
           
            
                        
                     
           
        }
        
         $orgs = $this->db->group_by('org_path')->get('emails')->result();
         $this->template->set_layout(false)
            ->enable_parser(true)
            ->set('orgs',array_for_select($orgs,'org_path','org_path'))
            ->set('total_alumnos',isset($list)?count($list):0)
            ->set('total_asignados',$total_asignados?$total_asignados:'')
            ->set('asignado',$asignado)
            ->set('message',$message)
            ->build('form_lectura');        
    
    

    }

     public function consulta()
    {
        $result   = false;
        $message  = '';      
        $asignado   = false; 
      
         $message = '<div class="alert alert-success">01</div>';
        if($_POST)
        {
            if(strlen($this->input->post('serial'))!= 10)
            {
               $message = '<div class="alert alert-warning">Numero de Serie Incorrecto</div>';

            }
            else{

          $chromebook_levantamiento =  $this->db->where('chromebook',$this->input->post('serial'))->get('chromebook_levantamiento')->row();
          if($chromebook_levantamiento)
          {
            $message = '<div class="alert alert-warning">La CHROMEBOOK con No. de Serie '.$chromebook_levantamiento->chromebook.' ya habia sido escaneada</div>';
          }
          else{

           $chromebook =  $this->db->where('serial',$this->input->post('serial'))->get('chromebooks')->row();
           

           if(!$chromebook)
           {
                $data = array( 'chromebook' => $this->input->post('serial'),
                                'status' =>   0  );
                if($this->db->insert('default_chromebook_levantamiento',$data))
                {
                   $message = '<div class="alert alert-info">La CHROMEBOOK con No. de Serie '.$this->input->post('serial').' No se encontraba en la Base de Datos</div>';
                }
           }
           else
           {
            $data = array( 'chromebook' => $this->input->post('serial'),
                                'status' =>   1 );
                if($this->db->insert('default_chromebook_levantamiento',$data))
                {
                   $message = '<div class="alert alert-success">La CHROMEBOOK con No. de Serie '.$chromebook->serial.' Se encontraba en la Base de Datos</div>';
                }

                    
               
           }   
           } 
           }      
          
         }


        $this->template->set_layout(false)
            ->enable_parser(true)
            ->set('message',$message)
            ->build('form_consulta');   
    }


    ////////////////Asignar org////////////////

    public function asignarOrg ()
    {


        //$this->load->helper('cookie');
        
        $result   = false;
        $message  = '';
        $org_path = $this->input->post('org_path');
        $serial = $this->input->post('serial');
        
        $asignado   = false; 
        $total_asignados = 0;
        
        
        if(!$_POST)
        {
            delete_cookie('total_asignados');
        }
        
        if($_POST && $org_path)
        {

           $chromebook =  $this->db->where(array('id' => $serial,/* 'org_path' => $org_path*/))->get('Chromebooks')->row();
           
           
           if(!$chromebook)
           {
               $message = '<div class="alert alert-warning">No hay registro de la CHROMEBOOK</div>';
           }
           elseif ($chromebook->org_path != null) {
               $message = '<div class="alert alert-info">LA CHROMEBOOK ESTA ASIGNADA A: '.$chromebook->org_path.'</div>';

           }
           else{
               $inc = 0;

                    $asignado = $this->db->where(array('id_chromebook' => $serial,
                                           'removido IS NULL' =>NULL,))
                                    ->join('emails','emails.email=chromebook_asignacion.email')
                                    ->get('chromebook_asignacion')->row();
                    if($asignado)
                    {
                        $message = '<div class="alert alert-info">CHROMEBOOK asignada : '.$asignado->id_chromebook.'  / '.$asignado->email.'</div>';
                    }
                    else
                    {
                            $data = array('org_path'  =>  $org_path);

                            if($this->db->update('chromebooks', $data, array('id' => $serial)))
                            {            
                                $message = '<div class="alert alert-success">CHROMEBOOK ASIGNADA A: : '.$org_path.' CORRECTAMENTE</div>';
                            }
                            else
                            {
                                $message = '<div class="alert alert-danger">OCURRIO UN PROBLEMA AL ASIGNADA A : '.$serial.' A: '.$org_path.'</div>';
                            }   
                    }


               }
           }
           
            
          $total_asignados =  $this->db
                        ->where(array('org_path'=>$this->input->post('org_path')))
                        ->count_all_results('chromebooks');             
                     
           
        
        
         $orgs = $this->db->group_by('org_path')->get('emails')->result();
         $this->template->set_layout(false)
            ->enable_parser(true)
            ->set('orgs',array_for_select($orgs,'org_path','org_path'))
            //->set('total_alumnos',isset($list)?count($list):0)
            ->set('total_asignados',$total_asignados?$total_asignados:'')
            //->set('asignado',$asignado)
            ->set('message',$message)
            ->build('form_asigOrg');        
    
    

    }

    /////////////////////REMOVER ORG///////////////
        public function removerOrg ()
    {


        //$this->load->helper('cookie');
        
        $result   = false;
        $message  = '';
        $org_path = $this->input->post('org_path');
        $serial = $this->input->post('serial');
        
        $asignado   = false; 
        
        
        if($_POST)
        {
              $asignado = $this->db->where(array('id_chromebook' => $serial,
                                           'removido IS NULL' =>NULL,))
                                    ->join('emails','emails.email=chromebook_asignacion.email')
                                    ->get('chromebook_asignacion')->row();
            if($asignado)
            {
                $message = '<div class="alert alert-warning">CHROMEBOOK asignada : '.$asignado->id_chromebook.'  / '.$asignado->email.'</div>';
            }
            else
            {

              $chromebook =  $this->db->where(array('id' => $serial,/* 'org_path' => $org_path*/))->get('Chromebooks')->row();
           
           
              if(!$chromebook)
               {
                   $message = '<div class="alert alert-warning">No hay registro de la CHROMEBOOK</div>';
               }
              elseif($chromebook->org_path == null)
               {
                   $message = '<div class="alert alert-info">La CHROMEBOOK no habia sido asignada previamente</div>';
               }
               else 
               {
                   $data = array('org_path'  =>  null);

                            if($this->db->update('chromebooks', $data, array('id' => $serial)))
                            {            
                                $message = '<div class="alert alert-success">CHROMEBOOK REMOVIDA A: '.$chromebook->org_path.' CORRECTAMENTE</div>';
                            }
                            else
                            {
                                $message = '<div class="alert alert-danger">OCURRIO UN PROBLEMA AL INTENTAR REMOVER : '.$serial.' /: '.$org_path.'</div>';
                            }   
               }


            }
        }
           
            
                        
                     
           
        
        
         $orgs = $this->db->group_by('org_path')->get('emails')->result();
         $this->template->set_layout(false)
            ->enable_parser(true)
            ->set('orgs',array_for_select($orgs,'org_path','org_path'))
            //->set('total_alumnos',isset($list)?count($list):0)
            //->set('total_asignados',$total_asignados?$total_asignados:'')
            //->set('asignado',$asignado)
            ->set('message',$message)
            ->build('form_removOrg');        
    
    

    }
    
}