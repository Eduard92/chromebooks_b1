<page backtop="30mm" backbottom="10mm" backleft="20mm" backright="20mm">
    <page_header>
        <table style="width: 100%;">
            <tr>
                <td style="width: 100%;text-align:center;">
                    {{ asset:image file="pdf/cintillo_header.png" style="width:100%;" }}
                    
                </td>
                                
            </tr>
            
        </table>
    </page_header>
    
    <p style="text-align: right;"><strong>{{plantel}}</strong></p>



    <p style="text-align: right;">{{fecha}}</p>


    <p><strong>A quien corresponda.</strong></p>


    <br />
    <br />
    
    <p style="text-align: justify; font-size: 14px; line-height: 20px;">
        Por medio de la presente le notificamos que el Alumno(a) <strong>{{alumno}}</strong> con Matrícula <strong>{{matricula}}</strong> devolvió la computadora HP Chromebook asignada a su persona para el desarrollo de sus actividades escolares, por lo que se ha liberado de su Resguardo Personal quedando el equipo de cómputo bajo la custodia del Responsable del Centro de Cómputo del Centro Educativo.
    </p>

    <br />
    <br />
   <table width="100%" >
        <thead>
            <tr>     
                <th width="150"></th>          
                <th width="250" align="center" style="background-color: #C5BFBF; padding: 3px;">Serie de Chromebook</th>
            </tr>
        </thead>
        <tbody>
            <tr>             
                <td width="150"></td>            
                <td style="padding: 4px;border: #a6ce39 2px solid; vertical-align: center; text-align: center;"><strong>{{serial}}</strong></td>              
            </tr>
        </tbody>
    </table>

    <br />
    <br />

    <p style="text-align: justify; font-size: 14px; line-height: 20px;">
    </p>
   <br />


    {{ if  observaciones != ""  }}
        <p>Observaciones.</p>
        <p>{{observaciones}}</p>
       
    {{ endif }}
     <br />
    <br /><br />
    <br /><br /><br />
    <br />
    <br />

    <table width="100%" >
            <tr>             
                <td width="200" style="border-top: #00000 1px solid; padding: 4px;vertical-align: center; text-align: center;">Entrega el Alumno(a)</td>
                <td width="150"></td> 
                <td width="200" style="border-top: #00000 1px solid; padding: 4px;vertical-align: center; text-align: center;">Recibe</td>
               
            </tr>
            <tr>             
                <td width="200" style="padding: 4px;vertical-align: center; text-align: center;"><strong>{{alumno}}</strong></td>
                <td width="150"></td> 
                <td width="200" style="padding: 4px;vertical-align: center; text-align: center;">Responsable del Centro de Cómputo</td>
               
            </tr>
    </table>

    <page_footer>
       <p style="text-align: left;">c.c.p.: Archivo</p>


        <table style="width: 100%;">
            <tr>
                <td style="width: 100%;text-align:left;">
                    {{ asset:image file="pdf/cintillo_footer.png" style="width:100%;" }}
                    
                </td>
                
                
            </tr>
            
        </table>
    </page_footer>
   


 </page>