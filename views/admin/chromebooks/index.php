<section ng-controller="IndexCtrlAsig">
    <?php if(group_has_role('chromebooks','create')): ?>
        <a href="#"  ng-click="report_dg()" uib-tooltip="Reporte" class="btn btn-success pull-right">Reporte</a>  
        <a href="#"  ng-click="newChrome()" uib-tooltip="Nueva Chromebook" class="btn btn-primary pull-right">Nuevo</a>
    <?php endif;?>
    <?php if(!group_has_role('chromebooks','admin_chrome') && !$chromebooks): ?>
       <div class="alert alert-info text-center"><?=sprintf(lang('chromebook:not_asigned_chrome'))?></div>
    <?php else:?>
        <div class="row col-md-12">
            <h4 class="text-success">Buscar</h4>
            <input type="text" class="form-control" ng-model="txt_disponibles" />
            <hr />
            <p class="text-right">Total registros: {{(chromebooks).length}}</p>
            <table class="table">
                <thead>
                    <tr>
                        <th>SERIAL</th>
                        <th>Propietario</th>    
                        <th>Co-propietario</th>
                        <?php if(group_has_role('chromebooks','admin_chrome')): ?>
                            <th width="20%"></th>
                        <?php endif;?>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="chrome in chromebooks | filter:txt_disponibles|limitTo:20" ng-class="{'danger':chrome.estatus == 'baja','warning': chrome.estatus == 'reparacion','active': chrome.estatus == 'extraviado'}" >
                        <td>{{chrome.id}}<br />
                            <span ng-if="chrome.email == null && chrome.estatus == 'disponible'" class="text-muted">Disponible</span>
                            <span ng-if="chrome.email == null && chrome.estatus == 'baja'" class="text-muted">Baja</span>
                            <span ng-if="chrome.email == null && chrome.estatus == 'reparacion'" class="text-muted">Reparación</span>
                            <span ng-if="chrome.email == null && chrome.estatus == 'extraviado'" class="text-muted">Extraviado</span>
                            <span ng-if="chrome.email != null" class="text-muted">Asignado</span>
                        </td>
                        <td>{{chrome.email}}</td>
                        <td>{{chrome.org_path}}</td>
                        <?php if(group_has_role('chromebooks','admin_chrome')): ?>
                          <td ng-if="chrome.org_path == null "><button ng-disabled="chrome.estatus != 'disponible' || chrome.email != null" uib-tooltip="Asignar Chromebook a Org" href="#" ng-click="asignar(chrome)" class="btn btn-default btn-success ui-wave "><i class="fa fa-plus-square" aria-hidden="true"></i> </button> <button ng-disabled="chrome.email != null || chrome.org_path != '/Dirección General'"  uib-tooltip="Configurar Estatus" href="#" ng-click="config(chrome)" class="btn btn-default btn-primary ui-wave" ><i class="fa fa-cogs" aria-hidden="true"></i></button>
                            <button ng-disabled="chrome.email == null || chrome.org_path == null "  uib-tooltip="Eliminar Chromebook" href="#" ng-click="delete(chrome)" class="btn btn-default btn-danger ui-wave" ><i class="fa  fa-trash" aria-hidden="true"></i></button>
</td>
                          <td ng-if="chrome.org_path != null"><button ng-disabled="chrome.estatus != 'disponible' || chrome.email != null"  uib-tooltip="Remover Chromebook a Org" href="#" ng-click="remover(chrome)" class="btn btn-default btn-danger ui-wave "><i class="fa fa-minus-square" aria-hidden="true"></i></button> <button  ng-disabled="chrome.email != null || chrome.org_path != '/Dirección General'" uib-tooltip="Configurar Estatus"  href="#" ng-click="config(chrome)" class="btn btn-default btn-primary ui-wave"><i class="fa fa-cogs" aria-hidden="true"></i></button>
                            <button ng-disabled="chrome.email == null || chrome.org_path == null "  uib-tooltip="Eliminar Chromebook" href="#" ng-click="delete(chrome)" class="btn btn-default btn-danger ui-wave" ><i class="fa  fa-trash" aria-hidden="true"></i></button></td>
                        <?php endif;?>
                    </tr>
                </tbody>
            </table>
    </div>
    <?php endif;?>
</section>
<script type="text/ng-template" id="modalFormAsig.html">
    <div class="modal-header" >
        <h3>Asignar/Remover</h3>
    </div>
          <?php  echo form_open('','name="frm" id="frm"');?>

    <div class="modal-body">
                          <div ng-if="form.email" class="alert alert-warning" ><?=lang('chromebook:not_change')?></div>

                    <div ng-bind-html="message" ng-if="message" class="alert alert-danger" "></div>
                    <div class="form-group">
                            <label>No. serial</label>
                            <input type="text" class="form-control" ng-model="form.id" disabled/>
                     </div>   
                      <div class="form-group" ng-if="method=='create'" >
                            <label>Organización</label>
                            <select class="form-control" name="org" ng-model="form.org"  ng-options="org.name for org in orgs track by org.org_path" required>
                                <option value=""> [ Elegir ] </option>
                            </select>
                     </div> 
                      <div class="form-group" ng-if="method=='edit'" >
                            <label></label>
                            <input class="form-control" name="org" ng-model="form.org" disabled/>
                     </div> 

                
    </div>
    <div class="modal-footer">
        <button type="button" ui-wave class="btn btn-flat" ng-click="cancel()">Cancelar</button>
        <button type="button" ui-wave class="btn btn-flat btn-primary" ng-disabled="!form.org" ng-click="save()" ng-if="method=='create'">Aceptar</button>
        <button type="button" ui-wave class="btn btn-flat btn-primary" ng-disabled="!form.org || form.email != null" ng-click="remove()" ng-if="method=='edit'">Remover</button>

    </div>    
     <?php echo form_close(); ?>                       
