(function () {
    'use strict';
    
    angular.module('app')
    .controller('IndexCtrl',['$scope','$http','$uibModal','$filter','logger',IndexCtrl])
    .controller('InputCtrl',['$scope','$http','$uibModalInstance','$window','chrome','asignaciones','chromebooks','method',InputCtrl])
    .controller('InputModalReport',['$scope','$http','$uibModalInstance','$window',InputModalReport])
    .controller('InputModalReportDg',['$scope','$http','$uibModalInstance','$window',InputModalReportDg])
    .controller('IndexCtrlAsig',['$scope','$http','$uibModal','$filter','logger',IndexCtrlAsig])
    .controller('InputModalAsig',['$scope','$http','$uibModalInstance','chrome','method','logger',InputModalAsig])
    .controller('InputModalStatus',['$scope','$http','$uibModalInstance','chrome','logger',InputModalStatus])
    .controller('InputModalAdd',['$scope','$http','$uibModalInstance','logger',InputModalAdd])
    
    
    .controller('InputModalRemoverCsv',['$scope','$http','$uibModalInstance','$cookies','$timeout','Upload','logger',InputModalRemoverCsv]);

    function IndexCtrl($scope,$http,$uibModal,$filter,logger)
    {
        var init;
        var q='';
        $scope.alerts = [];
        $scope.numPerPage = 20;
        $scope.currentPage = 1;
        $scope.currentPage = [];
        $scope.select = select;
        //$scope.onFilterChange = onFilterChange;
        //$scope.search = search;
        $scope.historial = [];
        $scope.chromebooks = resume.chromebooks;
        $scope.asignaciones   = resume.asignaciones;
        $scope.pagination = {total_rows:0};

        select();
        
        function select(page) {
            //var end, start;
            //start = (page - 1) * $scope.numPerPage;
            //end = start + $scope.numPerPage;
            page = page?page:1;
            
            $http.get(SITE_URL+'admin/chromebooks/asignaciones/load/'+page,{params:{q:q}}).then(function(response){
                
                var result = response.data;
                $scope.pagination = result.data.pagination;
                $scope.asignaciones = result.data.rows;
            });
            
            
            //return $scope.currentPageAsignaciones = $scope.filteredStores.slice(start, end);
        };

        
        
        $scope.details = function(chrome)
        {
             var modalInstance = $uibModal.open({
                            animation: $scope.animationsEnabled,
                            templateUrl: 'modalForm.html',
                            controller: 'InputCtrl',
                  
                            resolve: {
                                asignaciones: function () {
                                    return false;
                                },
                                chromebooks: function () {
                                    return false;
                                },
                                chrome: function () {
                                    return chrome;
                                },
                                method:function(){
                                    return 'details';
                                }
                            }
                      });
        }
        $scope.not_in_asignados = function(item,inverse)
        {
            
             if ($scope.asignaciones) {
                
                  var result = true;
                  $.each($scope.asignaciones,function(index,data){
                    
                      if(data.id_chromebook == item.id)
                      {
                        result = false;
                         
                      }
                  });
                  
                  return result;

            }
            
            return true;
        }
       $scope.add = function(chrome)
       {
           
              var modalInstance = $uibModal.open({
                            animation: $scope.animationsEnabled,
                            templateUrl: 'modalForm.html',
                            controller: 'InputCtrl',
                  
                            resolve: {
                                chrome: function () {
                                    return chrome;
                                },
                                asignaciones: function () {
                                    return $scope.asignaciones;
                                },
                                chromebooks: function () {
                                    return $scope.chromebooks;
                                },
                                 method: function () {
                                    return 'create';
                                }
                            }
                      });
              modalInstance.result.then(function (result) {
                
                //$scope.asignaciones.push(result);
                //$scope.vehiculo_select = result;
                
                if(result.status)
                {
                    select(1);
                }
                if(result.message)
                {
                    if(result.status)
                    {
                        logger.logSuccess(result.message);
                    }
                    else
                    {
                        logger.logError(result.message);
                    }
                }
            }, function (result) {



            });
       }   
       $scope.remove = function(chrome)
       {
           
              var modalInstance = $uibModal.open({
                            animation: $scope.animationsEnabled,
                            templateUrl: 'modalForm.html',
                            controller: 'InputCtrl',
                  
                            resolve: {
                                chrome: function () {
                                    return chrome;
                                },
                                asignaciones: function () {
                                    return $scope.asignaciones;
                                },
                                chromebooks: function () {
                                    return $scope.chromebooks;
                                },
                                 method: function () {
                                    return 'edit';
                                }
                            }
                           
                      });
              modalInstance.result.then(function (result) {
                
                //$scope.asignaciones.push(result);
                //$scope.vehiculo_select = result;
                
                if(result.status)
                {
                    select(1);
                } 
                
               // $scope.status = result.status;
                
                if(result.message)
                {
                    if(result.status)
                    {
                        logger.logSuccess(result.message);
                    }
                    else
                    {
                        logger.logError(result.message);
                    }
                    //$scope.alerts.push({type:result.status?'success':'danger',message:result.message});
                }
                
            }, function (result) {



            });
       }  
       $scope.search = function()
       {
            q = $scope.search_asignados;
            select(1);
       }

        $scope.report = function()
        {
             var modalInstance = $uibModal.open({
                            animation: $scope.animationsEnabled,
                            templateUrl: 'modalReport.html',
                            controller: 'InputModalReport',
                  
 
                      });
        }
        
        
        
        
        
        
        $scope.remover_csv = function()
        {
                     var modalInstance = $uibModal.open({
                            animation: $scope.animationsEnabled,
                            templateUrl: 'modalRemoverCsv.html',
                            controller: 'InputModalRemoverCsv',
                  
 
                      });
                      
                modalInstance.result.then(function (status) {
                    
                    if(status)
                    {
                        location.href= SITE_URL+'admin/chromebooks/asignaciones';
                    }
                           // $scope.selected = selectedItem;
                }, function () {
                            
                           // $treeData.contentFolders($scope.current_level); //verificar funcionamiento
                            
                });
        }
    }

    function IndexCtrlAsig($scope,$http,$uibModal,$filter,logger)
    {

       $scope.chromebooks = resume;

       $scope.asignar = function(chrome)
       {
           
              var modalInstance = $uibModal.open({
                            animation: $scope.animationsEnabled,
                            templateUrl: 'modalFormAsig.html',
                            controller: 'InputModalAsig',
                            resolve: {
                                chrome: function () {
                                    return chrome;
                                },
                                method: function () {
                                    return 'create';
                                }                                  
                            }
                      });

       } 
       $scope.remover = function(chrome)
       {
           
              var modalInstance = $uibModal.open({
                            animation: $scope.animationsEnabled,
                            templateUrl: 'modalFormAsig.html',
                            controller: 'InputModalAsig',
                            resolve: {
                                chrome: function () {
                                    return chrome;
                                } ,
                                method: function () {
                                    return 'edit';
                                }                              
                            }
                      });

       }  

       $scope.newChrome = function()
       {
           
              var modalInstance = $uibModal.open({
                            animation: $scope.animationsEnabled,
                            templateUrl: 'modalAdd.html',
                            controller: 'InputModalAdd',
                  
                            resolve: {
                                chrome: function () {
                                    return chrome;
                                },
                            }
                      });

       }  
       $scope.config = function(chrome)
       {
           
              var modalInstance = $uibModal.open({
                            animation: $scope.animationsEnabled,
                            templateUrl: 'modalStatus.html',
                            controller: 'InputModalStatus',
                  
                            resolve: {
                                chrome: function () {
                                    return chrome;
                                },
                            }
                      });

       }  
       $scope.report_dg = function()
        {
             var modalInstance = $uibModal.open({
                            animation: $scope.animationsEnabled,
                            templateUrl: 'modalReportDg.html',
                            controller: 'InputModalReportDg',
                  
 
                      });
        }
    }

    function InputCtrl($scope,$http,$uibModalInstance,$window,chrome,asignaciones,chromebooks,method)
    {
        
        $scope.orgs = orgs;
        $scope.dispose = true;
        $scope.method = method;
        $scope.history = history;
        $scope.chrome = chrome;
        $scope.emails = [];
        $scope.total_emails=0;
        
        if(chrome.id_chromebook)//Pauta para desasignar
        {
            $scope.form = {
                id:chrome.id,
                id_chromebook : chrome.id_chromebook,
                org:{org_path:chrome.org_path},
                email:{email:chrome.email,full_name:chrome.responsable},
                observaciones:chrome.observaciones
            };
            
            $scope.message = '<strong>ATENCION</strong><br/>  Este equipo va a ser removido';
        }
        else //Pauta para asignar
        {
            $scope.form = {
                
                id_chromebook : chrome.id,
                org:{org_path:chrome.org_path},

            };
        }
        
        $scope.title = 'Asignar/Remover '+$scope.form.id_chromebook;
        
        if(method=='details')
        {
            $scope.title = 'Detalles '+$scope.form.id_chromebook;
            history();
        }
        function history()
        {
            $http.post(SITE_URL+'admin/chromebooks/asignaciones/history/'+$scope.form.id_chromebook,{}).then(function(response){                
                var result =response.data;
                
                if(result.status)
                    $scope.historial = result.data;
                
            });
        }
        $scope.cancel = function () {
            $uibModalInstance.dismiss("cancel");
        }
        
        $scope.save = function()
        {
            if($scope.form.org.org_path != chrome.org_path && !confirm('La organización al cual va ser asignado es diferente.¿Desea continuar?'))
            {
                return false;
            }
            
            $scope.dispose = false;
            var url = chrome.id_chromebook?'admin/chromebooks/asignaciones/remover/':'admin/chromebooks/asignaciones/asignar/';
            $http.post(SITE_URL+url+$scope.form.id_chromebook,$scope.form).then(function(response){
                
                $scope.dispose = true;
               var result = response.data;
               
               if(result.status)
               {
                    //console.log(chrome);
                    //console.log(asignados);
                    if(chrome.id_chromebook)
                    {
                        var index = asignaciones.indexOf(chrome);
                        asignaciones.splice(index,1);
                        
                        chromebooks.push({id:chrome.id_chromebook,org_path:chrome.org_path});
                        
                        $window.open(SITE_URL+'admin/chromebooks/acuse_f/devolucion/'+result.data); 
                    }
                    else{
                        ///Verificar viabilidad
                        var index = chromebooks.indexOf(chrome);
                        chromebooks.splice(index,1);
                        
                        result.data.org_path = $scope.form.org.org_path;
                        result.data.full_name = $scope.form.email.full_name;
                        
                        
                         $window.open(SITE_URL+'admin/chromebooks/acuse_f/comodato/'+result.data.id); 
                        //result.data.id = String(result.data.id);
                        //console.log(result.data);
                        //asignaciones.push(result.data);
                    }
                     $uibModalInstance.close(result);
                   
               }
               else{
                   $scope.status = result.status;
                   $scope.message = result.message;
               }
              
               
                 
            });
        }
        $scope.$watch('form.org',function(newValue,oldValue){
            
            
            
            var org_path = newValue;
           
           
            if(!newValue)
            {
               return ;
            }
            
            
            if(oldValue && oldValue != newValue )$scope.org_path='';
            $scope.total_emails =0;
            var email = '';
            if($scope.form.email)
            {
                email = $scope.form.email.email;
            }
            
            $http.post(SITE_URL+'admin/chromebooks/asignaciones/get_emails',{org_path:org_path.org_path,email:email}).then(function(response){
 
               
               $scope.emails = response.data;
               $.each($scope.emails,function(index,data){
                
                    $scope.total_emails++;
               });
                
            });
            
            
        });

        $scope.valid_form = function () {
            return $scope.frm.$valid;
        }


        $scope.change = function()
        {
          /*var org_path = $scope.form.org.org_path;
          var id_chromebook = $scope.form.id_chromebook

          $http.post(SITE_URL+'admin/chromebooks/asignaciones/getOrgChrome',{org_path:org_path,id_chromebook:id_chromebook}).then(function(response){
                  
                  var result = response.data;

                   $scope.message = result.message;

                   if (result.status == false){
                     $scope.form.org_distinct = true;
                   }else
                   {
                     $scope.form.org_distinct = false;
                   }
                                                 
            })*/;
         
        }
    }

    function InputModalReport($scope,$http,$uibModalInstance,$window)
    {
          $scope.orgs = orgs;

         $scope.cancel = function(){
             $uibModalInstance.dismiss("cancel");
        }

                
        $scope.save = function(){

            var estatus = $scope.report.estatus?$scope.report.estatus:'';
            var org_path = $scope.report.org?$scope.report.org.org_path:'';
            
            if(org_path == null)
            {
              $scope.message = 'Favor de llenar todos los campos';
            }
            else
            {
              $window.open(SITE_URL+'admin/chromebooks/report/?estatus='+estatus+'&org='+org_path); 

              $uibModalInstance.close();
            }
                                  
        }

    }

    function InputModalAsig($scope,$http,$uibModalInstance,chrome,method,logger)
    {
        $scope.orgs = orgs;
        
        var org = chrome.org_path?chrome.org_path:'';
        
        $scope.method = method;
        
        $scope.form = {
                
                id : chrome.id,
                org : chrome.org_path?chrome.org_path:''
            };


         $scope.cancel = function(){
             $uibModalInstance.dismiss("cancel");
        }


        $scope.valid_form = function (){
           return $scope.report.$valid;
        } 
                
        $scope.save = function(){

            var serie = $scope.form.id;
            var org_path = $scope.form.org.org_path;

           
            $http.post(SITE_URL+'admin/chromebooks/asignarOrg',{org_path:org_path,serie:serie}).then(function(response){
              
                 var result = response.data;
               
               if(result.status)
               {
                   $scope.chrome = chrome;

                   chrome.org_path = org_path;
                   
                   logger.logSuccess(result.message);

                   $uibModalInstance.close();


               }
               else{
                   $scope.status = result.status;

                   $scope.message = result.message;

               }
            });
                  
        }

        $scope.remove = function(){

            var serie = $scope.form.id;
 
            $http.post(SITE_URL+'admin/chromebooks/removerOrg',{serie:serie}).then(function(response){
              
               var result = response.data;
               
               if(result.status)
               {
                   $scope.chrome = chrome;
                   
                   chrome.org_path = null;

                   logger.logSuccess(result.message);

                   $uibModalInstance.close();
               }
               else
               {
                   $scope.status = result.status;

                   $scope.message = result.message;

               }
            });
                  
        }


    }

    function InputModalAdd($scope,$http,$uibModalInstance,logger)
    {
        $scope.orgs = orgs; 
        $scope.chromebooks = resume.chromebooks;
      
         $scope.cancel = function(){
             $uibModalInstance.dismiss("cancel");
        }

   
        $scope.save = function(){

            var serie    = $scope.frm_add.serie;
            var org_path = $scope.frm_add.org.org_path?$scope.frm_add.org.org_path:null;

            $http.post(SITE_URL+'admin/chromebooks/newChromebook',{org_path:org_path,serie:serie}).then(function(response){
               var result = response.data;

               if(result.status)
               {
                     resume.chromebooks.unshift({'id':serie,'org_path':org_path});
                
                     $uibModalInstance.close();

                     logger.logSuccess(result.message);
               }
               else
               {
                   $scope.status = result.status;

                   $scope.message = result.message;
               }


            });

        }
    }
    
        
    function InputModalRemoverCsv($scope,$http,$uibModalInstance,$cookies,$timeout,Upload,logger)
    {     
      $scope.orgs = orgs;
      $scope.remove_result = [];
      $scope.dispose = true;
      $scope.message  = false;
      $scope.status   = false;

         $scope.cancel = function(){
             $uibModalInstance.dismiss("cancel");
         }

   
        $scope.close = function(){

                 
                     $uibModalInstance.close(true);
                  

        }

        $scope.upload_file = function(file,type)
        {
            
            if(!file) return false;
            var org_path = $scope.remove.org?$scope.remove.org.org_path:'';

            $scope.dispose = false;
            $scope.message  = false;
            
            $scope.remove_result = [];


            
            
            file.upload = Upload.upload({
              url: SITE_URL+'admin/chromebooks/asignaciones/upload',
              data: { org:org_path,file: file,csrf_hash_name:$cookies.get(pyro.csrf_cookie_name)}
            });
           
            
            file.upload.then(function (response) {
              var  result = response.data,
                   data   = response.data.data;
              $timeout(function () {
                  file.result = response.data;
                  $scope.dispose = true;

                  $scope.status = result.status;
                  $scope.message = result.message;
                  $scope.remove_result = result.data;
                  
                  
                  if(result.status == true)
                  {
                     setTimeout(function() {
                        location.href = SITE_URL+'admin/chromebooks/asignaciones';
                    }, 2000);
                     
                  }
                  /*else
                  {
                      
                  } */  
                /* if(result.asignado)
                  {
                      $scope.cambio = true;
                  } 

                  console.log( $scope.remove_result);*/

                 
                 
              });
            }, function (response) {
              if (response.status > 0)
                $scope.errorMsg = response.status + ': ' + response.data;
            }, function (evt) {
              
              file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
            });
            
            
        }
    }  
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    function InputModalStatus($scope,$http,$uibModalInstance,chrome,logger)
    {
             $scope.form_status = {
                
                id : chrome.id,
                estatus : chrome.estatus,
                email : chrome.email, 
                observaciones : chrome.observaciones,
                org_path : chrome.org_path
            };

         $scope.cancel = function(){
             $uibModalInstance.dismiss("cancel");
        }


        $scope.valid_form = function (){
           return $scope.frm_status.$valid;
        } 
                
        $scope.save = function(){

           var send_data = $scope.form_status

            $http.post(SITE_URL+'admin/chromebooks/config',send_data).then(function(response){
              
              var result = response.data;
               console.log(result);

               if(result.status)
               {
                   $scope.chrome = chrome;
                   
                   $scope.chrome.estatus  = send_data.estatus;

                   $scope.chrome.observaciones  = send_data.observaciones;
                  
                   logger.logSuccess(result.message);

                   $uibModalInstance.close();


               }
               else{
                   $scope.status = result.status;

                   $scope.message = result.message;

               }
            });
                  
        }


   
 
    }

    function InputModalReportDg($scope,$http,$uibModalInstance,$window)
    {
        $scope.orgs = orgs;

         $scope.cancel = function(){
             $uibModalInstance.dismiss("cancel");
        }
                
        $scope.save = function(){

            var estatus = $scope.form_status.estatus?$scope.form_status.estatus:'';
            var org = $scope.form_status.org?$scope.form_status.org:'';
            var org_post = org?'&org='+org.org_path:'';
                  $window.open(SITE_URL+'admin/chromebooks/report/?estatus='+estatus+org_post); 

              $uibModalInstance.close();
            
                                  
        }

    }
})();
