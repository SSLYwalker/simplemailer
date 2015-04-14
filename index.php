<html>
    <head>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.4/themes/humanity/jquery-ui.css">	
        <link rel="stylesheet" href="css/jquery.datetimepicker.css">
        <link rel="stylesheet" href="css/jquery-te-1.4.0.css">
        <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
        <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
        <script src="scripts/jquery.datetimepicker.js"></script>
        <script src="scripts/jquery-te-1.4.0.min.js"></script>
        <meta charset="UTF-8">
        <script type="text/javascript">
                /** Text resource-ok*/
                var gresstrarrayp={
                                "titlestr" : "Egyszerű levélküldő",
                                "submitcaption" : "Küldés",
                                "customername" : "Ügyfél neve",
                                "toaddress" : "Címzett",
                                "carboncopy" : "Másolat",
                                "blindcarboncopy" : "Rejtett Másolat",
                                "newrecipient" : "Új címzett",
                                "torles" : "Törlés",
                                "product" : "Termék",
                                "languages": "Nyelvek",
                                "hun": "Magyar",
                                "rom": "Román",
                                "cze": "Cseh",
                                "sky": "Szlovák",
                                "sz7" : "Szerviz7",
                                "mu" : "Modupro ULTIMATE",
                                "m" : "Modupro",
                                "qs" : "Qservice",
                                "start" : "Kezdés",
                                "stop" : "Befejezés",
                                "status": "Státusz",
                                "repair" : "Javítás",
                                "support" : "Segítségnyújtás",
                                "installation" : "Telepítés",
                                "activity" : "Tevékenység",
                                "recipient_delete_dialog" : "Címzett törlése",
                                "yes" : "Igen",
                                "no" : "Nem"
                };
                
                var submitarray = [];
                
                /**Egyedi azonosító generátor*/
                var guid = (function() {
                        function s4() {
                                return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
                        }
                        return function() {
                                return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
                                        s4() + '-' + s4() + s4() + s4();
                        };
                })();

                /*************************************************************
                * 	Recipient holder osztály definició
                */
                function recipientholder(
                        contenthtmltag, 
                        resstrarrayp, /**string tömb a megjelenítendő textekhez*/
                        parent        
                ) 
                {
                        this.contenthtmltag = contenthtmltag;
                        this.resstrarray = resstrarrayp;
                        this.items=new Array();
                        this.parent = parent;
                        this.lastrecipienttype = 'to';
                }
                
                /**
                *  @brief új címzett elemet ad a tárolótömbhöz
                *  
                *  @param type [in] Az email cím típusa.
                *  @param address [in] Az email cím.
                */
                recipientholder.prototype.additem=function(type, address){
                        var id=guid();
                        this.items.push({
                                "id" : id,
                                "type" : type,
                                "address" : address
                        });
                        return id;
                };
                
                /*uuid alapján visszaadja az elem tárolás pozívióját*/
                recipientholder.prototype.getindex=function(array, uuid){
                        for(var i=0; i<array.length; i++){
                            var index = array[i]['id'].indexOf(uuid);
                            if (index > -1){
                                return i;
                            };
                        };
                };
                
                /**
                *  @brief uuid alapján egy címzett elemet eltávolít a tárolótömbből
                *  
                *  @param array [in] A tárolótömb.
                *  @param uuid [in] Az elem egyedi azonosítója.
                */
                recipientholder.prototype.removeitem=function(array, uuid){
                    var index = this.getindex(array, uuid);
                    array.splice(index,1);
                };

                /*a címzett elem tualjdonságait állatja be a tárolótömbben*/
                recipientholder.prototype.setproperty=function(array, type, uuid, value){
                    var index = this.getindex(array, uuid);
                    array[index][type] = value;
                };

                recipientholder.prototype.validate = function() {
                    //alert('validálás');
                    
                    /*dtpicker validate*/
                    var dtpicker = $('#'+this.parent.contenthtmltag+'_status_dtpicker').val();
                    if(dtpicker !== ''){
                        //alert('dtpicker validate' + this.parent.currentdate+' '+this.parent.currenttime);
                    }
                };
                
                /*beállítja mi volt az utolsó címzettípus*/
                recipientholder.prototype.setlastrecipienttype = function() {
                    for(var i=0; i<this.items.length; i++)
                    {
                        if(i === this.items.length-1){
                            this.lastrecipienttype = this.items[i]['type'];
                        }
                    }
                };
                
                /*uuid alapján visszadja, hogy az aktuális elem az utolsó-e*/
                recipientholder.prototype.islastitem = function(uuid) {
                    var retval = false;
                    
                    var array = this.items;
                    var index = this.getindex(array, uuid);
                    if(index === this.items.length-1){
                        retval = true;
                    }
                    return retval;
                };
                
                /*uj cimzett hozzáadása*/
                recipientholder.prototype.newrecipient = function(){
                    var id = this.additem(this.lastrecipienttype,"");
                    this.show();
                    $("div").find("[data-id='" + id + "'] input").focus();
                };
                
                recipientholder.prototype.getdialogsstr = function(){
                    var cont = '';
                    
                    cont+='<div id="'+this.contenthtmltag+'_recipient_delete_dialog" title="'+this.resstrarray['recipient_delete_dialog']+'">';
                    cont+='<p>Valóban törlni szeretné a címzettet?</p>';
                    cont+='</div>';
                    
                    return cont;
                };

                recipientholder.prototype.show=function(){
                        var cont='';
                        cont+='<div id="'+this.contenthtmltag+'_recipientholderdiv" class="recipientholderdiv">';
                        cont+='<button type="button" id="newrecipient" href="#">'+this.resstrarray['newrecipient']+'</button>';
                        cont+=this.getdialogsstr();
                        cont+='</div>';
                        
                         $('#'+this.contenthtmltag).html(cont);
                        // jquery kontrolok inicializálása
                        for(var i=0; i<this.items.length; i++)
                        {
                                if(i===0){
                                    editable = true;
                                    deletable = false;
                                } else {
                                    editable = true;
                                    deletable = true;
                                }
                                var item=this.items[i];
                                $('#'+this.contenthtmltag+'_recipientholderdiv').append(this.getitemstr(item['id'],editable, deletable));
                                $("div").find("[data-id='" + item['id'] + "'] select").val(item['type']);
                                $("div").find("[data-id='" + item['id'] + "'] input").val(item['address']);
                                this.setlastrecipienttype();
                        }
                        
                        /*ESEMÉNYKEZELÉS*/
                        /*jQuery doesn't redefine the this pointer, but that's how JavaScript functions work in general. Store a reference to the this pointer under a different name, and use that.*/
                        var thispoi = this;
                        $(".emailinput").css("width", 400);
                        /*Új címzett hozzáadása*/
                        $("#newrecipient").click(function(){
                            thispoi.newrecipient();
                        });
                        
                        
                        
                        /*enter és nem üres inputfield és az utolsó emailinputon álltesetén új címzett*/
                        $(".emailinput").keypress(function(){
                            /*email input ertekkiaras az items tombbe*/
                            var type = 'address';
                            var uuid = $(this).parent().data("id");
                            var value = $(this).val();
                            thispoi.setproperty(thispoi.items, type, uuid, value);
                            /*ENTER nyomása és nem üres input esetén új címzett*/
                            var inputvalue = $(this).val();
                            var uuid = $(this).parent().data("id");
                            var lastitem = thispoi.islastitem(uuid);
                            
                            if(event.which === 13 && inputvalue !== '' && lastitem === true){
                                thispoi.newrecipient();
                            }
                        });
                        /*Az első elem mindenképpen címzett és nem lehettörölni*/
                        //$('#'+this.contenthtmltag+'_recipientholderdiv div:first select:first').attr("disabled","disabled");
                        //$('#'+this.contenthtmltag+'_recipientholderdiv div:first button:first').attr("disabled","disabled");
                        
                        
                        
                        $(".noneditable").attr("disabled","disabled");
                        
                        $(".dovalidate").keyup(function(){
                            thispoi.validate();
                        });
                        $(".dovalidate").click(function(){
                            thispoi.validate();
                        });
                        $(".dovalidate").change(function(){
                            thispoi.validate();
                        });
                        
                        /*select onchange ertekkiaras az items tombbe*/
                        $(".recipienttype").change(function(){
                            var type = 'type';
                            var uuid = $(this).parent().data("id");
                            var value = $(this).val();
                            thispoi.setproperty(thispoi.items, type, uuid, value);
                            thispoi.setlastrecipienttype();
                        });

                        /*fv*/
                        /*jQuery.fn.myvalidate = function() {
                            var uid = $(this).parent().data("id");
                            alert(uid);
                            if($(this).val() !== '')
                            {
                                $('#' + uid + 'emailinput_info').css("color","red");
                                $('#'+thispoi.parent.contenthtmltag+'_submitbutton').removeAttr("disabled");
                            } 
                            else 
                            {   
                                alert('#'+thispoi.parent.contenthtmltag+'_submitbutton');
                                $('#'+thispoi.parent.contenthtmltag+'_submitbutton').attr("disabled","disabled");
                            }
                        };*/

                        
                        $(".emailinput").change(function(){
                            thispoi.validate();
                        });
                        
                        /**Címzett eltávolítása dialógus*/
                        $(function() {
                        $('#'+thispoi.contenthtmltag+'_recipient_delete_dialog').dialog({
                                    autoOpen: false,
                                    modal: true,
                                    resizable: false,
                                    dialogClass: "recipient_delete_dialog",
                                    /*megnyitáskor az igen gombra kerül a fókusz és ebben az esetben
                                     *  a BACKSPACE megnyomását védeni kell mert másképp a böngésző betölti az előző oldalt*/
                                    open: function(){ 
                                            $(".recipient_delete_dialog").find("button").on("keydown", function(){
                                                if(event.which === 8){
                                                    event.preventDefault();
                                                }
                                            });
                                        },
                                    buttons: [
                                        {
                                        text: thispoi.resstrarray['yes'],
                                        click: function() {
                                                    var target = $(".markedfordelete");
                                                    var uuid = target.data("id");
                                                    thispoi.removeitem(thispoi.items, uuid);
                                                    thispoi.setlastrecipienttype();
                                                    target.remove();
                                                    $(this).dialog("close");
                                                    $(".emailinput:last").focus();
                                                }
                                        },
                                        {
                                        text: thispoi.resstrarray['no'],
                                        click: function() {
                                                    $(".markedfordelete").removeClass("markedfordelete");
                                                    $(this).dialog("close");
                                                }
                                            }
                                    ]
			});
                        
                       // $("body").on('keydown', function(){event.preventDefault();});
                        
			$("button.deleterecipient").on("click", function() {
                                $(this).parent().addClass("markedfordelete");
                                $(".emailinput").focus();
				$('#'+thispoi.contenthtmltag+'_recipient_delete_dialog').dialog("open");
                                
			});
                        
                        /*üres input BACKSPACE nyomása estén törlődik*/
                        $(".emailinput").on('keydown', function() {
                        var key = event.keyCode || event.charCode;
                        var inputvalue = $(this).val();
                        
                        if( key === 8 || key === 46 ){
                            if(inputvalue === ''&& $(this).parent().data("deletable") === true){
                                /*kivédi, hogy a BACKSPACE hatására visszanavigáljon az előző oldalra*/
                                event.preventDefault();
                                $(this).parent().addClass("markedfordelete");
				$('#'+thispoi.contenthtmltag+'_recipient_delete_dialog').dialog("open");
                                
                            }
                        }
                        });
                        
                        });
                        /*ESEMÉNYKEZELÉSVÉGE*/
                        
                        
                        $(".emailinput:last").focus();
                        $("#newrecipient").button();
                        $(".deleterecipient").button({
                            icons: {primary: 'ui-icon-trash'}
                        });
                        //$("select").selectmenu();
                };

                recipientholder.prototype.getitembyid=function(id){
                        var retitem=null;
                        for(var i=0; i<this.items.length; i++)
                        {
                                var item=this.items[i];
                                if(item['id'] === id)
                                {
                                        retitem=item;
                                        break;
                                }
                        }	
                        return retitem;
                };

                recipientholder.prototype.getitemstr = function(id,editablep,deletablep)
                {
                        var cont='';
                        var editable='';
                        if(editablep !== true ){
                            editable = 'noneditable';
                        };
                        
                        var item=this.getitembyid(id);
                        if(item !== null)
                        {
                                cont+='<div class="recipientitem" data-id="'+id+'" data-deletable="'+deletable+'">';
                                cont+='<select class="recipienttype dovalidate '+editable+' ui-widget ui-widget-input ui-corner-all">';
                                cont+='<option value="to">'+this.resstrarray['toaddress']+'</option>';
                                cont+='<option value="cc">'+this.resstrarray['carboncopy']+'</option>';
                                cont+='<option value="bcc">'+this.resstrarray['blindcarboncopy']+'</option>';
                                cont+='</select>';
                                cont+='<input type="email" class="emailinput dovalidate ui-widget ui-widget-input ui-corner-all"/>';
                                if(deletablep === true){
                                    cont+='<button class="deleterecipient" type="button" href="#">'+this.resstrarray['torles']+'</button>';
                                }
                                cont+='<div id="'+id+'emailinput_info">';
                                cont+='teszt';
                                cont+='</div>';
                                //cont+=item['id']+','+item['type']+','+item['address']; 
                                cont+='</div>';
                        }
                        return cont;
                };

                ///////////////////////////////////////////////////////////////////////////////////////////////
                // Mailer user interface osztály definició
                function maileruserinterface(
                        contenthtmltag, 
                        resstrarrayp /*string tömb a megjelenítendő textekhez*/
                ) 
                {
                        this.contenthtmltag = contenthtmltag;
                        this.resstrarray = resstrarrayp;
                        this.items = new Array({
                            "customername" : 'üres',
                            "lang" : 'hun',
                            "products" : 'sz7',
                            "activity" : 'repair',
                            "status" : 'start',
                            "message": ''
                        });
                        this.recipholder =new recipientholder(this.contenthtmltag+'_recipientholder', resstrarrayp, this);
                        this.recipholder.additem("to", "oze.peter@3szs.hu"); 
                        this.recipholder.additem("cc", "csernyey.krisztian@3szs.hu"); 
                        this.recipholder.additem("bcc", "oze.peter@gmail.com"); 				
                        this.dateformat='Y.m.d.';
                        this.timeformat='H:i';
                        this.currentdate='';
                        this.currenttime='';
                }
                
                maileruserinterface.prototype.validate = function() {
                    alert('maileruserinterface validálás');
                    
                    /*Name validate*/
                    var customername = $('#'+this.parent.contenthtmltag+'_customername').val();
                    if(customername === ''){
                        alert('üres ügyfélnév');
                    }
                };

                maileruserinterface.prototype.show = function() {
                        var cont='';
                        /*osztály pointer deklarálása*/
                        var thispoi = this;
                        /*recipholder pointer létrehozása*/
                        var rhpoi = thispoi.recipholder;
                        
                        
                        cont=thispoi.getformstr();
                         $('#'+thispoi.contenthtmltag).html(cont);
                        thispoi.settitle(thispoi.resstrarray['titlestr']);
                        // jquery ui-s kontrolok inicializálása
                        thispoi.initjquerycontrols();
                        thispoi.recipholder.show();
                        
                        $('#'+thispoi.contenthtmltag+'_customername').on("input", function(){
                            thispoi.items[0]["customername"] = $(this).val();
                        });
                        
                        $('#'+thispoi.contenthtmltag+'_lang').on("input", function(){
                            thispoi.items[0]["lang"] = $(this).val();
                            $(".jqte_editor").load('./templates/'+$(this).val()+'_template');
                            thispoi.items[0]["message"] = $(".jqte_editor").html();
                        });
                        
                        $('#'+thispoi.contenthtmltag+'_products').on("input", function(){
                            thispoi.items[0]["products"] = $(this).val();
                        });
                        
                        $('#'+thispoi.contenthtmltag+'_activity').on("input", function(){
                            thispoi.items[0]["activity"] = $(this).val();
                        });
                        
                        $('#'+thispoi.contenthtmltag+'_status').on("input", function(){
                            thispoi.items[0]["status"] = $(this).val();
                        });
                        
                        $('#'+thispoi.contenthtmltag+'_status_dtpicker').on("change", function(){
                            thispoi.items[0]["datetime"] = $(this).val();
                        });
                        
                        $(".jqte_editor").on("input", function(){
                            thispoi.items[0]["message"] = $(this).html();
                        });
                        
                         $('#'+thispoi.contenthtmltag+'_submitbutton').on("click", function() {
                                submitarray.push(rhpoi["items"]);
                                submitarray.push(thispoi["items"]);
                                var myJson = JSON.stringify(submitarray);
                                $("#"+thispoi.contenthtmltag+"_hidden").val(myJson);
                                $("#"+thispoi.contenthtmltag+'_form').submit();
                                /*$.ajax({
                                    "async" : false
                                });
                                $.post('/mailkuldo/mailsender.php', {"tomb" : submitarray});*/
                                /*var s = '';                                
                                for (row in submitarray[1]){
                                    s+= row + "\n";
                                    for (v in submitarray[1][row]){
                                        s+= v + " - " + submitarray[1][row][v] + "\n";
                                    }
                                    
                                }
                                alert(s);
                                alert(submitarray);*/
                            });
                        
                };

                maileruserinterface.prototype.initjquerycontrols = function(){
                        var thispoi=this;
                        $('#'+thispoi.contenthtmltag+'_submitbutton').button({
                                icons : {primary : 'ui-icon-check'}
                        });
                        
                        var format=thispoi.dateformat+' '+thispoi.timeformat;

                        /*Átmeneti formázás*/
                        $("label").css("display", "block");
                        $("select").css("width", 200);
                        $('#'+thispoi.contenthtmltag+'_status_dtpicker').datetimepicker({
                            format:format,
                            lang:'hu',
                            startDate: '2014.01.01',
                            mask:true, // '9999/19/39 29:59' - digit is the maximum possible for a cell
                            step: 5,
                            dayOfWeekStart: 1,
                            onSelectDate:function(current_time,$input){
                                thispoi.currentdate=current_time.dateFormat(thispoi.dateformat);
                            },
                            onSelectTime:function(current_time,$input){
                                //thispoi.currentdate=current_time.dateFormat(thispoi.dateformat);
                                thispoi.currenttime=current_time.dateFormat(thispoi.timeformat);
                            }
                            
                        });
                        $(".editor").jqte();
                        $(".jqte_editor").load('./templates/hun_template', function( response, status, xhr ) {
                            if ( status !== "error" ) {
                             thispoi.items[0]["message"] = $(".jqte_editor").html();
                            }
                        });
                        //$('#'+recipientholder.contenthtmltag+'_recipientholderdiv div:first button:first').focus();
                        
                        //$("#"+this.contenthtmltag+"_langdiv").on("change", function(){$(".editor").jqteVal("New article!");});

                };

                maileruserinterface.prototype.settitle = function(title) {
                        var cont='';

                         $('#htmltitle').remove();
                        cont+='<title id="htmltitle">';
                        cont+=title;
                        cont+='</title>';
                         $('html head').append(cont);
                };

                maileruserinterface.prototype.getformstr = function() {
                        var cont='';

                        cont+='<form id="'+this.contenthtmltag+'_form" method="POST" action="./mailsender.php" class="ui-widget-content ui-corner-all">';
                        cont+=this.getcustomernamedivstr();
                        cont+=this.getlangdivstr();
                        cont+=this.getproductsdivstr();
                        cont+=this.getactivitydivstr();
                        cont+=this.getstatusdivstr();
                        cont+='<div id="'+this.contenthtmltag+'_recipientholder">';
                        cont+='</div>';
                        cont+=this.geteditordivstr();
                        cont+=this.getbuttonsstr('button', this.resstrarray['submitcaption']);		
                        cont+='</form>';				

                        return cont;
                };

                maileruserinterface.prototype.getbuttonsstr = function(type, caption){
                        var cont='';

                        cont+='<div id="'+this.contenthtmltag+'_buttondiv" >';
                        cont+='<input id="'+this.contenthtmltag+'_submitbutton" type="'+type+'" value="'+caption+'">';
                        cont+='<input type="hidden" name="jsonize" id="'+this.contenthtmltag+'_hidden" value="">';
                        cont+='</div>';

                        return cont;
                };

                maileruserinterface.prototype.getcustomernamedivstr = function (){
                        var cont='';

                        cont+='<div id="'+this.contenthtmltag+'_customernamediv">';
                        cont+='<label class="ui-widget" for="'+this.contenthtmltag+'_customername">'+this.resstrarray['customername']+'</label>';
                        cont+='<input id="'+this.contenthtmltag+'_customername" name="'+this.contenthtmltag+'_customernamep" class="customername dovalidate ui-widget ui-widget-input ui-corner-all"/>';
                        cont+='</div>';
                        return cont;
                };

                maileruserinterface.prototype.getproductsdivstr = function (){
                        var cont='';

                        cont+='<div id="'+this.contenthtmltag+'_productdiv">';
                        cont+='<label class="ui-widget" for="'+this.contenthtmltag+'_products">'+this.resstrarray['product']+'</label>';
                        cont+='<select id="'+this.contenthtmltag+'_products" class="product ui-widget ui-widget-input ui-corner-all">';
                        cont+='<option value="sz7">'+this.resstrarray['sz7']+'</option>';
                        cont+='<option value="mu">'+this.resstrarray['mu']+'</option>';
                        cont+='<option value="m">'+this.resstrarray['m']+'</option>';
                        cont+='<option value="qs">'+this.resstrarray['qs']+'</option>';
                        cont+='</select>';
                        cont+='</div>';
                        return cont;
                };

                maileruserinterface.prototype.getlangdivstr = function (){
                        var cont='';

                        cont+='<div id="'+this.contenthtmltag+'_langdiv">';
                        cont+='<label class="ui-widget" for="'+this.contenthtmltag+'_languages">'+this.resstrarray['languages']+'</label>';
                        cont+='<select id="'+this.contenthtmltag+'_lang" class="product ui-widget ui-widget-input ui-corner-all">';
                        cont+='<option value="hun">'+this.resstrarray['hun']+'</option>';
                        cont+='<option value="rom">'+this.resstrarray['rom']+'</option>';
                        cont+='<option value="cze">'+this.resstrarray['cze']+'</option>';
                        cont+='<option value="sky">'+this.resstrarray['sky']+'</option>';
                        cont+='</select>';
                        cont+='</div>';
                        return cont;
                };

                maileruserinterface.prototype.getactivitydivstr = function (){
                        var cont='';

                        cont+='<div id="'+this.contenthtmltag+'_activitydiv">';
                        cont+='<label class="ui-widget" for="'+this.contenthtmltag+'_activity">'+this.resstrarray['activity']+'</label>';
                        cont+='<select id="'+this.contenthtmltag+'_activity" class="activitytype ui-widget ui-widget-input ui-corner-all">';
                        cont+='<option value="repair">'+this.resstrarray['repair']+'</option>';
                        cont+='<option value="support">'+this.resstrarray['support']+'</option>';
                        cont+='<option value="installation">'+this.resstrarray['installation']+'</option>';
                        cont+='</select>';
                        cont+='</div>';
                        return cont;
                };

                maileruserinterface.prototype.getstatusdivstr = function (){
                        var cont='';

                        cont+='<div id="'+this.contenthtmltag+'_statusdiv">';
                        cont+='<label class="ui-widget" for="'+this.contenthtmltag+'_status">'+this.resstrarray['status']+'</label>';
                        cont+='<select id="'+this.contenthtmltag+'_status" class="statustype ui-widget ui-widget-input ui-corner-all">';
                        cont+='<option value="start">'+this.resstrarray['start']+'</option>';
                        cont+='<option value="stop">'+this.resstrarray['stop']+'</option>';
                        cont+='</select>';
                        cont+='<input id="'+this.contenthtmltag+'_status_dtpicker" class="dtpicker dovalidate ui-widget ui-widget-input ui-corner-all"/>';
                        cont+='</div>';
                        return cont;
                };
                
                maileruserinterface.prototype.geteditordivstr = function (){
                        var cont='';

                        cont+='<div id="'+this.contenthtmltag+'_editordiv">';
                        cont+='<textarea class="editor" id="'+this.contenthtmltag+'_editor">szerkesztő</textarea>';
                        cont+='</div>';
                        return cont;
                };
                
                        $(document).ready(function(){
                        var mui=new maileruserinterface('content', gresstrarrayp);
                        //var mui1=new maileruserinterface('content1', gresstrarrayp);				
                        mui.show();
                        mui.settitle(guid());
                        //mui1.show();		
                });
        </script>
    </head>
    <body>
        <div id="content">
        </div>
    </body>
</html>