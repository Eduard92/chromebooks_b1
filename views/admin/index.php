<section ng-controller="IndexCtrl">
        
    
    <a href="#"  ng-click="report()" uib-tooltip="Reporte" class="btn btn-primary pull-right">Reporte</a> 
    
   
    <a href="#"  ng-click="remover_csv()" uib-tooltip="Reporte" class="btn btn-default pull-right">Remover por CSV</a> 
    <a href="<?=base_url('files/download/9036ebac1a89657')?>" target="_blank" class="btn btn-default pull-right"> Descargar plantilla</a>
    <div class="row col-md-12">

        <div class="col-md-6">
            <h4 class="text-success">Disponibles</h4>
            <input type="text" class="form-control" ng-model="txt_disponibles" placeholder="Buscar series" />
            <hr />
            <p class="text-right">Total registros:{{(chromebooks).length}}</p>
            <table class="table">
                <thead>
                    <tr>
                        <th>SERIAL</th>
                        <th width="20%"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="chrome in chromebooks | filter:txt_disponibles|limitTo:20">
                        <td> <a href="#" ng-click="details(chrome)">{{chrome.id}}</a><br />
                            <span class="text-muted">Disponible</span>
                        </td>
                        <td><a href="#" ng-click="add(chrome)">Asignar</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <h4 class="text-success">Asignados</h4>
            <div class="input-group">
                <input type="text" class="form-control" data-ng-model="search_asignados" placeholder="Buscar series, email o nombre" />
                     <span class="input-group-btn">
                        <button class="btn" ng-click="search()"><i class="fa fa-search"></i></button>
                     </span>
                
            </div>
            <hr />
            
            
            <uib-alert ng-repeat="alert in alerts" type="{{alert.type}}" close="closeAlert($index)">{{alert.message}}</uib-alert>
            <p class="text-right">Total registros:{{pagination.total_rows}}</p>
            <table class="table">
                <thead>
                    <tr>
                        <th>SERIAL</th>
                        <th width="20%"></th>
                    </tr>
                </thead>
                <tr ng-repeat="chrome in asignaciones">
                    <td>
                        <a href="#" ng-click="details(chrome)">{{chrome.id_chromebook}}</a><br />
                        <span class="text-muted">{{chrome.email}}</span>
                    </td>
                    <td><a href="#" ng-click="remove(chrome)">Remover</a></td>
                </tr>
            </table>
            
             <uib-pagination class="pagination-sm"
                    ng-model="currentPage"
                    total-items="pagination.total_rows"
                    max-size="4"
                    ng-change="select(currentPage)"
                    items-per-page="numPerPage"
                    rotate="false"
                    previous-text="&lsaquo;" next-text="&rsaquo;"
                    boundary-links="true"></uib-pagination>
        
        </div>
    </div>
</section>
<script type="text/ng-template" id="modalForm.html">
    <div class="modal-header" >
        <h3>{{title}}</h3>
    </div>
     <?php  echo form_open('','name="frm" id="frm"');?>
    <div class="modal-body">
   
        <uib-tabset class="ui-tab">
            <uib-tab  heading="Asignacion"  ng-if="method!='details'" active="true" >
            
                    <div ng-bind-html="message" ng-if="message" class="alert alert-info" ng-class="{'alert-success':!form.id && status,'alert-danger':!form.id&& !status}"></div>
                    <div class="form-group">
                            <label>No. serial</label>
                            <input type="text" class="form-control" ng-model="form.id_chromebook" disabled/>
                     </div>   
                      <div class="form-group">
                            <label>Organización</label>
                            <select class="form-control" name="org" ng-readonly="form.id" ng-model="form.org" ng-options="org.name for org in orgs track by org.org_path"  required>
                                <option value=""> [ Elegir ] </option>
                            </select>
                            <div ng-messages="frm.org.$error"  role="alert" ng-if="frm.org.$dirty">
                                    <div class="text-danger" ng-message="required">Este campo es requerido</div>
                            </div>
                            
                     </div> 
                     <div class="form-group">
                            <label>Responsable</label>
                            <div class="input-group">
                                <select class="form-control" name="email" ng-readonly="form.id" ng-model="form.email" ng-options="email.full_name for email in emails track by email.email" required>
                                    <option value=""> [ Elegir ] </option>
                                    
                                </select>
                                <span class="input-group-addon">{{total_emails}}</span>
                            </div>
                            <div ng-messages="frm.email.$error"  role="alert" ng-if="frm.email.$dirty">
                                    <div class="text-danger" ng-message="required">Este campo es requerido</div>
                             </div>
                             
                     </div>   
                     
                     <div class="form-group">
                            <label>Email</label>
                            <input type="hidden" class="form-control" name="responsable"  ng-readonly="form.id" ng-model="form.email.full_name">
                            <input type="text" class="form-control"   ng-readonly="form.id" value="{{form.email.email}}" >
                            <div ng-messages="frm.responsable.$error"  role="alert" ng-if="frm.responsable.$dirty">
                                    <div class="text-danger" ng-message="required">Este campo es requerido</div>
                             </div>
                     </div> 
                     <div class="form-group">
                            <label>Observaciones</label>
                            <textarea class="form-control" ng-model="form.observaciones"></textarea>
                     </div>  
                     
                      
            </uib-tab>
            <uib-tab  heading="Historial" ng-click="history()" >
                 <div class="alert alert-info text-center" ng-if="!historial"><?=lang('chromebook:not_history')?></div>
                 <table class="table" ng-if="historial">
                    <thead>
                        <tr>
                            <th>Correo</th>
                            <th>Asignado</th>
                            <th>Removido</th>
                            <th width="2%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="history in historial">
                        
                            <td>
                                {{history.responsable}}<br/>
                                <a href="mailto:{{history.email}}" class="text-muted">{{history.email}}</a>
                            </td>
                            <td>
                               
                                
                                <a title="Descargar comodato" ng-if = "history.removido == null " target="_blank" href="acuse_f/comodato/{{history.id}}" >{{history.asignado}}</a>

                                <span  ng-if = "history.removido" >{{history.asignado}}</span>
                            </td>
                            <td>
                                <a title="Descargar acuse de removido" target="_blank" href="acuse_f/devolucion/{{history.id}}" >{{history.removido}}</a> 
                            </td>
                            <td><i class="zmdi zmdi-collection-text" tooltip-placement="left" uib-tooltip="{{history.observaciones}}" ng-if="history.observaciones" ></i></td>
                        </tr>
                    </tbody>
                 </table>
            </uib-tab>
        </uib-tabset>
                
    </div>
    <div class="modal-footer">
       
                        
        <div class="row" ng-if="!dispose">
            <div class="col-md-3 col-md-offset-4">
            <md-progress-circular md-mode="indeterminate"></md-progress-circular> <br/>Espere por favor....
            </div>
        </div>
        <button type="button" ui-wave class="btn btn-flat" ng-click="cancel()">Cancelar</button>
        <button type="button" ui-wave class="btn btn-flat btn-primary" ng-if="method!='details'" confirm-action ng-click="save()" ng-disabled="!dispose || !valid_form()">Aceptar</button>
    </div>    
     <?php echo form_close(); ?>                       
