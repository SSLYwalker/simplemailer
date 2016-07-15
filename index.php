<html>
    <head>
        <link rel="icon" href="images/sm_ico.png">
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.4/themes/humanity/jquery-ui.css">	
        <link rel="stylesheet" href="css/jquery.datetimepicker.css">
        <link rel="stylesheet" href="css/jquery-te-1.4.0.css">
	<link rel="stylesheet" href="css/base.css">
        <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
        <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
        <script src="scripts/jquery.datetimepicker.js"></script>
        <script src="scripts/jquery-te-1.4.0.min.js"></script>
        <meta charset="UTF-8">
        <script type="text/javascript">
                /** Text resource-ok*/
		/**
                 * @brief Az oldal megjelnítési nyelvénk a kódja
                 * 
                 * @type String
                 */
                var g_lang = 'hun';
                
                
        	var mui;
                /**
                 * @brief Ez a nyevi objektum tartalmazza a címkék nyelvi 
                 * megfelelőit
                 * 
                 * @type Object
                 */                
                var g_string_resource_obj={
					"hun" : {
                                                    "titleStr" : "Egyszerű levélküldő",
                                                    "submitCaption" : "Küldés",
                                                    "customerName" : "Ügyfél neve",
                                                    "toAddress" : "Címzett",
                                                    "carbonCopy" : "Másolat",
                                                    "blindCarbonCopy" : "Rejtett Másolat",
                                                    "newRecipient" : "Új címzett",
                                                    "delete" : "Törlés",
                                                    "product" : "Termék",
                                                    "msgLanguages": "Üzenet nyelve",
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
                                                    "recipient_delete_dialog" : "Valóban törölni szeretné a címzettet?",
                                                    "recipient_delete_dialog_title" : "Címzett törlése",
                                                    "yes" : "Igen",
                                                    "no" : "Nem"
                                                },
					"eng" : {
                                                    "titleStr" : "Simple mailer",
                                                    "submitCaption" : "Send",
                                                    "customerName" : "Customer name",
                                                    "toAddress" : "To",
                                                    "carbonCopy" : "CC",
                                                    "blindCarbonCopy" : "BCC",
                                                    "newRecipient" : "New Recipient",
                                                    "delete" : "Delete",
                                                    "product" : "Product",
                                                    "msgLanguages": "Message Language",
                                                    "hun": "Hungarian",
                                                    "rom": "Romanian",
                                                    "cze": "Czech",
                                                    "sky": "Slovak",
                                                    "sz7" : "Szerviz7",
                                                    "mu" : "Modupro ULTIMATE",
                                                    "m" : "Modupro",
                                                    "qs" : "Qservice",
                                                    "start" : "Beginning",
                                                    "stop" : "Completion",
                                                    "status": "Status",
                                                    "repair" : "Repair",
                                                    "support" : "Support",
                                                    "installation" : "Installation",
                                                    "activity" : "Activity",
                                                    "recipient_delete_dialog" : "Do you want to delete the recipient?",
                                                    "recipient_delete_dialog_title" : "Delete recipient",
                                                    "yes" : "Yes",
                                                    "no" : "No"
                                                }
                };
                
                
                
               
                /**
                 * @brief Az adatok ebben a tömmben kerülnek átadásra a feldolgozónak
                 * 
                 * @type Array
                 */
                var submitarray = [];
				
                /**
                 * @brief Egyedi azonosító generátor
                 * 
                 * @type Function
                 */
                var guid = (function() {
                        function s4() {
                                return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
                        }
                        return function() {
                                return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
                                        s4() + '-' + s4() + s4() + s4();
                        };
                })();

                
               /**************************************************************
                * @brief Recipient holder osztály definició
                * 
                * @param {type} contenthtmltag
                * @param {type} string_resource_obj
                * @param {type} parent
                * @returns {recipientholder}
                */
                function recipientholder(
                        contenthtmltag, 
                        string_resource_obj, /**string tömb a megjelenítendő textekhez*/
                        parent        
                ) 
                {
                        this.contenthtmltag = contenthtmltag;
                        this.str_res_obj = string_resource_obj;
                        /**
                         * @brief A cymzetteket tároló tömb id, típus, email cím
                         */
                        this.recipient_arr=new Array();
                        this.parent = parent;
                        this.lastrecipienttype = 'to';
                        this.url;
                        this.ajaxData = {};
                }

                /**
                 * @brief Email input változáa esetén, az a fv ajaxos hívással 
                 * megjeleíti az az alternatívákat
                 * 
                 * @param {type} event
                 * 
                 * @returns Nothing
                 */
                recipientholder.prototype.liveSearch = function(event) {
                  var thispoi = this;
                  var liveSearchDivParentId = $(event.target).parent().data('id');
                  var liveSearchDiv = $('#' + liveSearchDivParentId + '_emailinput_livesearch');
                  if (event.target.value.length===0) {
                        thispoi.clearLiveSearchDiv(liveSearchDiv);
                        return;
                  }
                  
                  this.liveSearchAjaxSettings(liveSearchDivParentId);
             
                /*    
                  xmlhttp.onreadystatechange=function() {
                        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
                          document.getElementById("livesearch").innerHTML=xmlhttp.responseText;
                          document.getElementById("livesearch").style.border="1px solid #A5ACB2";
                        }
                  }
                  xmlhttp.open("GET","livesearch.php?q="+str,true);
                  xmlhttp.send();*/
                };
                
                /*jquery elemként jön a paraméter*/
                recipientholder.prototype.clearLiveSearchDiv = function(liveSearchDiv){
                    liveSearchDiv.html('');
                    liveSearchDiv.css('border', '0px');
                }
                
                recipientholder.prototype.liveSearchResultItemOnClick = function(parentId, emailAddress){
                    var thispoi = this;
                    alert('hopp');
                    var emailInput = $('#' + $parentId +  '_emailinput');
                    console.log(emailInput);
                    emailInput.val('\'' + $emailAddress + '\'').focus(); 
                    this.clearLiveSearchDiv();
                };
                
                recipientholder.prototype.liveSearchAjaxSettings = function(id){
                    var thispoi = this;
                    thispoi.ajaxData = {
                        "elementId" : id,
                        "action" : "liveSearch",
                        "pattern": $('#' + id + '_emailinput').val()
                    };
                    thispoi.url = '/simplemailer/livesearch.php';
                    
                    thispoi.callAjax();
                };
                
                /**
                 * A liveSearch eredményét megjelenítő fv
                 * 
                 * @see recipientholder.prototype.liveSearch
                 * 
                 * @param [in] response Az ajax hívás response-ja JSON
                 * 
                 * @returns {undefined}
                 */
                recipientholder.prototype.showLiveSearchResult = function(response, container_parent_id){
                    var container = $('#' + container_parent_id + '_emailinput_livesearch');
                    container.html(response);
                    
                    $('.liveSearchItem').on('click', function(event){
                        var recipientHolderDiv = event;
                        console.log(recipientHolderDiv);
                    });
                };
                
                /**
                 * @brief Általános ajax hívást megvalósító fv.
                 * 
                 */
                recipientholder.prototype.callAjax = function(){
                    var thispoi = this;
                    $.ajax({
                     url: thispoi.url,
                     method: 'post',
                     async: false,
                     data: thispoi.ajaxData  
                    }).done(function(response){
                     //var responseJSON = $.parseJSON(response);
                     var responseJSON = response;
                     switch(thispoi.ajaxData.action){
                      case 'liveSearch' : {
                       thispoi.showLiveSearchResult(responseJSON, thispoi.ajaxData.elementId);
                       break;
                      }
                      case 'getNews' : {
                       thispoi.doNews(responseJSON);
                       break;
                      }   
                      case 'login' : {
                       thispoi.doLogin(responseJSON);
                       break;
                      }   
                     }
                    }); 
                };
                
               /**
                * Címett adati tárolja le egy tömbben
                * 
                * @param {type} typep  Címzett típusa
                * @param {type} addressp [in] Címzett email címe
                * 
                * @returns {String} A címzett egyedi azonosítója a tömbben
                */
                recipientholder.prototype.additem=function(typep, addressp){
                        var id=guid();

                        this.recipient_arr.push({
                                "id" : id,
                                "type" : typep,
                                "address" : addressp
                        });
                        return id;
                };
                
                /**
                 * uuid alapján visszaadja az elem tárolás pozívióját
                 * 
                 * @param array [in] Tároló tömb
                 * @param uuid [in] Egyedi azonosító
                 * 
                 * @returns A keresett elem indexével.
                 */
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
                *  @see additem();
                *  
                *  @param array [in] A tárolótömb.
                *  @param uuid [in] Az elem egyedi azonosítója.
                */
                recipientholder.prototype.removeitem=function(array, uuid){
                    var index = this.getindex(array, uuid);
                    array.splice(index,1);
                    this.g
                };

				/**
                *  @brief A címzett elem tualjdonságait állatja be a tárolótömbben
                *  
                *  @param array [in] A tárolótömb.
				*  @param type [in] Címzett típusa.
                *  @param uuid [in] Az elem egyedi azonosítója.
				*  @param value [in] Az email cím.
                */
                recipientholder.prototype.setproperty=function(array, type, uuid, value){
                    var index = this.getindex(array, uuid);
                    array[index][type] = value;
                };
                
		/**
                *  @brief Validáló fv.
                */
                recipientholder.prototype.validate = function() {
                    //alert('validálás');
                    
                    /*dtpicker validate*/
                    var dtpicker = $('#'+this.parent.contenthtmltag+'_status_dtpicker').val();
                    if(dtpicker !== ''){
                        //alert('dtpicker validate' + this.parent.currentdate+' '+this.parent.currenttime);
                    }
                };
                
				/**
                *  @brief Beállítja mi volt az utolsó címzettípus
                */
                recipientholder.prototype.setlastrecipienttype = function() {
                    for(var i=0; i<this.recipient_arr.length; i++)
                    {
                        if(i === this.recipient_arr.length-1){
                            this.lastrecipienttype = this.recipient_arr[i]['type'];
                        }
                    }
                };
                
				/**
                *  @brief uuid alapján visszadja, hogy az aktuális elem az utolsó-e
                *  
                *  @param uuid [in] Az elem egyedi azonosítója.
                */
                recipientholder.prototype.islastitem = function(uuid) {
                    var retval = false;
                    
                    var array = this.recipient_arr;
                    var index = this.getindex(array, uuid);
                    if(index === this.recipient_arr.length-1){
                        retval = true;
                    }
                    return retval;
                };
                
				/**
                *  @brief uj cimzett hozzáadása
                */
                recipientholder.prototype.newRecipient = function(){
                    var id = this.additem(this.lastrecipienttype,"");
                    this.show();
                    //$("div").find("[data-id='" + id + "'] input").focus();
					$('div[data-id = ' + id + '] input').focus();
                };
                
				/**
                *  @brief Törlési párbeszédablak
                */
                recipientholder.prototype.getDialogsStr = function(){
                    var cont = '';
                    
                    cont+='<div id="'+this.contenthtmltag+'_recipient_delete_dialog" title="'+this.str_res_obj[g_lang]['recipient_delete_dialog_title']+'">';
                    cont+='<p>' + this.str_res_obj[g_lang]['recipient_delete_dialog'] + '</p>';
                    cont+='</div>';
                    
                    return cont;
                };

                recipientholder.prototype.show=function(){
                        var cont='';
                        cont+='<div id="'+this.contenthtmltag+'_recipientholderdiv" class="recipientholderdiv">';
                        cont+='<button type="button" id="newRecipient" href="#">'+this.str_res_obj[g_lang]['newRecipient']+'</button>';
                        cont+=this.getDialogsStr();
                        cont+='</div>';
                        
                         $('#'+this.contenthtmltag).html(cont);
                        // jquery kontrolok inicializálása
                        for(var i=0; i<this.recipient_arr.length; i++)
                        {
                                if(i===0){
                                    editable = true;
                                    deletable = false;
                                } else {
                                    editable = true;
                                    deletable = true;
                                }
                                var item=this.recipient_arr[i];
                                $('#'+this.contenthtmltag+'_recipientholderdiv').append(this.getrecipient_arrtr(item['id'],editable, deletable));
                                $("div").find("[data-id='" + item['id'] + "'] select").val(item['type']);
                                $("div").find("[data-id='" + item['id'] + "'] input").val(item['address']);
                                this.setlastrecipienttype();
                        }
                        
                        /*ESEMÉNYKEZELÉS*/
                        /*jQuery doesn't redefine the this pointer, but that's how JavaScript functions work in general. Store a reference to the this pointer under a different name, and use that.*/
                        var thispoi = this;
                        /*Új címzett hozzáadása*/
                        $("#newRecipient").click(function(){
                            thispoi.newRecipient();
                        });
                        
                        
                        
                        /*Ha enter-t nyomtunk, nem üres az inputfield és az utolsó emailinputon állunk - új címzettet hozlétre
						* Ha BACKSPACE-t nyomtunk és üres az input - prevent default máskülönben és delete dialog vagy livesearch
						*/
                        $(".emailinput").on('keyup', function(event){
                            /*email input ertekkiaras az recipient_arr tombbe*/
                            var type = 'address';
                            var uuid = $(this).parent().data("id");
                            var value = $(this).val();
                            thispoi.setproperty(thispoi.recipient_arr, type, uuid, value);
                            /*ENTER nyomása és nem üres input esetén új címzett*/
                            var inputvalue = $(this).val();
                            var uuid = $(this).parent().data("id");
                            var lastitem = thispoi.islastitem(uuid);							
							
							if(event.which === 8){
								if(inputvalue === '' && $(this).parent().data('deletable') === true){
									
									$(this).parent().addClass('markedfordelete');
									alert('#'+thispoi.contenthtmltag+'_recipient_delete_dialog');
									$('#'+thispoi.contenthtmltag+'_recipient_delete_dialog').dialog('open');
									/*kivédi, hogy a BACKSPACE hatására visszanavigáljon az előző oldalra*/
									event.defaultPrevented;
								}
							}
							
                            if(event.which === 13 && inputvalue !== '' && lastitem === true){
                                thispoi.newRecipient();
                            } else {
				thispoi.liveSearch(event);
                            }
                        });
						
						
                        
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
                        
                        /*select onchange ertekkiaras az recipient_arr tombbe*/
                        $(".recipienttype").change(function(){
                            var type = 'type';
                            var uuid = $(this).parent().data("id");
                            var value = $(this).val();
                            thispoi.setproperty(thispoi.recipient_arr, type, uuid, value);
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

                        
                        $('.emailinput').on('change', function(){
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
                                                    event.defaultPrevented;
                                                }
                                            });
                                        },
                                    buttons: [
                                        {
                                        text: thispoi.str_res_obj[g_lang]['yes'],
                                        click: function() {
                                                    var target = $(".markedfordelete");
                                                    var uuid = target.data("id");
                                                    thispoi.removeitem(thispoi.recipient_arr, uuid);
                                                    thispoi.setlastrecipienttype();
                                                    target.remove();
                                                    $(this).dialog("close");
                                                    $(".emailinput:last").focus();
                                                }
                                        },
                                        {
                                        text: thispoi.str_res_obj[g_lang]['no'],
                                        click: function() {
                                                    $(".markedfordelete").removeClass("markedfordelete");
                                                    $(this).dialog("close");
                                                }
                                            }
                                    ]
						});
                        
							$('button.deleterecipient').on('click', function() {
									$(this).parent().addClass("markedfordelete");
									$(".emailinput").focus();
									$('#'+thispoi.contenthtmltag+'_recipient_delete_dialog').dialog("open");
									
							});
                        
						});

                        /*ESEMÉNYKEZELÉSVÉGE*/
                        
                        $('.emailinput:last').focus();
                        $('#newRecipient').button();
                        $('.deleterecipient').button({
                            icons: {primary: 'ui-icon-trash'}
                        });
                };

                recipientholder.prototype.getitembyid=function(id){
                        var retitem=null;
                        for(var i=0; i<this.recipient_arr.length; i++)
                        {
                                var item=this.recipient_arr[i];
                                if(item['id'] === id)
                                {
                                        retitem=item;
                                        break;
                                }
                        }	
                        return retitem;
                };

                recipientholder.prototype.getrecipient_arrtr = function(id,editablep,deletablep)
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
                                cont+='<option value="to">'+this.str_res_obj[g_lang]['toAddress']+'</option>';
                                cont+='<option value="cc">'+this.str_res_obj[g_lang]['carbonCopy']+'</option>';
                                cont+='<option value="bcc">'+this.str_res_obj[g_lang]['blindCarbonCopy']+'</option>';
                                cont+='</select>';
                                cont+='<input type="email" id="'+id+'_emailinput" class="emailinput dovalidate ui-widget ui-widget-input ui-corner-all"/>';
                                if(deletablep === true){
                                    cont+='<button class="deleterecipient" type="button" href="#">'+this.str_res_obj[g_lang]['delete']+'</button>';
                                }
                                cont+='<div id="'+id+'_emailinput_info">';
                                cont+='teszt';
                                cont+='</div>';
				cont+='<div id="'+id+'_emailinput_livesearch" class="livesearch_div">';
                                cont+='resppppppp';
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
                        string_resource_obj /*string tömb a megjelenítendő textekhez*/
                ) 
                {
                        this.contenthtmltag = contenthtmltag;
                        this.str_res_obj = string_resource_obj;
                        this.recipient_arr = new Array({
                            "customerName" : 'üres',
                            "lang" : 'hun',
                            "products" : 'sz7',
                            "activity" : 'repair',
                            "status" : 'start',
                            "message": ''
                        });
                        this.recipholder =new recipientholder(this.contenthtmltag+'_recipientholder', string_resource_obj, this);
                        this.recipholder.additem("to", ""); 
                        //this.recipholder.additem("cc", "csernyey.krisztian@3szs.hu"); 
                        this.recipholder.additem("bcc", "zk@3szs.hu"); 				
                        this.dateformat='Y.m.d.';
                        this.timeformat='H:i';
                        this.currentdate='';
                        this.currenttime='';
                }
				
		/**
                *  @brief Cation szövegek kinyerése a string resource objektumból
                *  
                *  @param lang [in] Nyelv.
                *  @param mit [in] Caption.
		*
		*  @return string
                */
		maileruserinterface.prototype.getCaption = function(lang, mit){
			var retval = 'Caption Str';
			retval = this.str_res_obj[lang][mit];
			return retval;
		};
                
                maileruserinterface.prototype.validate = function() {
                    alert('maileruserinterface validálás');
                    
                    /*Name validate*/
                    var customerName = $('#'+this.parent.contenthtmltag+'_customerName').val();
                    if(customerName === ''){
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
                        thispoi.settitle(thispoi.getCaption(g_lang, 'titleStr'));
                        // jquery ui-s kontrolok inicializálása
                        thispoi.initjquerycontrols();
                        thispoi.recipholder.show();
                        
                        $('#'+thispoi.contenthtmltag+'_customerName').on("input", function(){
                            thispoi.recipient_arr[0]["customerName"] = $(this).val();
                        });
                        
                        $('#'+thispoi.contenthtmltag+'_lang').on("input", function(){
                            thispoi.recipient_arr[0]["lang"] = $(this).val();
                            $(".jqte_editor").load('./templates/'+$(this).val()+'_template');
                            thispoi.recipient_arr[0]["message"] = $(".jqte_editor").html();
                        });
                        
                        $('#'+thispoi.contenthtmltag+'_products').on("input", function(){
                            thispoi.recipient_arr[0]["products"] = $(this).val();
                        });
                        
                        $('#'+thispoi.contenthtmltag+'_activity').on("input", function(){
                            thispoi.recipient_arr[0]["activity"] = $(this).val();
                        });
                        
                        $('#'+thispoi.contenthtmltag+'_status').on("input", function(){
                            thispoi.recipient_arr[0]["status"] = $(this).val();
                        });
                        
                        $('#'+thispoi.contenthtmltag+'_status_dtpicker').on("change", function(){
                            thispoi.recipient_arr[0]["datetime"] = $(this).val();
                        });
                        
                        $(".jqte_editor").on("input", function(){
                            thispoi.recipient_arr[0]["message"] = $(this).html();
                        });
                        
                         $('#'+thispoi.contenthtmltag+'_submitbutton').on("click", function() {
                                submitarray.push(rhpoi["recipient_arr"]);
                                submitarray.push(thispoi["recipient_arr"]);
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
                             thispoi.recipient_arr[0]["message"] = $(".jqte_editor").html();
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
                        cont+=this.getcustomerNamedivstr();
                        cont+=this.getlangdivstr();
                        cont+=this.getproductsdivstr();
                        cont+=this.getactivitydivstr();
                        cont+=this.getstatusdivstr();
                        cont+='<div id="'+this.contenthtmltag+'_recipientholder">';
                        cont+='</div>';
                        cont+=this.geteditordivstr();
                        cont+=this.getbuttonsstr('button', this.str_res_obj[g_lang]['submitCaption']);		
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

                maileruserinterface.prototype.getcustomerNamedivstr = function (){
                        var cont='';

                        cont+='<div id="'+this.contenthtmltag+'_customerNamediv">';
                        cont+='<label class="ui-widget" for="'+this.contenthtmltag+'_customerName">'+this.getCaption(g_lang, 'customerName')+'</label>';
                        cont+='<input id="'+this.contenthtmltag+'_customerName" name="'+this.contenthtmltag+'_customerNamep" class="customerName dovalidate ui-widget ui-widget-input ui-corner-all"/>';
                        cont+='</div>';
                        return cont;
                };

                maileruserinterface.prototype.getproductsdivstr = function (){
                        var cont='';

                        cont+='<div id="'+this.contenthtmltag+'_productdiv">';
                        cont+='<label class="ui-widget" for="'+this.contenthtmltag+'_products">'+this.str_res_obj[g_lang]['product']+'</label>';
                        cont+='<select id="'+this.contenthtmltag+'_products" class="product ui-widget ui-widget-input ui-corner-all">';
                        cont+='<option value="sz7">'+this.str_res_obj[g_lang]['sz7']+'</option>';
                        cont+='<option value="mu">'+this.str_res_obj[g_lang]['mu']+'</option>';
                        cont+='<option value="m">'+this.str_res_obj[g_lang]['m']+'</option>';
                        cont+='<option value="qs">'+this.str_res_obj[g_lang]['qs']+'</option>';
                        cont+='</select>';
                        cont+='</div>';
                        return cont;
                };

                maileruserinterface.prototype.getlangdivstr = function (){
                        var cont='';

                        cont+='<div id="'+this.contenthtmltag+'_langdiv">';
                        cont+='<label class="ui-widget" for="'+this.contenthtmltag+'_msgLanguages">'+this.str_res_obj[g_lang]['msgLanguages']+'</label>';
                        cont+='<select id="'+this.contenthtmltag+'_lang" class="product ui-widget ui-widget-input ui-corner-all">';
                        cont+='<option value="hun">'+this.str_res_obj[g_lang]['hun']+'</option>';
                        cont+='<option value="rom">'+this.str_res_obj[g_lang]['rom']+'</option>';
                        cont+='<option value="cze">'+this.str_res_obj[g_lang]['cze']+'</option>';
                        cont+='<option value="sky">'+this.str_res_obj[g_lang]['sky']+'</option>';
                        cont+='</select>';
                        cont+='</div>';
                        return cont;
                };
                /**
                 * @bref info a fvrol
                 * 
                 * @returns {String}
                 */
                maileruserinterface.prototype.getactivitydivstr = function (){
                        var cont='';

                        cont+='<div id="'+this.contenthtmltag+'_activitydiv">';
                        cont+='<label class="ui-widget" for="'+this.contenthtmltag+'_activity">'+this.str_res_obj[g_lang]['activity']+'</label>';
                        cont+='<select id="'+this.contenthtmltag+'_activity" class="activitytype ui-widget ui-widget-input ui-corner-all">';
                        cont+='<option value="repair">'+this.str_res_obj[g_lang]['repair']+'</option>';
                        cont+='<option value="support">'+this.str_res_obj[g_lang]['support']+'</option>';
                        cont+='<option value="installation">'+this.str_res_obj[g_lang]['installation']+'</option>';
                        cont+='</select>';
                        cont+='</div>';
                        return cont;
                };

                maileruserinterface.prototype.getstatusdivstr = function (){
                        var cont='';

                        cont+='<div id="'+this.contenthtmltag+'_statusdiv">';
                        cont+='<label class="ui-widget" for="'+this.contenthtmltag+'_status">'+this.str_res_obj[g_lang]['status']+'</label>';
                        cont+='<select id="'+this.contenthtmltag+'_status" class="statustype ui-widget ui-widget-input ui-corner-all">';
                        cont+='<option value="start">'+this.str_res_obj[g_lang]['start']+'</option>';
                        cont+='<option value="stop">'+this.str_res_obj[g_lang]['stop']+'</option>';
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
                        mui=new maileruserinterface('content', g_string_resource_obj);
                        //var mui1=new maileruserinterface('content1', g_string_resource_obj);				
                        mui.show();
                        mui.settitle("Egyszerű levélküldő");
                        //mui1.show();		
                });
        </script>
			
    </head>
    <body>
        <div id="content">
        </div>
    </body>
	
</html>