</script>

<script type="text/ng-template" id="modalAdd.html">
    <div class="modal-header" >
        <h3>Agregar Chromebook</h3>
    </div>
     <?php  echo form_open('','name="frm_add" id="frm_add"');?>
    <div class="modal-body">
        <div ng-bind-html="message" ng-if="message" class="alert alert-danger" "></div>                    
                    <div class="form-group">
                            <label>No. serial</label>
                            <input type="text" class="form-control" ng-model="frm_add.serie"/>
                     </div>   
                      <div class="form-group">
                            <label>Organización</label>
                            <select class="form-control" name="org" ng-readonly="frm_add.id" ng-model="frm_add.org" ng-options="org.name for org in orgs track by org.org_path" required>
                                <option value=""> [ Elegir ] </option>
                            </select>
                            <div ng-messages="frm_add.org.$error"  role="alert" ng-if="frm_add.org.$dirty">
                                    <div class="text-danger" ng-message="required">Este campo es requerido</div>
                             </div>
                     </div> 
                    
                      

                
    </div>
    <div class="modal-footer">
       
                        
        <button type="button" ui-wave class="btn btn-flat" ng-click="cancel()">Cancelar</button>
        <button type="button" ui-wave class="btn btn-flat btn-primary" ng-if="frm_add.serie"  ng-click="save()" ">Aceptar</button>
    </div>    
     <?php echo form_close(); ?>                       
</script>

<script type="text/ng-template" id="modalStatus.html">
    <div class="modal-header" >
        <h3>Cambiar Estado</h3>
    </div>
     <?php  echo form_open('','name="frm_status" id="frm_status"');?>
    <div class="modal-body">
        <div ng-if="form_status.email" class="alert alert-warning" ><?=lang('chromebook:not_change')?></div>
        <div ng-if="form_status.org_path != '/Dirección General'" class="alert alert-warning" ><?=lang('chromebook:not_change_org')?></div>                    

        <div ng-bind-html="message" ng-if="message" class="alert alert-danger" "></div>                    
                    <div class="form-group">
                            <label>No. serial</label>
                            <input type="text" class="form-control" ng-readonly="form_status.id" ng-model="form_status.id"/>
                     </div>   
                      <div class="form-group">
                            <label>Estatus</label>
                            <select class="form-control" name="chrome_status" ng-disabled="form_status.email || form_status.org_path != '/Dirección General'" ng-model="form_status.estatus" required>
                                <option value="disponible"> Disponible </option>
                                <option value="reparacion"> Reparación </option>
                                <option value="extraviado"> Extraviado </option>
                                <option value="baja"> Baja </option>
                                
                            </select>
   
                     </div> 
                     <div class="form-group" ng-if="form_status.estatus != 'disponible'" >
                            <label>Observaciones</label>
                            <textarea  class="form-control" ng-model="form_status.observaciones" required ></textarea>
                     </div>

    </div>
    <div class="modal-footer">
       
        <button type="button" ui-wave class="btn btn-flat" ng-click="cancel()">Cancelar</button>
        <button type="button" ui-wave class="btn btn-flat btn-primary" ng-if="!form_status.email && form_status.org_path == '/Dirección General'" ng-disabled="!valid_form()" ng-click="save()" ">Aceptar</button>
    </div>    
     <?php echo form_close(); ?>                       
</script>

<script type="text/ng-template" id="modalReportDg.html">
    <div class="modal-header" >
        <h3>Generar Reporte</h3>
    </div>
     <?php  echo form_open('','name="frm_report" id="frm_report"');?>
    <div class="modal-body">

        <div ng-bind-html="message" ng-if="message" class="alert alert-danger"></div>

                    <div class="form-group">
                             <label>Estatus</label>
                         <div>
                             <label class="radio-inline"><input type="radio"  ng-model="form_status.estatus" value="disponible"/> Disponible</label>
                             <label class="radio-inline"><input type="radio"  ng-model="form_status.estatus" value="reparacion"/> Reparación</label>
                             <label class="radio-inline"><input type="radio"  ng-model="form_status.estatus" value="extraviado"/> Extraviado</label>
                             <label class="radio-inline"><input type="radio"  ng-model="form_status.estatus" value="baja"/> Baja</label>
                             <label class="radio-inline"><input type="radio"  ng-model="form_status.estatus" value="general"/> General</label>
                         </div>
                   </div>               
    </div>
    <div class="modal-footer">
       
                        
        <button type="button" ui-wave class="btn btn-flat" ng-click="cancel()">Cancelar</button>
        <button type="button" ui-wave class="btn btn-flat btn-primary" ng-click="save()" ng-disabled="!form_status.estatus">Aceptar</button>
    </div>    
     <?php echo form_close(); ?>                         
</script>