</script>
<script type="text/ng-template" id="modalReport.html">
    <div class="modal-header" >
        <h3>Generar Reporte</h3>
    </div>
     <?php  echo form_open('','name="report" id="report"');?>
    <div class="modal-body">

        <div ng-bind-html="message" ng-if="message" class="alert alert-danger"></div>

                   <div class="form-group">
                             <label>Estatus</label>
                         <div>
                             <label class="radio-inline"><input type="radio"  ng-model="report.estatus" value="0"/> Disponibles</label>
                             <label class="radio-inline"><input type="radio"  ng-model="report.estatus" value="1"/> Asignados</label>
                         </div>
                   </div>
                      <div class="form-group" ng-if="report.estatus == 0 || report.estatus == 1 ">
                            <label>Organización</label>

                            <select class="form-control" ng-init="report.org = orgs[0]" ng-model="report.org" ng-options="org.name for org in orgs track by org.org_path" required>
                               <option value="" > [ Elegir ] </option>
                            </select>
                      </div>                 
    </div>
    <div class="modal-footer">
       
                        
        <button type="button" ui-wave class="btn btn-flat" ng-click="cancel()">Cancelar</button>
        <button type="button" ui-wave class="btn btn-flat btn-primary" ng-click="save()" ng-disabled="!report.estatus || !report.org ">Aceptar</button>
    </div>    
     <?php echo form_close(); ?>                       
</script>
<script type="text/ng-template" id="modalRemoverCsv.html">
    <div class="modal-header" >
        <h3>Remoción Masiva</h3>
    </div>
     <?php  echo form_open();?>
    <div class="modal-body">

        <div class="alert alert-warning" ng-if="!dispose">Favor de no cerrar esta ventana, hasta terminar con el proceso</div> 
        <div class="alert" ng-class="{'alert-danger':!status,'alert-success':status}"  ng-if="message"> {{message}} </div>               

                      <div class="form-group">
                            <label>Organización</label>
                            <select class="form-control" ng-init="remove.org = orgs[0]" ng-model="remove.org" ng-options="org.name for org in orgs track by org.org_path" required>
                               <option value="" > [ Elegir ] </option>
                            </select>
                      </div> 
                     <div class="form-group" ng-if="remove.org" >
                        <label>Archivo CSV</label>
                        
                                <input type="file"  accept=".csv" ngf-select="upload_file(file_csv,'csv')"  ng-model="file_csv"
                                ngf-max-height="10000" ngf-max-size="80MB"/>
                                <md-progress-linear md-mode="determinate" ng-show="file_csv.progress >= 0" value="{{file_csv.progress}}"></md-progress-linear>
                                   
                     </div>
                     <p  class="extra" ng-if="remove_result.length>0" >Errores : {{remove_result.length}}</p>  
                     <div  class="well"   ng-if="remove_result.length>0" > 
                                 
                          <ul class="list-unstyled list-users-li">
                              <li ng-repeat="remove in remove_result">
                                  <span class="fa fa-check text-success" ng-if="remove.status"></span>
                                  <span class="fa {{remove.icon}} text-danger" ng-if="!remove.status" title="{{remove.message}}"></span>
                                  {{remove.serial}}                                                                              
                              </li>
                          </ul>
                      </div>                               
    </div>

    <div class="modal-footer">
       
                        
        <button type="button" ui-wave class="btn btn-flat" ng-click="cancel()" ng-if="!status">Cancelar</button>
        <button type="button" ui-wave class="btn btn-flat" ng-click="close()" ng-if="status">Aceptar</button>
        
        <!--button type="button" ui-wave class="btn btn-flat btn-primary" ng-disabled="!dispose" ng-click="save()" ">Aceptar</button-->
    </div>    
     <?php echo form_close(); ?>                       
</script>
